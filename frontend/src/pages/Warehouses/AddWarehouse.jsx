import {useEffect, useState} from "react";
import {get, post} from "../../api/appService.js";
import {useNavigate} from "react-router-dom";
import useAxiosErrorInterceptor from "../../api/ErrorInterceptor.jsx";

const AddWarehouse = () => {
    const navigate = useNavigate();
    useAxiosErrorInterceptor();
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const [loadingMessage, setLoadingMessage] = useState("");

    const [formData, setFormData] = useState({
        name: "",
        location: ""
    });

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

        console.log(formDataToSend.get('name'));
        try {
            const data = await post("/warehouses",formDataToSend);
            if (data.success) {
                navigate("/warehouses");
            } else {
                console.log(data.errors);
                setErrors(data.errors);
                setErrorMessage("There was an issue with the form submission.");
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            setErrorMessage("Something went wrong while submitting the form.");
        }finally {
            setLoading(false);
        }
    };


    return (
        <div className="p-4 bg-white shadow-md rounded-lg">
            <h2 className="text-2xl font-semibold mb-4">Add Warehouse</h2>
            {loading ? (
                // Spinner to show while data is loading
                <div className="flex justify-center items-center py-4">
                    <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-blue-950 border-t-transparent" />
                    <h3 className="text-2xl text-blue-950 pl-2 font-bold"> {loadingMessage}</h3>
                </div>
            ) : (
            <form onSubmit={handleSubmit}>
                {errorMessage && (
                    <div className="text-red-500 mb-4">{errorMessage}</div>
                )}

                <div className="grid grid-cols-2 gap-4 mb-4">
                    {/* Form fields here, same as your original form... */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700"> Name</label>
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
                        <label className="block text-sm font-medium text-gray-700">Location</label>
                        <input
                            type="text"
                            name="location"
                            value={formData.location}
                            onChange={handleChange}
                            className={`px-4 py-2 border rounded-md w-full ${errors.location ? 'border-red-500' : ''}`}
                            required
                        />
                        {errors.location && (
                            <span className="text-sm text-red-500">{errors.location[0]}</span>
                        )}
                    </div>
                </div>

                {/* Success or Error message */}
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

export default AddWarehouse;
