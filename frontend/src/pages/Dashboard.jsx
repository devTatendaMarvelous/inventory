import Table from "../components/Table.jsx";
import useAxiosErrorInterceptor from "../api/ErrorInterceptor.jsx";

const Dashboard = () => {
    useAxiosErrorInterceptor();
    const columns = [
        { key: "id", label: "ID" },
        { key: "name", label: "Name" },
        { key: "email", label: "Email" },
        { key: "role", label: "Role" },
    ];

    // Sample Data
    const data = [
        { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
        { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
        { id: 3, name: "Michael Brown", email: "michael@example.com", role: "Editor" },
        { id: 4, name: "Sarah Wilson", email: "sarah@example.com", role: "User" },
        { id: 5, name: "David Johnson", email: "david@example.com", role: "Admin" },
        { id: 6, name: "Emily White", email: "emily@example.com", role: "User" },
    ];

    // Handle Edit Action
    const handleEdit = (row) => {
        alert(`Editing ${row.name}`);
    };

    // Handle Delete Action
    const handleDelete = (row) => {
        alert(`Deleting ${row.name}`);
    };
    return (
        <>
                <div className="flex items-center justify-start p-4">
                    <h1 className="text-2xl font-semibold mb-4">Dashboard</h1>
                </div>
            {/*<Table columns={columns} data={data} onEdit={handleEdit} onDelete={handleDelete} />*/}
        </>
    );
};

export default Dashboard;
