import React, { useState } from 'react';
import { useNotification } from '../components/shared/Notification';
import ViewOrderModal from '../components/ViewOrderModal';
import EditOrderModal from '../components/EditOrderModal';
import CreateOrderModal from '../components/CreateOrderModal';

const mockOrders = [
    { id: 1, tenphieu: 'Đơn hàng TQ #001', money: 15000000, lock: false, completed: true, created_at: '2024-12-20' },
    { id: 2, tenphieu: 'Đơn hàng TQ #002', money: 8500000, lock: true, completed: false, created_at: '2024-12-21' },
    { id: 3, tenphieu: 'Đơn hàng TQ #003', money: 25000000, lock: false, completed: false, created_at: '2024-12-22' },
];

const Orders: React.FC = () => {
    const notification = useNotification();
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [dayInput, setDayInput] = useState('');
    const [monthInput, setMonthInput] = useState('');
    const [yearInput, setYearInput] = useState('');
    const [viewingOrder, setViewingOrder] = useState<number | null>(null);
    const [editingOrder, setEditingOrder] = useState<number | null>(null);
    const [showCreateModal, setShowCreateModal] = useState(false);

    const filteredOrders = mockOrders.filter(order => {
        const matchesSearch = order.tenphieu.toLowerCase().includes(search.toLowerCase());
        const matchesStatus = !statusFilter ||
            (statusFilter === 'completed' && order.completed) ||
            (statusFilter === 'pending' && !order.completed) ||
            (statusFilter === 'locked' && order.lock);

        // Filter by date components - filter immediately as user types in any field
        const orderDate = new Date(order.created_at);
        const orderDay = orderDate.getDate();
        const orderMonth = orderDate.getMonth() + 1;
        const orderYear = orderDate.getFullYear();

        const matchesDay = !dayInput || String(orderDay).padStart(2, '0').startsWith(dayInput.padStart(2, '0'));
        const matchesMonth = !monthInput || String(orderMonth).padStart(2, '0').startsWith(monthInput.padStart(2, '0'));
        const matchesYear = !yearInput || String(orderYear).startsWith(yearInput);

        return matchesSearch && matchesStatus && matchesDay && matchesMonth && matchesYear;
    });

    const handleView = (id: number) => {
        setViewingOrder(id);
    };

    const handleExportPDF = (id: number) => {
        notification.success(`Đã xuất PDF đơn hàng #${id}`);
    };

    const handleExportExcel = (id: number) => {
        notification.success(`Đã xuất Excel đơn hàng #${id}`);
    };

    const handleEdit = (id: number) => {
        setEditingOrder(id);
    };

    return (
        <div className="space-y-6">
            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Đơn hàng</h1>
                    <p className="text-slate-500 text-sm md:text-base">Quản lý đơn đặt hàng từ Trung Quốc</p>
                </div>
                <button
                    onClick={() => setShowCreateModal(true)}
                    className="flex items-center justify-center gap-2 px-4 md:px-6 py-2 md:py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-primary/20 w-full sm:w-auto"
                >
                    <span className="material-symbols-outlined text-xl">add</span>
                    Tạo đơn hàng
                </button>
            </div>

            {/* Filters */}
            <div className="card">
                <div className="flex flex-col lg:flex-row gap-4">
                    <input
                        type="text"
                        placeholder="Tìm kiếm đơn hàng..."
                        className="input flex-1"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                    <div className="flex flex-col sm:flex-row gap-4 items-center">
                        <select
                            className="input w-full sm:w-48"
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="locked">Đã khóa</option>
                        </select>

                        {/* Date filter - 3 separate inputs like date picker */}
                        <div className="flex items-center justify-center gap-1 w-full sm:w-auto">
                            <input
                                type="text"
                                className="input w-12 md:w-16 text-center px-1"
                                placeholder="DD"
                                maxLength={2}
                                value={dayInput}
                                onChange={(e) => {
                                    const val = e.target.value.replace(/\D/g, '').slice(0, 2);
                                    setDayInput(val);
                                }}
                            />
                            <span className="text-slate-400">/</span>
                            <input
                                type="text"
                                className="input w-12 md:w-16 text-center px-1"
                                placeholder="MM"
                                maxLength={2}
                                value={monthInput}
                                onChange={(e) => {
                                    const val = e.target.value.replace(/\D/g, '').slice(0, 2);
                                    setMonthInput(val);
                                }}
                            />
                            <span className="text-slate-400">/</span>
                            <input
                                type="text"
                                className="input w-16 md:w-20 text-center px-1"
                                placeholder="YYYY"
                                maxLength={4}
                                value={yearInput}
                                onChange={(e) => {
                                    const val = e.target.value.replace(/\D/g, '').slice(0, 4);
                                    setYearInput(val);
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Orders Table */}
            <div className="card p-0 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="admin-table w-full min-w-[700px]">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên phiếu</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th className="text-right px-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredOrders.map((order) => (
                                <tr key={order.id}>
                                    <td className="font-mono text-slate-800">#{order.id}</td>
                                    <td className="font-medium text-slate-800">{order.tenphieu}</td>
                                    <td className="text-slate-600">{new Intl.NumberFormat('vi-VN').format(order.money)}đ</td>
                                    <td>
                                        <div className="flex items-center gap-2">
                                            {order.completed ? (
                                                <span className="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Hoàn thành</span>
                                            ) : (
                                                <span className="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Đang xử lý</span>
                                            )}
                                            {order.lock && (
                                                <span className="material-symbols-outlined text-sm text-slate-500">lock</span>
                                            )}
                                        </div>
                                    </td>
                                    <td className="text-slate-600">{order.created_at}</td>
                                    <td className="text-right px-4">
                                        <div className="flex items-center justify-end gap-1">
                                            <button
                                                onClick={() => handleView(order.id)}
                                                className="p-1.5 text-slate-400 hover:text-blue-600 transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Chi tiết"
                                            >
                                                <span className="material-symbols-outlined text-lg">visibility</span>
                                            </button>
                                            <button
                                                onClick={() => handleExportPDF(order.id)}
                                                className="p-1.5 text-slate-400 hover:text-green-600 transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Xuất PDF"
                                            >
                                                <span className="material-symbols-outlined text-lg">picture_as_pdf</span>
                                            </button>
                                            <button
                                                onClick={() => handleExportExcel(order.id)}
                                                className="p-1.5 text-slate-400 hover:text-yellow-600 transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Xuất Excel"
                                            >
                                                <span className="material-symbols-outlined text-lg">table_view</span>
                                            </button>
                                            <button
                                                onClick={() => handleEdit(order.id)}
                                                className="p-1.5 text-slate-400 hover:text-primary transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Sửa"
                                            >
                                                <span className="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {viewingOrder && (
                <ViewOrderModal
                    orderId={viewingOrder}
                    onClose={() => setViewingOrder(null)}
                />
            )}
            {editingOrder && (
                <EditOrderModal
                    orderId={editingOrder}
                    onClose={() => setEditingOrder(null)}
                />
            )}
            {showCreateModal && (
                <CreateOrderModal
                    onClose={() => setShowCreateModal(false)}
                />
            )}
        </div>
    );
};

export default Orders;
