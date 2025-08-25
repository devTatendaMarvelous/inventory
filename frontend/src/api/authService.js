import axiosInstance from "./axiosInstance";
import useAxiosErrorInterceptor from "./ErrorInterceptor.jsx";

export const login = async (email, password) => {
    const response = await axiosInstance.post("/login", { email, password });

    if (response.data.success) {
        const resData=response.data.data.original
        localStorage.setItem("token", resData.access_token);
        localStorage.setItem("user", JSON.stringify(resData.user));
        localStorage.setItem("permissions", JSON.stringify(resData.permissions));
    }
    return response.data;
};

export const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("permissions");
};


export const getCurrentUser = () =>  JSON.parse(localStorage.getItem("user"));

export const isAuthenticated = () => localStorage.getItem("token")?localStorage.getItem("token"):null;
