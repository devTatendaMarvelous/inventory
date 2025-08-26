import { useEffect, useState } from "react";
import { get, put } from "../../api/appService.js";
import { useNavigate, useParams } from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const EditCategory = () => {
    const navigate = useNavigate();
    const { id } = useParams();

    useAxiosErrorInterceptor();
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const [loadingMessage, setLoadingMessage] = useState("");
    const [formData, setFormData] = useState({
        name: "",
        description: "",
    });

    useEffect(() => {
        const fetchCategory = async () => {
            try {
                setLoadingMessage("Loading...");
                setLoading(true);
                const categoryData = await get(`/categories/${id}`);
                setFormData({
                    name: categoryData.name || "",
                    description: categoryData.description || "",
                });
            } catch (error) {
                setErrorMessage("Failed to load category details.");
            } finally {
                setLoading(false);
            }
        };
        fetchCategory();
    }, [id]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoadingMessage("Updating...");
        setLoading(true);
        setSuccessMessage("");
        setErrorMessage("");

        try {
            const data = await put(`/categories/${id}`, formData);
            if (data.success) {
                navigate("/categories");
            } else {
                setErrorMessage("There was an issue updating the category.");
            }
        } catch (error) {
            setErrorMessage("Something went wrong while updating.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-4 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-semibold mb-4">Edit Category</h2>
            {loading ? (
                <div className="flex justify-center items-center py-4">
                    <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-blue-950 border-t-transparent" />
                    <h3 className="text-2xl text-blue-950 pl-2 font-bold">{loadingMessage}</h3>
                </div>
            ) : (
                <form onSubmit={handleSubmit}>
                    {errorMessage && <div className="text-red-500 mb-4">{errorMessage}</div>}
                    <div className="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-md w-full"
                                required
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Description</label>
                            <input
                                type="text"
                                name="description"
                                value={formData.description}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-md w-full"
                                required
                            />
                        </div>
                    </div>
                    {successMessage && <div className="text-green-500">{successMessage}</div>}
                    <button
                        type="submit"
                        className="bg-blue-500 text-white px-4 py-2 rounded-md"
                        disabled={loading}
                    >
                        {loading ? "Updating..." : "Update Stock"}
                    </button>
                </form>
            )}
        </div>
    );
};

export default EditCategory;