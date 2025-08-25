const Unauthorized = () => {
    return (
        <div className="flex flex-col items-center justify-center h-screen bg-blue-950 text-white">
            <div className="bg-white text-blue-950 p-8 rounded-2xl shadow-lg text-center">
                <h1 className="text-4xl font-bold mb-4">403 - Unauthorized</h1>
                <p className="text-lg mb-6">You do not have permission to view this page.</p>
                <a href="/" className="bg-blue-950 text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition">
                    Go to Home
                </a>
            </div>
        </div>
    );
};

export default Unauthorized;
