import { useEffect, useState } from "react";
import { get, put } from "../../api/appService.js";
import { useNavigate, useParams } from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const EditStock = () => {
    const navigate = useNavigate();
    const { id } = useParams();

    useAxiosErrorInterceptor();
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const [loadingMessage, setLoadingMessage] = useState("");
    const [categories, setCategories] = useState([]);
    const [errors, setErrors] = useState({});

    const [formData, setFormData] = useState({
        name: "",
        description: "",
        sku: "",
        price: "",
        category_id: "",
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoadingMessage("Loading...");
                setLoading(true);
                const productData = await get(`/products/${id}`);
                setFormData({
                    name: productData.name || "",
                    description: productData.description || "",
                    sku: productData.sku || "",
                    price: productData.price || "",
                    category_id: productData.category?.id || "",
                });
                const categoriesData = await get("/categories");
                setCategories(categoriesData);
            } catch (error) {
                setErrorMessage("Failed to load product or categories.");
            } finally {
                setLoading(false);
            }
        };
        fetchData();
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

        const formDataToSend = new FormData();
        for (const key in formData) {
            formDataToSend.append(key, formData[key]);
        }

        try {
            const data = await put(`/products/${id}`, formDataToSend);
            if (data.success) {
                navigate("/products");
            } else {
                setErrors(data.errors);
                setErrorMessage("There was an issue updating the product.");
            }
        } catch (error) {
            setErrorMessage("Something went wrong while updating.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-4 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-semibold mb-4">Edit Product</h2>
            {loading ? (
                <div className="flex justify-center items-center py-4">
                    <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-blue-950 border-t-transparent" />
                    <h3 className="text-2xl text-blue-950 pl-2 font-bold">{loadingMessage}</h3>
                </div>
            ) : (
                <form onSubmit={handleSubmit}>
                    {errorMessage && (
                        <div className="text-red-500 mb-4">{errorMessage}</div>
                    )}

                    <div className="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.name ? 'border-red-500' : ''}`}
                                required
                            />
                            {errors.name && (
                                <span className="text-sm text-red-500">{errors.name[0]}</span>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Description</label>
                            <input
                                type="text"
                                name="description"
                                value={formData.description}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.description ? 'border-red-500' : ''}`}
                            />
                            {errors.description && (
                                <span className="text-sm text-red-500">{errors.description[0]}</span>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">SKU</label>
                            <input
                                type="text"
                                name="sku"
                                value={formData.sku}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.sku ? 'border-red-500' : ''}`}
                                required
                            />
                            {errors.sku && (
                                <span className="text-sm text-red-500">{errors.sku[0]}</span>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Price</label>
                            <input
                                type="number"
                                name="price"
                                value={formData.price}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.price ? 'border-red-500' : ''}`}
                                required
                                step="0.01"
                            />
                            {errors.price && (
                                <span className="text-sm text-red-500">{errors.price[0]}</span>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Category</label>
                            <select
                                name="category_id"
                                value={formData.category_id}
                                onChange={handleChange}
                                className={`px-4 py-2 border rounded-md w-full ${errors.category_id ? 'border-red-500' : ''}`}
                                required
                            >
                                <option value="">Select Category</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                            {errors.category_id && (
                                <span className="text-sm text-red-500">{errors.category_id[0]}</span>
                            )}
                        </div>
                    </div>

                    {successMessage && (
                        <div className="text-green-500 mb-4">{successMessage}</div>
                    )}

                    <div className="mt-6 flex justify-end">
                        <button
                            type="submit"
                            className="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-700"
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