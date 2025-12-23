import React, { useState, useEffect } from 'react';
import { useNotification } from '../components/shared/Notification';
import { getCategories, addCategory, deleteCategory, Category } from '../data/mockDatabase';

const Categories: React.FC = () => {
    const notification = useNotification();
    const [categories, setCategories] = useState<Category[]>([]);
    const [newCategoryName, setNewCategoryName] = useState('');
    const [isAdding, setIsAdding] = useState(false);

    // Load categories on mount
    useEffect(() => {
        setCategories(getCategories());
    }, []);

    const handleAddCategory = () => {
        if (!newCategoryName.trim()) {
            notification.warning('Vui lòng nhập tên danh mục');
            return;
        }

        try {
            addCategory(newCategoryName);
            notification.success(`Đã thêm danh mục "${newCategoryName}" thành công`);
            setNewCategoryName('');
            setIsAdding(false);
            // Reload page to refresh sidebar
            window.location.reload();
        } catch (error: any) {
            notification.error(error.message || 'Có lỗi xảy ra');
        }
    };

    const handleDeleteCategory = (category: Category) => {
        if (category.id === 'ALL') {
            notification.error('Không thể xóa danh mục "Tất cả"');
            return;
        }

        if (confirm(`Bạn có chắc muốn xóa danh mục "${category.label}"?`)) {
            try {
                deleteCategory(category.id);
                notification.success(`Đã xóa danh mục "${category.label}"`);
                // Reload to refresh sidebar
                window.location.reload();
            } catch (error: any) {
                notification.error(error.message || 'Có lỗi xảy ra');
            }
        }
    };

    return (
        <div className="space-y-6">
            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight uppercase">Quản lý danh mục</h1>
                    <p className="text-slate-500 text-sm md:text-base italic">Tạo và quản lý các danh mục sản phẩm</p>
                </div>
                <button
                    onClick={() => setIsAdding(true)}
                    className="flex items-center justify-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-primary/20"
                >
                    <span className="material-symbols-outlined text-xl">add</span>
                    Thêm danh mục
                </button>
            </div>

            {/* Add Category Form */}
            {isAdding && (
                <div className="card">
                    <h3 className="font-bold text-slate-800 mb-4">Thêm danh mục mới</h3>
                    <div className="flex flex-col sm:flex-row gap-4">
                        <input
                            type="text"
                            placeholder="Nhập tên danh mục (ví dụ: HỆ THỐNG ĐIỆN)"
                            value={newCategoryName}
                            onChange={(e) => setNewCategoryName(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleAddCategory()}
                            className="input flex-1"
                            autoFocus
                        />
                        <div className="flex gap-2">
                            <button
                                onClick={handleAddCategory}
                                className="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-colors"
                            >
                                Lưu
                            </button>
                            <button
                                onClick={() => {
                                    setIsAdding(false);
                                    setNewCategoryName('');
                                }}
                                className="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition-colors"
                            >
                                Hủy
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Categories List */}
            <div className="card p-0 overflow-hidden">
                <table className="admin-table w-full">
                    <thead>
                        <tr className="bg-slate-50">
                            <th className="w-16 text-center">#</th>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th className="text-right px-6">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        {categories.map((category, index) => (
                            <tr key={category.id} className="hover:bg-slate-50/80 transition-colors">
                                <td className="text-center text-slate-500">{index + 1}</td>
                                <td>
                                    <span className="font-mono text-sm font-bold text-primary bg-primary/5 px-2 py-1 rounded border border-primary/10">
                                        {category.id}
                                    </span>
                                </td>
                                <td className="font-semibold text-slate-800">{category.label}</td>
                                <td className="text-right px-6">
                                    {category.id !== 'ALL' ? (
                                        <button
                                            onClick={() => handleDeleteCategory(category)}
                                            className="p-2 text-slate-400 hover:text-red-500 transition-colors bg-white rounded-xl border border-slate-100 shadow-sm hover:border-red-200"
                                            title="Xóa danh mục"
                                        >
                                            <span className="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    ) : (
                                        <span className="text-slate-300 text-xs italic">Mặc định</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Info */}
            <div className="flex items-center gap-2 px-2 text-slate-500 text-sm">
                <span className="material-symbols-outlined text-lg">info</span>
                <span>Tổng cộng <span className="font-bold text-slate-800">{categories.length}</span> danh mục. Danh mục mới sẽ xuất hiện trong menu sidebar sau khi tải lại trang.</span>
            </div>
        </div>
    );
};

export default Categories;
