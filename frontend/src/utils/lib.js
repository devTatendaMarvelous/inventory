
export const can = (permission) => {
    const permissions= localStorage.getItem("permissions")?localStorage.getItem("permissions"):[]
    return permissions.includes(permission);
};
