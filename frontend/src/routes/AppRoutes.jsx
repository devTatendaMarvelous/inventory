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
import Category from "../pages/Categories/Category.jsx";
import AddCategory from "../pages/Categories/AddCategory.jsx";
import EditCategory from "../pages/Categories/EditCategory.jsx";

import Product from "../pages/Products/Product.jsx";
import AddProduct from "../pages/Products/AddProduct.jsx";
import EditProduct from "../pages/Products/EditProduct.jsx";
import Stock from "../pages/Stocks/Stock.jsx";
import AddStock from "../pages/Stocks/AddStock.jsx";
import EditStock from "../pages/Stocks/EditStock.jsx";


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
                        <Route path="/categories" element={<Category/>}/>
                        <Route path="/categories/create" element={<AddCategory/>}/>
                        <Route path="/categories/edit/:id" element={<EditCategory/>}/>
                        <Route path="/products" element={<Product/>}/>
                        <Route path="/products/create" element={<AddProduct/>}/>
                        <Route path="/products/edit/:id" element={<EditProduct/>}/>
                        <Route path="/stocks" element={<Stock/>}/>
                        <Route path="/stocks/create" element={<AddStock/>}/>
                        <Route path="/stocks/edit/:id" element={<EditStock/>}/>
                    </Route>
                </Routes>
            </Router>
        </AuthProvider>
    );
};

export default AppRoutes;
