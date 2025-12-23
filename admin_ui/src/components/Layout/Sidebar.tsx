import React, { useState, useEffect, useRef } from 'react';
import { NavLink, useLocation, useNavigate } from 'react-router-dom';
import { getCategories } from '../../data/mockDatabase';

interface Category {
    id: string;
    label: string;
}

const ADMIN_NAME_KEY = 'sinotruk_admin_name';
const ADMIN_AVATAR_KEY = 'sinotruk_admin_avatar';

const Sidebar: React.FC<{ isOpen?: boolean; onClose?: () => void }> = ({ isOpen, onClose }) => {
    const [isProductsExpanded, setIsProductsExpanded] = useState(false);
    const [categories, setCategories] = useState<Category[]>([]);
    const [adminName, setAdminName] = useState(() => localStorage.getItem(ADMIN_NAME_KEY) || 'Admin');
    const [adminAvatar, setAdminAvatar] = useState(() => localStorage.getItem(ADMIN_AVATAR_KEY) || '');
    const [isEditingName, setIsEditingName] = useState(false);
    const [tempName, setTempName] = useState(adminName);
    const inputRef = useRef<HTMLInputElement>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);
    const location = useLocation();
    const navigate = useNavigate();

    useEffect(() => {
        setCategories(getCategories());
    }, [location.pathname]);

    useEffect(() => {
        if (isEditingName && inputRef.current) {
            inputRef.current.focus();
            inputRef.current.select();
        }
    }, [isEditingName]);

    const handleStartEdit = () => {
        setTempName(adminName);
        setIsEditingName(true);
    };

    const handleSaveName = () => {
        const newName = tempName.trim() || 'Admin';
        setAdminName(newName);
        localStorage.setItem(ADMIN_NAME_KEY, newName);
        setIsEditingName(false);
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') handleSaveName();
        else if (e.key === 'Escape') setIsEditingName(false);
    };

    const handleAvatarClick = () => {
        fileInputRef.current?.click();
    };

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (event) => {
            const base64 = event.target?.result as string;
            setAdminAvatar(base64);
            localStorage.setItem(ADMIN_AVATAR_KEY, base64);
        };
        reader.readAsDataURL(file);
    };

    const isProductsActive = location.pathname.includes('/products');

    const handleCategoryClick = (categoryId: string) => {
        if (categoryId === 'ALL') navigate('/products');
        else navigate(`/products?category=${categoryId}`);
        onClose?.();
    };

    return (
        <>
            {/* Mobile Overlay */}
            <div
                className={`fixed inset-0 bg-black/50 z-40 transition-opacity duration-300 md:hidden ${isOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'}`}
                onClick={onClose}
            ></div>

            <aside className={`fixed md:static inset-y-0 left-0 w-64 bg-white border-r border-slate-200 flex flex-col shadow-sm z-50 transform transition-transform duration-300 md:translate-x-0 admin-sidebar ${isOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                {/* Logo */}
                <div className="h-16 flex items-center justify-between px-6 border-b border-slate-200">
                    <div className="flex items-center gap-3 cursor-pointer">
                        <div className="w-10 h-10 text-primary">
                            <span className="material-symbols-outlined text-4xl font-bold">local_shipping</span>
                        </div>
                        <div className="flex flex-col">
                            <span className="text-slate-800 text-lg font-bold tracking-tight uppercase leading-tight">Sinotruk</span>
                            <span className="text-primary text-[10px] font-bold tracking-[0.15em] uppercase leading-tight">Admin</span>
                        </div>
                    </div>
                </div>

                {/* Navigation */}
                <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
                    <NavLink
                        to="/dashboard"
                        onClick={onClose}
                        className={({ isActive }) =>
                            `flex items-center gap-3 px-4 py-3 rounded-xl transition-colors ${isActive ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-700 hover:bg-slate-100'}`
                        }
                    >
                        <span className="material-symbols-outlined text-xl">dashboard</span>
                        <span className="font-medium">Dashboard</span>
                    </NavLink>

                    <div>
                        <button
                            onClick={() => setIsProductsExpanded(!isProductsExpanded)}
                            className={`w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-colors ${isProductsActive ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-700 hover:bg-slate-100'}`}
                        >
                            <div className="flex items-center gap-3">
                                <span className="material-symbols-outlined text-xl">inventory_2</span>
                                <span className="font-medium">Sản phẩm</span>
                            </div>
                            <span className={`material-symbols-outlined text-lg transition-transform ${isProductsExpanded ? 'rotate-180' : ''}`}>expand_more</span>
                        </button>

                        {isProductsExpanded && (
                            <div className="mt-1 ml-4 pl-4 border-l-2 border-slate-200 space-y-1">
                                {categories.map((cat) => (
                                    <button
                                        key={cat.id}
                                        onClick={() => handleCategoryClick(cat.id)}
                                        className="w-full text-left px-3 py-2 text-sm text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors"
                                    >
                                        {cat.label}
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    <NavLink
                        to="/categories"
                        onClick={onClose}
                        className={({ isActive }) =>
                            `flex items-center gap-3 px-4 py-3 rounded-xl transition-colors ${isActive ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-700 hover:bg-slate-100'}`
                        }
                    >
                        <span className="material-symbols-outlined text-xl">category</span>
                        <span className="font-medium">Danh mục</span>
                    </NavLink>
                </nav>

                {/* User */}
                <div className="p-4 border-t border-slate-200">
                    <div className="flex items-center gap-3 px-3 py-3 rounded-xl bg-slate-50 border border-slate-200">
                        {/* Avatar with upload */}
                        <div
                            onClick={handleAvatarClick}
                            className="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center border border-slate-200 flex-shrink-0 cursor-pointer overflow-hidden hover:border-primary transition-colors"
                            title="Click để đổi ảnh"
                        >
                            {adminAvatar ? (
                                <img src={adminAvatar} alt="Avatar" className="w-full h-full object-cover" />
                            ) : (
                                <span className="material-symbols-outlined text-slate-600 text-sm">person</span>
                            )}
                        </div>
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            onChange={handleAvatarChange}
                            className="hidden"
                        />

                        {/* Name with edit */}
                        <div
                            className="flex-1 min-w-0 cursor-pointer group"
                            onClick={handleStartEdit}
                            title="Click để đổi tên"
                        >
                            {isEditingName ? (
                                <input
                                    ref={inputRef}
                                    type="text"
                                    value={tempName}
                                    onChange={(e) => setTempName(e.target.value)}
                                    onBlur={handleSaveName}
                                    onKeyDown={handleKeyDown}
                                    className="w-full text-slate-800 text-xs font-bold bg-white border border-primary rounded px-1 py-0.5 focus:outline-none"
                                />
                            ) : (
                                <>
                                    <p className="text-slate-800 text-xs font-bold truncate group-hover:text-primary transition-colors">{adminName}</p>
                                    <p className="text-slate-500 text-[10px] truncate">Quản trị</p>
                                </>
                            )}
                        </div>

                        {/* Logout */}
                        <button
                            onClick={() => {
                                localStorage.removeItem('isAuthenticated');
                                localStorage.removeItem('username');
                                window.location.href = '/';
                            }}
                            className="text-slate-400 hover:text-primary transition-colors flex-shrink-0"
                            title="Đăng xuất"
                        >
                            <span className="material-symbols-outlined text-sm">logout</span>
                        </button>
                    </div>
                </div>
            </aside>
        </>
    );
};

export default Sidebar;
