import React, { useState } from 'react';
import { useNotification } from '../components/shared/Notification';
import CreateImportModal from '../components/CreateImportModal';
import * as XLSX from 'xlsx';

// Mock imports data
const mockImports = [
    { id: 1, tenphieu: 'NK-001', supplier: 'Nhà cung cấp A', money: 20000000, date: '2024-12-20', status: 'Hoàn thành' },
    { id: 2, tenphieu: 'NK-002', supplier: 'Nhà cung cấp B', money: 15000000, date: '2024-12-21', status: 'Đang xử lý' },
    { id: 3, tenphieu: 'NK-003', supplier: 'Nhà cung cấp C', money: 30000000, date: '2024-12-22', status: 'Chờ duyệt' },
];

const Imports: React.FC = () => {
    const notification = useNotification();
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [showCreateModal, setShowCreateModal] = useState(false);

    const filteredImports = mockImports.filter(i => {
        const matchesSearch = i.tenphieu.toLowerCase().includes(search.toLowerCase()) ||
            i.supplier.toLowerCase().includes(search.toLowerCase());
        const matchesStatus = !statusFilter ||
            (statusFilter === 'completed' && i.status === 'Hoàn thành') ||
            (statusFilter === 'processing' && i.status === 'Đang xử lý') ||
            (statusFilter === 'pending' && i.status === 'Chờ duyệt');
        return matchesSearch && matchesStatus;
    });

    const handleExportExcel = () => {
        const ws = XLSX.utils.json_to_sheet(filteredImports);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Nhập kho');
        XLSX.writeFile(wb, `nhap-kho-${new Date().toISOString().split('T')[0]}.xlsx`);
        notification.success('Đã xuất file Excel thành công');
    };

    const handleView = (id: number) => {
        notification.info(`Xem chi tiết phiếu nhập #${id}`);
    };

    const handleExportPDF = (id: number) => {
        notification.success(`Đã xuất PDF phiếu nhập #${id}`);
    };

    const handleEdit = (id: number) => {
        notification.info(`Sửa phiếu nhập #${id}`);
    };

    return (
        <div className="space-y-6">
            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Nhập kho</h1>
                    <p className="text-slate-500 text-sm md:text-base">Quản lý phiếu nhập kho</p>
                </div>
                <button
                    onClick={() => setShowCreateModal(true)}
                    className="flex items-center justify-center gap-2 px-4 md:px-6 py-2 md:py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-primary/20 w-full sm:w-auto"
                >
                    <span className="material-symbols-outlined text-xl">add</span>
                    Tạo phiếu nhập
                </button>
            </div>

            {/* Filters */}
            <div className="card">
                <div className="flex flex-col lg:flex-row gap-4">
                    <div className="flex-1">
                        <input
                            type="text"
                            placeholder="Tìm kiếm theo mã phiếu, nhà cung cấp..."
                            className="input w-full"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                    <div className="flex flex-col sm:flex-row gap-4">
                        <select
                            className="input w-full sm:w-48"
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="pending">Chờ duyệt</option>
                        </select>
                        <button
                            onClick={handleExportExcel}
                            className="btn btn-secondary flex items-center justify-center gap-2 w-full sm:w-auto"
                        >
                            <span className="material-symbols-outlined">download</span>
                            Xuất Excel
                        </button>
                    </div>
                </div>
            </div>

            {/* Imports Table */}
            <div className="card p-0 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="admin-table w-full min-w-[700px]">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Nhà cung cấp</th>
                                <th>Tổng tiền</th>
                                <th>Ngày nhập</th>
                                <th>Trạng thái</th>
                                <th className="text-right px-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredImports.map((importItem) => (
                                <tr key={importItem.id}>
                                    <td className="font-mono text-slate-800">{importItem.tenphieu}</td>
                                    <td className="font-medium text-slate-800">{importItem.supplier}</td>
                                    <td className="text-slate-600">{new Intl.NumberFormat('vi-VN').format(importItem.money)}đ</td>
                                    <td className="text-slate-600">{importItem.date}</td>
                                    <td>
                                        <span className={`px-3 py-1 rounded-full text-xs font-bold ${importItem.status === 'Hoàn thành' ? 'bg-green-100 text-green-700' :
                                            importItem.status === 'Đang xử lý' ? 'bg-yellow-100 text-yellow-700' :
                                                'bg-slate-100 text-slate-600'
                                            }`}>
                                            {importItem.status}
                                        </span>
                                    </td>
                                    <td className="text-right px-4">
                                        <div className="flex items-center justify-end gap-1">
                                            <button
                                                onClick={() => handleView(importItem.id)}
                                                className="p-1.5 text-slate-400 hover:text-blue-600 transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Chi tiết"
                                            >
                                                <span className="material-symbols-outlined text-lg">visibility</span>
                                            </button>
                                            <button
                                                onClick={() => handleExportPDF(importItem.id)}
                                                className="p-1.5 text-slate-400 hover:text-green-600 transition-colors bg-slate-50 rounded-lg border border-slate-100"
                                                title="Xuất PDF"
                                            >
                                                <span className="material-symbols-outlined text-lg">picture_as_pdf</span>
                                            </button>
                                            <button
                                                onClick={() => handleEdit(importItem.id)}
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

            {/* Info */}
            <div className="flex items-center justify-between">
                <p className="text-slate-500 text-sm">
                    Hiển thị {filteredImports.length} / {mockImports.length} phiếu nhập
                </p>
            </div>

            {showCreateModal && <CreateImportModal onClose={() => setShowCreateModal(false)} />}
        </div>
    );
};

export default Imports;

