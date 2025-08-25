import {BrowserRouter as Router, Routes, Route, useNavigate,} from "react-router-dom";
import PrivateRoute from "./PrivateRoute.jsx";
import Dashboard from "../pages/Dashboard";
import Login from "../pages/auth/Login.jsx";
import Unauthorized from "../pages/Unauthorized";
import {AuthProvider} from "../context/AuthContext.jsx";

import PrivateLayout from "../layouts/PrivateLayout.jsx";
import {logout} from "../api/authService.js";
import {useEffect} from "react";
import Warehouse from "../pages/Warehouses/Warehouse.jsx";
import AddWarehouse from "../pages/Warehouses/AddWarehouse.jsx";
import EditWarehouse from "../pages/Warehouses/EditWarehouse.jsx";
import NotFound from "../pages/NotFound.jsx";

const AppRoutes = () => {

    const LogoutAndRedirect = () => {
        const navigate = useNavigate();

        useEffect(() => {
            logout();
            navigate("/");
        }, [navigate]);
        return null;
    };

    return (
        <AuthProvider>
            <Router>
                <Routes>
                    <Route path="/login" element={<Login/>}/>
                    <Route path="/unauthorized" element={<Unauthorized/>}/>
                    <Route path="/logout" element={<LogoutAndRedirect/>}/>
                    <Route path="/notfound" element={<NotFound/>}/>
                    <Route element={
                        <PrivateRoute>
                            <PrivateLayout/>
                        </PrivateRoute>}>
                        <Route path="/" element={<Dashboard/>}/>
                        <Route path="/dashboard" element={<Dashboard/>}/>
                        <Route path="/warehouses" element={<Warehouse/>}/>
                        <Route path="/warehouses/create" element={<AddWarehouse/>}/>
                        <Route path="/warehouses/edit/:id" element={<EditWarehouse/>}/>
                    </Route>
                </Routes>
            </Router>
        </AuthProvider>
    );
};

export default AppRoutes;
