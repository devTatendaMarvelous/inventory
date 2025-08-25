import {del, get} from "../../api/appService.js";
import {useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const Warehouse = () => {
    const [warehouses, setWarehouses] = useState([]);
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate();
    useAxiosErrorInterceptor();
    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoading(true);
                const data = await get("/warehouses");
                setWarehouses(data);
                // console.log(data);
            } catch (error) {
                console.error("Data fetching failed:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);


    const create = () => {
        navigate("/warehouses/create");
    }


    const onEdit = (warehouse) => {
        navigate(`/warehouses/edit/${warehouse.id}`);
    }
    const onDelete = async(warehouse) => {
        try {
            const response = await del(`/warehouses/${warehouse.id}`)
  
            if (response.success) {

            }
        }catch (error) {
            console.error(error);
        }
    }
    return (
        <div className="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <div className="flex items-center justify-start p-4">
                <h1 className="text-2xl font-semibold mb-4">Warehouses</h1>
            </div>
            <div className="mb-4 flex items-center justify-between p-4">
                <div></div>
                <button
                    className="bg-green-500 text-white px-3 py-1 rounded-md text- hover:bg-green-700"
                    onClick={create}
                >
                    Add Warehouse
                </button>
            </div>

            {loading ? (
                // Spinner to show while data is loading
                <div className="flex justify-center items-center py-4">
                    <div
                        className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full
                        border-blue-950 border-t-transparent"/>
                    <h3 className="text-2xl text-blue-950 pl-2 font-bold"> Loading...</h3>
                </div>
            ) : (
                <table className="min-w-full border border-gray-200 rounded-lg">
                    <thead>
                    <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer
                        border-b">
                            Name
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer
                        border-b">
                            Location
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    {warehouses.map((warehouse) => (
                        <tr key={warehouse.id} className="hover:bg-gray-100">
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{warehouse.name}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{warehouse.location}</td>
                            <td className="px-6 py-4 border-b flex gap-2">
                                <button
                                    className="bg-blue-500 text-white px-3 py- rounded-md text-xs hover:bg-blue-700"
                                    onClick={() => onEdit(warehouse)}
                                >
                                    Edit
                                </button>
                                <button
                                    className="bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-700"
                                    onClick={() => onDelete(warehouse)}
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}

        </div>
    );
};

export default Warehouse;
