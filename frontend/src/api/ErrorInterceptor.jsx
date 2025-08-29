import { useNavigate } from 'react-router-dom';
import axiosInstance from './axiosInstance';
import {useEffect} from "react"; // Assuming you have the axios instance

const useAxiosErrorInterceptor = () => {
    const navigate = useNavigate();

    // Set up the Axios response interceptor to handle errors
    useEffect(() => {
        const interceptor = axiosInstance.interceptors.response.use(
            (response) => response, // Pass successful responses through
            (error) => {
                if (error.response) {
                    const status = error.response.status;
                    // Handle errors and navigate based on status code
                    if (status === 401) {
                        navigate("/logout");
                    } else if (status === 403) {
                        navigate("/unauthorized");
                    } else if (status === 404) {
                        navigate("/notFound");
                    }
                }
                return Promise.reject(error); // Reject the error to propagate it
            }
        );
        // Cleanup the interceptor when the component is unmounted
        return () => {
            axiosInstance.interceptors.response.eject(interceptor);
        };
    }, [navigate]);
};

export default useAxiosErrorInterceptor;
