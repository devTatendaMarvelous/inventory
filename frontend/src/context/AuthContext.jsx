import { createContext, useState, useEffect } from "react";
import {getCurrentUser, isAuthenticated, logout} from "../api/authService";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState();

    useEffect(() => {
        if (isAuthenticated()) {
            setUser(getCurrentUser());
        }
    }, []);

    const handleLogout = () => {
        logout();
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, setUser, handleLogout }}>
            {children}
        </AuthContext.Provider>
    );
};

export default AuthContext;