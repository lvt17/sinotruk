import React, { useState, useRef, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { updateProfile } from '../lib/supabase';

interface ProfileModalProps {
    onClose: () => void;
    currentName: string;
    currentAvatar: string;
    onSave: (name: string, avatar: string) => void;
}

const ProfileModal: React.FC<ProfileModalProps> = ({ onClose, currentName, currentAvatar, onSave }) => {
    const [name, setName] = useState(currentName);
    const [avatar, setAvatar] = useState(currentAvatar);
    const [isUploading, setIsUploading] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        document.body.classList.add('modal-open');
        return () => document.body.classList.remove('modal-open');
    }, []);

    const handleAvatarChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('Ảnh quá lớn. Vui lòng chọn ảnh dưới 5MB.');
            return;
        }

        setIsUploading(true);

        try {
            const reader = new FileReader();
            const base64Promise = new Promise<string>((resolve, reject) => {
                reader.onload = () => resolve(reader.result as string);
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });

            const base64Image = await base64Promise;

            // Try Cloudinary upload
            try {
                const response = await fetch('/api/upload-avatar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image: base64Image }),
                });

                if (response.ok) {
                    const result = await response.json();
                    setAvatar(result.secure_url);
                } else {
                    setAvatar(base64Image);
                }
            } catch {
                setAvatar(base64Image);
            }
        } catch (error) {
            console.error('Upload error:', error);
        } finally {
            setIsUploading(false);
        }
    };

    const handleSave = async () => {
        setIsSaving(true);

        try {
            const userId = localStorage.getItem('userId');

            if (userId) {
                // Save to Supabase
                await updateProfile(parseInt(userId), {
                    full_name: name.trim() || 'Admin',
                    avatar: avatar || null
                });
            }

            // Also update localStorage for immediate UI update
            onSave(name.trim() || 'Admin', avatar);
            onClose();
        } catch (error) {
            console.error('Save error:', error);
            // Still update localStorage even if Supabase fails
            onSave(name.trim() || 'Admin', avatar);
            onClose();
        } finally {
            setIsSaving(false);
        }
    };

    return ReactDOM.createPortal(
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" onClick={onClose}>
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4" onClick={e => e.stopPropagation()}>
                {/* Header */}
                <div className="p-5 border-b border-slate-200 flex items-center justify-between">
                    <h2 className="text-lg font-bold text-slate-800">Chỉnh sửa hồ sơ</h2>
                    <button onClick={onClose} className="p-1.5 text-slate-400 hover:text-slate-600 transition-colors rounded-lg hover:bg-slate-100">
                        <span className="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                {/* Content */}
                <div className="p-5 space-y-5">
                    {/* Avatar */}
                    <div className="flex flex-col items-center gap-3">
                        <div
                            onClick={() => !isUploading && fileInputRef.current?.click()}
                            className={`w-20 h-20 rounded-full bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all overflow-hidden ${isUploading ? 'opacity-50' : ''}`}
                        >
                            {isUploading ? (
                                <span className="material-symbols-outlined text-primary text-2xl animate-spin">progress_activity</span>
                            ) : avatar ? (
                                <img src={avatar} alt="Avatar" className="w-full h-full object-cover" />
                            ) : (
                                <span className="material-symbols-outlined text-slate-400 text-3xl">add_a_photo</span>
                            )}
                        </div>
                        <button
                            onClick={() => !isUploading && fileInputRef.current?.click()}
                            disabled={isUploading}
                            className="text-sm text-primary font-medium hover:underline disabled:opacity-50"
                        >
                            {isUploading ? 'Đang tải...' : avatar ? 'Đổi ảnh' : 'Tải ảnh lên'}
                        </button>
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            onChange={handleAvatarChange}
                            className="hidden"
                        />
                    </div>

                    {/* Name */}
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">Tên hiển thị</label>
                        <input
                            type="text"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder="Nhập tên..."
                            className="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                    </div>
                </div>

                {/* Actions */}
                <div className="p-5 border-t border-slate-200 flex gap-3">
                    <button
                        onClick={onClose}
                        disabled={isSaving}
                        className="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors font-medium disabled:opacity-50"
                    >
                        Hủy
                    </button>
                    <button
                        onClick={handleSave}
                        disabled={isUploading || isSaving}
                        className="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors font-medium disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        {isSaving ? (
                            <>
                                <span className="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                                Đang lưu...
                            </>
                        ) : (
                            'Lưu'
                        )}
                    </button>
                </div>
            </div>
        </div>,
        document.body
    );
};

export default ProfileModal;
