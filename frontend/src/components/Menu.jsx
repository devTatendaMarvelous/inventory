import {can} from "../utils/lib.js";

const menuItems = [
    {
        title: "MENU",
        items: [
            {
                icon: "/assets/images/home.png",
                label: "Home",
                href: "/",
                permission: "Access "
            },
            {
                icon: "/assets/images/teacher.png",
                label: "Warehouses",
                href: "/warehouses",
                permission: "View Warehouses"
            },
            {
                icon: "/assets/images/teacher.png",
                label: "Categories",
                href: "/categories",
                permission: "View Categories"
            },

        ],
    },
    {
        title: "Settings",
        items: [
            // {
            //     icon: "/assets/images/profile.png",
            //     label: "Profile",
            //     href: "/profile",
            //     permission: "Access "
            // },
            // {
            //     icon: "/assets/images/setting.png",
            //     label: "Settings",
            //     href: "/assets/images/settings",
            //     permission: "Access "
            // },
            // {
            //     icon: "/assets/images/logout.png",
            //     label: "Logout",
            //     href: "/logout",
            // },
        ],
    },
];

const Menu = () => {

    return (
        <div className="mt-4 text-sm ">
            {menuItems.map(i => (

                <div key={i.title} className="flex flex-col gap-2">
                    <span className="hidden lg:block text-white font-light my-4">{i.title}</span>
                    {
                        i.items.map(item => (
                            can(item.permission) && (
                                <a href={item.href} key={item.label}
                                   className="flex items-center justify-center lg:justify-start gap-4 text-white  py-2 ">
                                    <img src={item.icon} alt="" width={20} height={20}/>
                                    <span className="hidden lg:block">{item.label}</span>
                                </a>
                            )
                        ))}
                </div>
            ))}
            <a href="/logout"
               className="flex items-center justify-center lg:justify-start gap-4 text-white  py-2 ">
                <img src="/assets/images/logout.png" alt="" width={20} height={20}/>
                <span className="hidden lg:block">Logout</span>
            </a>
        </div>
    )
}
export default Menu;