// import img from "next/img";

import {getCurrentUser} from "../api/authService.js";

const Navbar=()=>{
    const user = getCurrentUser();
    return (
        <div className="flex items-center justify-between p-4 bg-white ">
            <div className=" "></div>
            <div className="flex items-center gap-6 justify-end w-full" >
                <div className="flex flex-col">
                    <span className="text-xs leading-3 font-medium">{user?.first_name+' '+user?.last_name}</span>
                    <span className="text-[10px] text-gray-500 text-right">{user?.role}</span>
                </div>
                <img src="/assets/images/avatar.png" alt=''  width={36} height={36} className="rounded-full"/>
            </div>

        </div>
    )
}
export default Navbar;