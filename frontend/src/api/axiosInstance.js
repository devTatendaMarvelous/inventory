import axios from "axios";
import useAxiosErrorInterceptor from "./ErrorInterceptor.jsx";

// Your base API URL
const API_URL = "http://127.0.0.1:8000/api/v1";

const axiosInstance = axios.create({
    baseURL: API_URL,
    headers: {
        "Content-Type": "application/json",
    },
});
// eslint-disable-next-line react-hooks/rules-of-hooks

// Automatically attach JWT token
axiosInstance.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default axiosInstance;
