import { useEffect, useState } from "react";
import { get, post } from "../../api/appService.js";
import { useNavigate } from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const AddProduct = () => {
    const navigate = useNavigate();
    useAxiosErrorInterceptor();
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const [loadingMessage, setLoadingMessage] = useState("");
    const [categories, setCategories] = useState([]);

    const [formData, setFormData] = useState({
        name: "",
        description: "",
        sku: "",
        price: "",
        category_id: "",
    });

    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const data = await get("/categories");
                setCategories(data);
            } catch (error) {
                setErrorMessage("Failed to load categories.");
            }
        };
        fetchCategories();
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const formDataToSend = new FormData();
        setLoadingMessage("Saving...");
        setLoading(true);
        setSuccessMessage("");
        setErrorMessage("");

        for (const key in formData) {
            formDataToSend.append(key, formData[key]);
        }

        try {
            const data = await post("/products", formDataToSend);
            if (data.success) {
                navigate("/products");
            } else {
                setErrors(data.errors);
                setErrorMessage("There was an issue with the form submission.");
            }
        } catch (error) {
            setErrorMessage("Something went wrong while submitting the form.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-4 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-semibold mb-4">Add Product</h2>
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
                            Submit
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
};

export default AddProduct;