import React, { useState, useEffect } from 'react';
import { useNotification } from '../components/shared/Notification';
import { categoryService, Category } from '../services/supabase';

interface CategoryWithExtras extends Category {
    is_vehicle_name?: boolean;
    code?: string;
    thumbnail?: string;
    is_visible?: boolean;
}

const Categories: React.FC = () => {
    const notification = useNotification();
    const [categories, setCategories] = useState<CategoryWithExtras[]>([]);
    const [isAdding, setIsAdding] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [_loading, setLoading] = useState(true);

    // New category form
    const [newForm, setNewForm] = useState({
        name: '',
        code: '',
        is_vehicle_name: false,
        is_visible: true
    });

    // Edit form
    const [editForm, setEditForm] = useState({
        name: '',
        code: '',
        is_vehicle_name: false,
        is_visible: true
    });

    const loadCategories = async () => {
        setLoading(true);
        try {
            const data = await categoryService.getAll();
            setCategories(data);
        } catch (err) {
            console.error('Error loading categories:', err);
            notification.error('Không thể tải danh mục');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadCategories();
    }, []);

    const handleAddCategory = async () => {
        if (!newForm.name.trim()) {
            notification.warning('Vui lòng nhập tên danh mục');
            return;
        }

        try {
            await categoryService.create({
                name: newForm.name.toUpperCase(),
                code: newForm.code.toUpperCase() || undefined,
                is_vehicle_name: newForm.is_vehicle_name,
                is_visible: newForm.is_visible
            });
            notification.success(`Đã thêm danh mục "${newForm.name}"`);
            setNewForm({ name: '', code: '', is_vehicle_name: false, is_visible: true });
            setIsAdding(false);
            loadCategories();
        } catch (error: any) {
            notification.error(error.message || 'Có lỗi xảy ra');
        }
    };

    const handleEdit = (cat: CategoryWithExtras) => {
        setEditingId(cat.id);
        setEditForm({
            name: cat.name,
            code: cat.code || '',
            is_vehicle_name: cat.is_vehicle_name || false,
            is_visible: cat.is_visible !== false
        });
    };

    const handleSaveEdit = async () => {
        if (!editForm.name.trim() || !editingId) return;

        try {
            await categoryService.update(editingId, {
                name: editForm.name.toUpperCase(),
                code: editForm.code.toUpperCase() || undefined,
                is_vehicle_name: editForm.is_vehicle_name,
                is_visible: editForm.is_visible
            });
            notification.success('Đã cập nhật danh mục');
            setEditingId(null);
            loadCategories();
        } catch (error: any) {
            notification.error(error.message || 'Có lỗi xảy ra');
        }
    };

    const handleToggleVisibility = async (cat: CategoryWithExtras) => {
        try {
            await categoryService.update(cat.id, { is_visible: cat.is_visible === false });
            notification.success(cat.is_visible === false ? 'Đã hiển thị danh mục' : 'Đã ẩn danh mục');
            loadCategories();
        } catch (error: any) {
            notification.error(error.message || 'Có lỗi xảy ra');
        }
    };

    const handleDeleteCategory = async (category: CategoryWithExtras) => {
        if (confirm(`Bạn có chắc muốn xóa danh mục "${category.name}"?`)) {
            try {
                await categoryService.delete(category.id);
                notification.success(`Đã xóa danh mục "${category.name}"`);
                loadCategories();
            } catch (error: any) {
                notification.error(error.message || 'Có lỗi xảy ra');
            }
        }
    };

    const vehicleCategories = categories.filter(c => c.is_vehicle_name);
    const partCategories = categories.filter(c => !c.is_vehicle_name);

    const renderCategoryCard = (cat: CategoryWithExtras, isVehicle: boolean) => (
        <div key={cat.id} className={`flex items-center gap-3 px-4 py-3 rounded-xl border ${cat.is_visible === false ? 'bg-slate-100 border-slate-200 opacity-60' :
                isVehicle ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200'
            }`}>
            {editingId === cat.id ? (
                <div className="flex flex-wrap items-center gap-2 w-full">
                    <input
                        type="text"
                        placeholder="Tên"
                        value={editForm.name}
                        onChange={(e) => setEditForm({ ...editForm, name: e.target.value })}
                        className="input py-1.5 px-2 text-sm w-32"
                    />
                    <input
                        type="text"
                        placeholder="Mã"
                        value={editForm.code}
                        onChange={(e) => setEditForm({ ...editForm, code: e.target.value })}
                        className="input py-1.5 px-2 text-sm w-20"
                    />
                    <label className="flex items-center gap-1 text-xs">
                        <input type="checkbox" checked={editForm.is_vehicle_name} onChange={(e) => setEditForm({ ...editForm, is_vehicle_name: e.target.checked })} />
                        Xe
                    </label>
                    <label className="flex items-center gap-1 text-xs">
                        <input type="checkbox" checked={editForm.is_visible} onChange={(e) => setEditForm({ ...editForm, is_visible: e.target.checked })} />
                        Hiện
                    </label>
                    <button onClick={handleSaveEdit} className="text-green-600 hover:bg-green-100 p-1.5 rounded-lg">
                        <span className="material-symbols-outlined text-sm">check</span>
                    </button>
                    <button onClick={() => setEditingId(null)} className="text-slate-400 hover:bg-slate-200 p-1.5 rounded-lg">
                        <span className="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            ) : (
                <>
                    <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2">
                            <span className={`font-medium ${isVehicle ? 'text-blue-700' : 'text-slate-700'}`}>{cat.name}</span>
                            {cat.code && (
                                <span className="text-xs bg-white px-1.5 py-0.5 rounded border border-slate-200 text-slate-500 font-mono">{cat.code}</span>
                            )}
                            {cat.is_visible === false && (
                                <span className="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Ẩn</span>
                            )}
                        </div>
                    </div>
                    <div className="flex items-center gap-1">
                        <button
                            onClick={() => handleToggleVisibility(cat)}
                            className={`p-1.5 rounded-lg transition-colors ${cat.is_visible === false ? 'text-green-600 hover:bg-green-100' : 'text-slate-400 hover:bg-slate-200'}`}
                            title={cat.is_visible === false ? 'Hiển thị' : 'Ẩn'}
                        >
                            <span className="material-symbols-outlined text-sm">{cat.is_visible === false ? 'visibility' : 'visibility_off'}</span>
                        </button>
                        <button onClick={() => handleEdit(cat)} className="text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg">
                            <span className="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <button onClick={() => handleDeleteCategory(cat)} className="text-slate-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg">
                            <span className="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </>
            )}
        </div>
    );

    return (
        <div className="space-y-6">
            {/* Page Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight uppercase">Quản lý danh mục</h1>
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
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Tên danh mục *</label>
                            <input
                                type="text"
                                placeholder="VD: ĐỘNG CƠ hoặc HOWO"
                                value={newForm.name}
                                onChange={(e) => setNewForm({ ...newForm, name: e.target.value })}
                                className="input w-full"
                                autoFocus
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Mã danh mục</label>
                            <input
                                type="text"
                                placeholder="VD: DC, HOWO (tùy chọn)"
                                value={newForm.code}
                                onChange={(e) => setNewForm({ ...newForm, code: e.target.value })}
                                className="input w-full"
                            />
                        </div>
                    </div>
                    <div className="flex flex-wrap gap-4 mb-4">
                        <label className="flex items-center gap-2 cursor-pointer p-3 bg-blue-50 rounded-xl border border-blue-200 hover:border-blue-400 transition-colors">
                            <input
                                type="checkbox"
                                checked={newForm.is_vehicle_name}
                                onChange={(e) => setNewForm({ ...newForm, is_vehicle_name: e.target.checked })}
                                className="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
                            />
                            <span className="font-medium text-blue-700 text-sm">Đây là HÃNG XE</span>
                        </label>
                        <label className="flex items-center gap-2 cursor-pointer p-3 bg-green-50 rounded-xl border border-green-200 hover:border-green-400 transition-colors">
                            <input
                                type="checkbox"
                                checked={newForm.is_visible}
                                onChange={(e) => setNewForm({ ...newForm, is_visible: e.target.checked })}
                                className="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500"
                            />
                            <span className="font-medium text-green-700 text-sm">Hiển thị trên website</span>
                        </label>
                    </div>
                    <div className="flex gap-2">
                        <button
                            onClick={handleAddCategory}
                            className="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-colors"
                        >
                            Lưu
                        </button>
                        <button
                            onClick={() => { setIsAdding(false); setNewForm({ name: '', code: '', is_vehicle_name: false, is_visible: true }); }}
                            className="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition-colors"
                        >
                            Hủy
                        </button>
                    </div>
                </div>
            )}

            {/* Vehicle Categories */}
            <div className="card">
                <div className="flex items-center gap-2 mb-4">
                    <span className="material-symbols-outlined text-primary">local_shipping</span>
                    <h3 className="font-bold text-slate-800">Loại xe ({vehicleCategories.length})</h3>
                </div>
                {vehicleCategories.length > 0 ? (
                    <div className="grid gap-2">
                        {vehicleCategories.map(cat => renderCategoryCard(cat, true))}
                    </div>
                ) : (
                    <p className="text-slate-400 text-sm">Chưa có loại xe nào</p>
                )}
            </div>

            {/* Part Categories */}
            <div className="card">
                <div className="flex items-center gap-2 mb-4">
                    <span className="material-symbols-outlined text-primary">category</span>
                    <h3 className="font-bold text-slate-800">Bộ phận ({partCategories.length})</h3>
                </div>
                {partCategories.length > 0 ? (
                    <div className="grid gap-2">
                        {partCategories.map(cat => renderCategoryCard(cat, false))}
                    </div>
                ) : (
                    <p className="text-slate-400 text-sm">Chưa có bộ phận nào</p>
                )}
            </div>

            {/* Info */}
            <div className="flex items-center gap-2 px-2 text-slate-500 text-sm">
                <span className="material-symbols-outlined text-lg">info</span>
                <span>Tổng cộng <span className="font-bold text-slate-800">{categories.length}</span> danh mục.</span>
            </div>
        </div>
    );
};

export default Categories;
