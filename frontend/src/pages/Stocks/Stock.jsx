import { del, get, post } from "../../api/appService.js";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const Stock = () => {
    const [movements, setMovements] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [modalNotes, setModalNotes] = useState("");
    const [selectedId, setSelectedId] = useState(null);
    const navigate = useNavigate();
    useAxiosErrorInterceptor();
    const fetchData = async () => {
        try {
            setLoading(true);
            const data = await get("/stocks");
            console.log(data);
            setMovements(data);
        } catch (error) {
            console.error("Data fetching failed:", error);
        } finally {
            setLoading(false);
        }
    };
    useEffect(() => {

        fetchData();
    }, []);

    const create = () => {
        navigate("/stocks/create");
    };

    const onEdit = (movement) => {
        navigate(`/stocks/edit/${movement.id}`);
    };

    const onDelete = async (movement) => {
        try {
            const response = await del(`/stocks/${movement.id}`);
            if (response.success) {
                // Optionally refresh list or show message
            }
        } catch (error) {
            console.error(error);
        }
    };

    const onApprove = async (id) => {
        try {
            await post(`/stocks/${id}/validation`, { status: "APPROVED" });
            // Optionally refresh list or show message
            fetchData();
        } catch (error) {
            console.error(error);
        }
    };

    const onReject = (id) => {
        setSelectedId(id);
        setShowModal(true);

    };

    const handleModalSubmit = async () => {
        try {
            await post(`/stocks/${selectedId}/validation`, { status: "REJECTED", notes: modalNotes });
            setShowModal(false);
            setModalNotes("");
            setSelectedId(null);
            // Optionally refresh list or show message
            fetchData();
        } catch (error) {
            console.error(error);
        }
    };

    return (
        <div className="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <div className="flex items-center justify-start p-4">
                <h1 className="text-2xl font-semibold mb-4">Stock Movements</h1>
            </div>
            <div className="mb-4 flex items-center justify-between p-4">
                <div></div>
                <button
                    className="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-700"
                    onClick={create}
                >
                    Add Movement
                </button>
            </div>

            {loading ? (
                <div className="flex justify-center items-center py-4">
                    <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-blue-950 border-t-transparent" />
                    <h3 className="text-2xl text-blue-950 pl-2 font-bold"> Loading...</h3>
                </div>
            ) : (
                <table className="min-w-full border border-gray-200 rounded-lg">
                    <thead>
                    <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Product</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Warehouse</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Type</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Qty In</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Qty Out</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Unit Price</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Status</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Notes</th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {movements.map((m) => (
                        <tr key={m.id} className="hover:bg-gray-100">
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.product?.name}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.warehouse}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.movement_type}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.quantity_in}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.quantity_out}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.unit_price}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.status}</td>
                            <td className="px-6 py-4 text-sm text-gray-700 border-b">{m.notes}</td>
                            <td className="px-6 py-4 border-b flex gap-2">
                                {m.status === "PENDING" && (
                                    <>
                                <button
                                    className="bg-blue-500 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700"
                                    onClick={() => onEdit(m)}
                                >
                                    Edit
                                </button>
                                <button
                                    className="bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-700"
                                    onClick={() => onDelete(m)}
                                >
                                    Delete
                                </button>
                                <button
                                    className="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-800"
                                    onClick={() => onApprove(m.id)}
                                >
                                    Approve
                                </button>
                                <button
                                    className="bg-yellow-600 text-white px-3 py-1 rounded-md text-xs hover:bg-yellow-800"
                                    onClick={() => onReject(m.id)}
                                >
                                    Reject
                                </button>
                                </>
                            )
                                }
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}

            {showModal && (
                <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50">
                    <div className="bg-white p-6 rounded shadow-lg w-96">
                        <h2 className="text-lg font-semibold mb-4">Reject Movement</h2>
                        <label className="block mb-2 text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            className="w-full border rounded-md p-2 mb-4"
                            rows={3}
                            value={modalNotes}
                            onChange={e => setModalNotes(e.target.value)}
                            required
                        />
                        <div className="flex justify-end gap-2">
                            <button
                                className="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-600"
                                onClick={() => { setShowModal(false); setModalNotes(""); setSelectedId(null); }}
                            >
                                Cancel
                            </button>
                            <button
                                className="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-800"
                                onClick={handleModalSubmit}
                                disabled={!modalNotes.trim()}
                            >
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Stock;