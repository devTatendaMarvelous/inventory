import { useNavigate } from "react-router-dom";
import { useEffect } from "react";

const withAuth = (WrappedComponent) => {
    return (props) => {
        const navigate = useNavigate();
        const token = localStorage.getItem("token");

        useEffect(() => {
            if (!token) {
                navigate("/login");
            }
        }, [token, navigate]);

        return token ? <WrappedComponent {...props} /> : null;
    };
};

export default withAuth;
