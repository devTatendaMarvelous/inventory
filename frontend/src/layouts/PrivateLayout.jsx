import { Outlet } from "react-router-dom";
import Navbar from "../components/Navbar.jsx";
import Menu from "../components/Menu.jsx";

const PrivateLayout = () => {
    return (
        <div className='h-screen flex bg-blue-950'>
            {/* LEFT MENU */}
            <div className='w-[14%] md:w-[8%] lg:w-[16%] xl:w-[14%] p-4'>
                <a href='/' className='flex items-center justify-center lg:justify-start gap-2'>
                    <img src="/vite.svg" alt="logo" width={32} height={32} />
                    <h3 className="hidden lg:block text-white font-bold">Inventory App</h3>
                </a>
                <Menu />
            </div>

            {/* RIGHT CONTENT */}
            <div className='w-[86%] md:w-[82%] lg:w-[84%] xl:w-[86%] bg-[#F7F8FA] overflow-scroll'>
                <Navbar />
                <div className="p-4">
                    <Outlet /> {/* This will render the dashboard/profile component */}
                </div>
            </div>
        </div>
    );
};

export default PrivateLayout;
