import { useEffect, useState } from "react";
import { get, put } from "../../api/appService.js";
import { useNavigate, useParams } from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const movementTypes = ["IN", "OUT", "TRANSFER"];

const EditStock = () => {
    const navigate = useNavigate();
    const { id } = useParams();
    useAxiosErrorInterceptor();

    const [products, setProducts] = useState([]);
    const [warehouses, setWarehouses] = useState([]);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const [loadingMessage, setLoadingMessage] = useState("");

    const [form, setForm] = useState({
        product_id: "",
        warehouse_id: "",
        movement_type: "IN",
        unit_price: "",
        quantity_in: "",
        initiated_by: 1,
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoadingMessage("Loading...");
                setLoading(true);
                const stockData = await get(`/stocks/${id}`);

                setForm({
                    product_id: stockData.product?.id|| "",
                    warehouse_id: stockData.warehouse?.id||"",
                    movement_type: stockData.movement_type || "IN",
                    unit_price: stockData.unit_price || "",
                    quantity_in: stockData.quantity_in || "",
                    quantity_out: stockData.quantity_out || "",
                });
                setProducts(await get("/products"));
                setWarehouses(await get("/warehouses"));
            } catch {
                setErrorMessage("Failed to load stock, products, or warehouses.");
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [id]);

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoadingMessage("Updating...");
        setLoading(true);
        setSuccessMessage("");
        setErrorMessage("");
        setErrors({});
        try {
            const data = await put(`/stocks/${id}`, form);
            if (data.success) {
                setSuccessMessage("Stock updated successfully.");
                navigate("/stocks");
            } else {
                setErrors(data.errors || {});
                setErrorMessage("There was an issue updating the stock.");
            }
        } catch {
            setErrorMessage("Something went wrong while updating the stock.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-4 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-semibold mb-4">Edit Stock</h2>
            {loading ? (
                <div className="flex justify-center items-center py-4">
                    <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-green-950 border-t-transparent" />
                    <h3 className="text-2xl text-green-950 pl-2 font-bold">{loadingMessage}</h3>
                </div>
            ) : (
                <form onSubmit={handleSubmit}>
                    {errorMessage && (
                        <div className="text-red-500 mb-4">{errorMessage}</div>
                    )}
                    <div className="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Product</label>
                            <select
                                name="product_id"
                                value={form.product_id}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.product_id ? 'border-red-500' : ''}`}
                                required
                            >
                                <option value="">Select Product</option>
                                {products.map(p => (
                                    <option key={p.id} value={String(p.id)} >{p.name}</option>
                                ))}
                            </select>
                            {errors.product_id && (
                                <span className="text-sm text-red-500">{errors.product_id[0]}</span>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Warehouse</label>
                            <select
                                name="warehouse_id"
                                value={form.warehouse_id}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.warehouse_id ? 'border-red-500' : ''}`}
                                required
                            >
                                <option value="">Select Warehouse</option>
                                {warehouses.map(w => (
                                    <option key={w.id} value={String(w.id)}>{w.name}</option>
                                ))}
                            </select>
                            {errors.warehouse_id && (
                                <span className="text-sm text-red-500">{errors.warehouse_id[0]}</span>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Movement Type</label>
                            <select
                                name="movement_type"
                                value={form.movement_type}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.movement_type ? 'border-red-500' : ''}`}
                                required
                            >
                                {movementTypes.map(type => (
                                    <option key={type} value={type}>{type}</option>
                                ))}
                            </select>
                            {errors.movement_type && (
                                <span className="text-sm text-red-500">{errors.movement_type[0]}</span>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Unit Price</label>
                            <input
                                type="number"
                                name="unit_price"
                                value={form.unit_price}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.unit_price ? 'border-red-500' : ''}`}
                                required
                                step="0.01"
                            />
                            {errors.unit_price && (
                                <span className="text-sm text-red-500">{errors.unit_price[0]}</span>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Quantity In</label>
                            <input
                                type="number"
                                name="quantity_in"
                                value={form.quantity_in}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.quantity_in ? 'border-red-500' : ''}`}
                                required
                            />
                            {errors.quantity_in && (
                                <span className="text-sm text-red-500">{errors.quantity_in[0]}</span>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Quantity Out</label>
                            <input
                                type="number"
                                name="quantity_out"
                                value={form.quantity_out}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.quantity_out ? 'border-red-500' : ''}`}
                                required
                            />
                            {errors.quantity_out && (
                                <span className="text-sm text-red-500">{errors.quantity_out[0]}</span>
                            )}
                        </div>
                    </div>
                    {successMessage && (
                        <div className="text-green-500 mb-4">{successMessage}</div>
                    )}
                    <div className="mt-6 flex justify-end">
                        <button
                            type="submit"
                            className="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-700"
                            disabled={loading}
                        >
                            Update
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
};

export default EditStock;