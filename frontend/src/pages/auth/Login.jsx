import { useState, useContext } from "react";
import { useNavigate } from "react-router-dom";
import { login } from "../../api/authService.js";
import AuthContext from "../../context/AuthContext.jsx";

const Login = () => {

    const navigate = useNavigate();
    const { user } = useContext(AuthContext);

    const [loading, setLoading] = useState(false); // State for loading
    const [loginFailed, setLoginFailed] = useState(false); // State for loading
    console.log(user);
    if (user) {
        navigate("/dashboard");
    }
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const { setUser } = useContext(AuthContext);

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoading(true); // Show loader when request starts
        try {
            const data = await login(email, password);
            setUser(data.user);
            // alert(data.message);

            navigate("/dashboard");
        } catch (error) {
            setLoginFailed(true);
        }finally {
            setLoading(false); // Hide loader when request completes
        }
    };

    return (
        <div className="flex h-screen items-center justify-center bg-gray-100">
            <div className="w-full max-w-md p-6 bg-white shadow-lg rounded-xl ">
                <div className="flex items-center justify-center pb-3">
                <img src="/vite.svg" alt="logo" width={210} height={210} />
                </div>
                <h2 className="text-2xl font-bold text-center mb-4">Inventory Management System</h2>
                <h2 className="text-2xl font-semibold text-center mb-4">Login</h2>
                {loading ? (
                    // Spinner to show while data is loading
                    <div className="flex justify-center items-center py-4">
                        <div className="spinner-border animate-spin inline-block w-8 h-8 border-4 border-solid rounded-full border-blue-950 border-t-transparent" />
                        <h3 className="text-2xl text-blue-950 pl-2 font-bold"> Logging You In...</h3>
                    </div>
                ) : (
                <form onSubmit={handleLogin} className="space-y-4">
                    {loginFailed ? (
                        <p className="text-red-500">Login failed please check your credentials </p>
                    ):''}
                    <input
                        type="email"
                        placeholder="Email"
                        onChange={(e) => setEmail(e.target.value)}
                        className="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900"

                    />
                    <input
                        type="password"
                        placeholder="Password"
                        onChange={(e) => setPassword(e.target.value)}
                        className="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-900"
                    />
                    <button className="w-full bg-blue-900 text-white p-3 rounded-lg hover:bg-blue-950">
                        Login
                    </button>
                </form>
                )}
            </div>
        </div>
    );
};

export default Login;
