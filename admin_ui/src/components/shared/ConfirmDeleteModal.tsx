import React from 'react';
import ReactDOM from 'react-dom';

interface ConfirmDeleteModalProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: () => void;
    title?: string;
    message?: string;
    itemName?: string;
}

const ConfirmDeleteModal: React.FC<ConfirmDeleteModalProps> = ({
    isOpen,
    onClose,
    onConfirm,
    title = 'Xác nhận xóa',
    message = 'Bạn có chắc chắn muốn xóa?',
    itemName
}) => {
    if (!isOpen) return null;

    const handleConfirm = () => {
        onConfirm();
        onClose();
    };

    return ReactDOM.createPortal(
        <div
            className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
            onClick={onClose}
        >
            <div
                className="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-scale-in"
                onClick={(e) => e.stopPropagation()}
            >
                {/* Header */}
                <div className="p-6 text-center">
                    <div className="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <span className="material-symbols-outlined text-4xl text-red-500">delete_forever</span>
                    </div>
                    <h3 className="text-xl font-bold text-slate-800 mb-2">{title}</h3>
                    <p className="text-slate-500">
                        {message}
                        {itemName && (
                            <span className="block mt-2 font-semibold text-slate-700">"{itemName}"</span>
                        )}
                    </p>
                </div>

                {/* Actions */}
                <div className="flex border-t border-slate-100">
                    <button
                        onClick={onClose}
                        className="flex-1 py-4 text-slate-600 font-medium hover:bg-slate-50 transition-colors"
                    >
                        Hủy
                    </button>
                    <button
                        onClick={handleConfirm}
                        className="flex-1 py-4 text-red-500 font-bold hover:bg-red-50 transition-colors border-l border-slate-100"
                    >
                        Xóa
                    </button>
                </div>
            </div>
        </div>,
        document.body
    );
};

export default ConfirmDeleteModal;
