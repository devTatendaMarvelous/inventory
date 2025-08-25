import axiosInstance from "./axiosInstance";

export const get = async (endpoint,data = null) => {
    try {

        // Make GET request with token and optional params
        const response = await axiosInstance.get(endpoint, {
            params: data
        });
        if (response.data.success) {
            return response.data.data;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
        throw error;
    }
};
export const post = async (endpoint,data = null) => {
    try {
        // Send formData directly as the request body
        const response = await axiosInstance.post(endpoint, data);
        // console.log(response.data);
        return response.data;
    } catch (error) {
        console.error("Error fetching data:", error);
        throw error;
    }
};
export const put = async (endpoint,data = null) => {
    try {
        // Send formData directly as the request body
        const response = await axiosInstance.put(endpoint, data);
        // console.log(response.data);
        return response.data;
    } catch (error) {
        console.error("Error fetching data:", error);
        throw error;
    }
};

export const del = async (endpoint) => {
    try {
        // Send formData directly as the request body
        const response = await axiosInstance.delete(endpoint);
        return response.data;
    } catch (error) {
        console.error("Error fetching data:", error);
        throw error;
    }
};
