import { useEffect, useState } from "react";
import { get } from "../api/appService.js";
import {
    BarChart, Bar, XAxis, YAxis, Tooltip, Legend, PieChart, Pie, Cell, ResponsiveContainer
} from "recharts";

const COLORS = ["#0088FE", "#00C49F", "#FFBB28", "#FF8042"];

const Dashboard = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [warehouses, setWarehouses] = useState([]);
    const [filters, setFilters] = useState({
        start_date: "",
        end_date: "",
        product_id: "",
        warehouse_id: ""
    });

    useEffect(() => {
        // Fetch products and warehouses for dropdowns
        const fetchDropdowns = async () => {
            try {
                const wh = await get("/warehouses");
                setWarehouses(wh || []);
            } catch {
                setProducts([]);
                setWarehouses([]);
            }
        };
        fetchDropdowns();
    }, []);

    useEffect(() => {
        const fetchReports = async () => {
            setLoading(true);
            let query = [];
            Object.entries(filters).forEach(([k, v]) => {
                if (v) query.push(`${k}=${encodeURIComponent(v)}`);
            });
            const url = `/reports${query.length ? "?" + query.join("&") : ""}`;
            try {
                const res = await get(url);
                setData(res || []);
            } catch {
                setData([]);
            } finally {
                setLoading(false);
            }
        };
        fetchReports();
    }, [filters]);

    // Flatten for charts
    const flat = data.flatMap(r =>
        r.stock_levels.map(s => ({
            warehouse: r.warehouse,
            product: s.product,
            quantity_in: s.quantity_in,
            quantity_out: s.quantity_out,
            balance: s.balance
        }))
    );

    // Pie chart data: total balance per product
    const pieData = Object.values(
        flat.reduce((acc, cur) => {
            acc[cur.product] = acc[cur.product] || { name: cur.product, value: 0 };
            acc[cur.product].value += cur.balance;
            return acc;
        }, {})
    );

    return (
        <div className="p-6 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-bold mb-4">Stock Dashboard</h2>
            {/* Filters */}
            <div className="flex gap-4 mb-6">
                <input type="date" value={filters.start_date}
                    onChange={e => setFilters(f => ({ ...f, start_date: e.target.value }))}
                    className="border px-2 py-1 rounded" placeholder="Start Date" />
                <input type="date" value={filters.end_date}
                    onChange={e => setFilters(f => ({ ...f, end_date: e.target.value }))}
                    className="border px-2 py-1 rounded" placeholder="End Date" />
          
                <select
                    value={filters.warehouse_id}
                    onChange={e => setFilters(f => ({ ...f, warehouse_id: e.target.value }))}
                    className="border px-2 py-1 rounded"
                >
                    <option value="">Select Warehouse</option>
                    {warehouses.map(w => (
                        <option key={w.id} value={w.id}>{w.name}</option>
                    ))}
                </select>
            </div>
            {loading ? <div>Loading...</div> : (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {/* Bar Chart: Product balances per warehouse */}
                    <div>
                        <h3 className="font-semibold mb-2">Product Balances by Warehouse</h3>
                        <ResponsiveContainer width="100%" height={300}>
                            <BarChart data={flat}>
                                <XAxis dataKey="product" />
                                <YAxis />
                                <Tooltip />
                                <Legend />
                                <Bar dataKey="balance" fill="#0088FE" />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                    {/* Pie Chart: Proportion of total stock per product */}
                    <div>
                        <h3 className="font-semibold mb-2">Stock Distribution by Product</h3>
                        <ResponsiveContainer width="100%" height={300}>
                            <PieChart>
                                <Pie
                                    data={pieData}
                                    dataKey="value"
                                    nameKey="name"
                                    cx="50%"
                                    cy="50%"
                                    outerRadius={100}
                                    label
                                >
                                    {pieData.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                    ))}
                                </Pie>
                                <Tooltip />
                            </PieChart>
                        </ResponsiveContainer>
                    </div>
                    {/* Stacked Bar: Quantity In vs Out per product */}
                    <div>
                        <h3 className="font-semibold mb-2">Quantity In vs Out</h3>
                        <ResponsiveContainer width="100%" height={300}>
                            <BarChart data={flat}>
                                <XAxis dataKey="product" />
                                <YAxis />
                                <Tooltip />
                                <Legend />
                                <Bar dataKey="quantity_in" stackId="a" fill="#00C49F" />
                                <Bar dataKey="quantity_out" stackId="a" fill="#FF8042" />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Dashboard;