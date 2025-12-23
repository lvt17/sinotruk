import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { useNotification } from './shared/Notification';
import { productService, categoryService, Category, Product } from '../services/supabase';

interface EditProductModalProps {
    product: Product;
    onClose: () => void;
    onSave?: () => void;
}

const EditProductModal: React.FC<EditProductModalProps> = ({ product, onClose, onSave }) => {
    const notification = useNotification();
    const [categories, setCategories] = useState<Category[]>([]);
    const [formData, setFormData] = useState({
        code: product.code || '',
        name: product.name || '',
        price: product.price || 0,
        price_bulk: product.price_bulk || 0,
        total: product.total || 0,
        category_id: product.category_id || 0,
        description: product.description || '',
        image: product.image || '',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        const loadCategories = async () => {
            try {
                const data = await categoryService.getAll();
                setCategories(data);
            } catch (err) {
                console.error('Error loading categories:', err);
            }
        };
        loadCategories();
    }, []);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!formData.name || !formData.code) {
            notification.error('Vui lòng điền đầy đủ thông tin');
            return;
        }

        setIsSubmitting(true);

        try {
            await productService.update(product.id, {
                code: formData.code,
                name: formData.name,
                price: formData.price,
                price_bulk: formData.price_bulk,
                total: formData.total,
                category_id: formData.category_id || null,
                description: formData.description,
                image: formData.image || null,
            });

            notification.success('Đã cập nhật sản phẩm thành công!');

            if (onSave) {
                onSave();
            }

            onClose();
        } catch (error: any) {
            console.error('Error updating product:', error);
            notification.error('Không thể cập nhật: ' + (error.message || 'Lỗi không xác định'));
        } finally {
            setIsSubmitting(false);
        }
    };

    return ReactDOM.createPortal(
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" onClick={onClose}>
            <div className="bg-white rounded-3xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
                <div className="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h2 className="text-2xl font-bold text-slate-800">Sửa sản phẩm</h2>
                    <button onClick={onClose} className="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                        <span className="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Mã sản phẩm <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                required
                                value={formData.code}
                                onChange={(e) => setFormData({ ...formData, code: e.target.value.toUpperCase() })}
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Danh mục
                            </label>
                            <select
                                value={formData.category_id}
                                onChange={(e) => setFormData({ ...formData, category_id: Number(e.target.value) })}
                                className="input"
                            >
                                <option value={0}>Chọn danh mục</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>{cat.name}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">
                            Tên sản phẩm <span className="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            required
                            value={formData.name}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            className="input"
                        />
                    </div>

                    <div className="grid grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Giá lẻ (VNĐ)</label>
                            <input
                                type="number"
                                min="0"
                                value={formData.price}
                                onChange={(e) => setFormData({ ...formData, price: Number(e.target.value) })}
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Giá sỉ (VNĐ)</label>
                            <input
                                type="number"
                                min="0"
                                value={formData.price_bulk}
                                onChange={(e) => setFormData({ ...formData, price_bulk: Number(e.target.value) })}
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-2">Tồn kho</label>
                            <input
                                type="number"
                                min="0"
                                value={formData.total}
                                onChange={(e) => setFormData({ ...formData, total: Number(e.target.value) })}
                                className="input"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">Mô tả</label>
                        <textarea
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            className="input min-h-[80px]"
                        />
                    </div>

                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-6 py-2.5 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors font-medium"
                            disabled={isSubmitting}
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors font-medium disabled:opacity-50 flex items-center gap-2"
                        >
                            {isSubmitting ? (
                                <>
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                    Đang lưu...
                                </>
                            ) : (
                                'Lưu thay đổi'
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </div>,
        document.body
    );
};

export default EditProductModal;
