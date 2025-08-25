import  { useState } from "react";

const Table = ({ columns, data, pageSize = 5, onEdit, onDelete }) => {
    const [sortColumn, setSortColumn] = useState(null);
    const [sortOrder, setSortOrder] = useState("asc");
    const [currentPage, setCurrentPage] = useState(1);

    // Sorting function
    const handleSort = (columnKey) => {
        if (sortColumn === columnKey) {
            setSortOrder(sortOrder === "asc" ? "desc" : "asc");
        } else {
            setSortColumn(columnKey);
            setSortOrder("asc");
        }
    };

    // Sort data based on selected column
    const sortedData = [...data].sort((a, b) => {
        if (!sortColumn) return 0;
        const valueA = a[sortColumn];
        const valueB = b[sortColumn];
        if (typeof valueA === "string") return sortOrder === "asc" ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
        return sortOrder === "asc" ? valueA - valueB : valueB - valueA;
    });

    // Pagination logic
    const totalPages = Math.ceil(sortedData.length / pageSize);
    const paginatedData = sortedData.slice((currentPage - 1) * pageSize, currentPage * pageSize);

    return (
        <div className="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <table className="min-w-full border border-gray-200 rounded-lg">
                <thead>
                <tr>
                    {columns.map((column) => (
                        <th
                            key={column.key}
                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer border-b"
                            onClick={() => handleSort(column.key)}
                        >
                            {column.label}
                            {sortColumn === column.key && (sortOrder === "asc" ? " 🔼" : " 🔽")}
                        </th>
                    ))}
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Actions</th>
                </tr>
                </thead>
                <tbody>
                {paginatedData.map((row, index) => (
                    <tr key={index} className="hover:bg-gray-100">
                        {columns.map((column) => (
                            <td key={column.key} className="px-6 py-4 text-sm text-gray-700 border-b">
                                {row[column.key]}
                            </td>
                        ))}
                        <td className="px-6 py-4 border-b flex gap-2">
                            <button
                                className="bg-blue-500 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700"
                                onClick={() => onEdit(row)}
                            >
                                Edit
                            </button>
                            <button
                                className="bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-700"
                                onClick={() => onDelete(row)}
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>

            {/* Pagination */}
            <div className="flex justify-between items-center mt-4">
                <button
                    className={`px-4 py-2 text-sm ${currentPage === 1 ? "text-gray-400" : "text-blue-500"} hover:underline`}
                    disabled={currentPage === 1}
                    onClick={() => setCurrentPage(currentPage - 1)}
                >
                    Previous
                </button>
                <span className="text-gray-700">
          Page {currentPage} of {totalPages}
        </span>
                <button
                    className={`px-4 py-2 text-sm ${currentPage === totalPages ? "text-gray-400" : "text-blue-500"} hover:underline`}
                    disabled={currentPage === totalPages}
                    onClick={() => setCurrentPage(currentPage + 1)}
                >
                    Next
                </button>
            </div>
        </div>
    );
};

export default Table;
