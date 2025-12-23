import React, { useState, useEffect } from 'react';
import { NavLink, useLocation, useNavigate } from 'react-router-dom';
import { getCategories } from '../../data/mockDatabase';

interface Category {
    id: string;
    label: string;
}

const Sidebar: React.FC<{ isOpen?: boolean; onClose?: () => void }> = ({ isOpen, onClose }) => {
    const [isProductsExpanded, setIsProductsExpanded] = useState(false);
    const [categories, setCategories] = useState<Category[]>([]);
    const location = useLocation();
    const navigate = useNavigate();

    // Load categories on mount and when location changes (to refresh after adding new category)
    useEffect(() => {
        setCategories(getCategories());
    }, [location.pathname]);

    // Check if current path is products related
    const isProductsActive = location.pathname.includes('/products');

    const handleCategoryClick = (categoryId: string) => {
        if (categoryId === 'ALL') {
            navigate('/products');
        } else {
            navigate(`/products?category=${categoryId}`);
        }
        onClose?.();
    };

    return (
        <>
            {/* Mobile Overlay */}
            <div
                className={`fixed inset-0 bg-black/50 z-40 transition-opacity duration-300 md:hidden ${isOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
                    }`}
                onClick={onClose}
            ></div>

            <aside className={`fixed md:static inset-y-0 left-0 w-64 bg-white border-r border-slate-200 flex flex-col shadow-sm z-50 transform transition-transform duration-300 md:translate-x-0 admin-sidebar ${isOpen ? 'translate-x-0' : '-translate-x-full'
                }`}>
                {/* Logo - matching frontend Navbar exactly */}
                <div className="h-16 flex items-center justify-between px-6 border-b border-slate-200">
                    <div className="flex items-center gap-3 cursor-pointer">
                        <div className="w-10 h-10 text-primary">
                            <span className="material-symbols-outlined text-4xl font-bold">local_shipping</span>
                        </div>
                        <div className="flex flex-col">
                            <span className="text-slate-800 text-xl font-bold tracking-tight leading-none uppercase">Sinotruk</span>
                            <span className="text-primary text-[10px] font-bold tracking-[0.2em] leading-none uppercase">Admin</span>
                        </div>
                    </div>

                    {/* Mobile Close Button */}
                    <button onClick={onClose} className="md:hidden text-slate-400 hover:text-slate-600">
                        <span className="material-symbols-outlined">close</span>
                    </button>
                </div>

                {/* Navigation */}
                <nav className="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
                    {/* Dashboard */}
                    <NavLink
                        to="/dashboard"
                        onClick={onClose}
                        className={({ isActive }) =>
                            `flex items-center gap-3 px-4 py-3 rounded-xl transition-colors ${isActive
                                ? 'bg-primary text-white shadow-lg shadow-primary/20'
                                : 'text-slate-700 hover:bg-slate-100 hover:text-slate-800'
                            }`
                        }
                    >
                        <span className="material-symbols-outlined text-xl">dashboard</span>
                        <span className="font-medium">Dashboard</span>
                    </NavLink>

                    {/* Products with Dropdown */}
                    <div className="space-y-1">
                        <button
                            onClick={() => setIsProductsExpanded(!isProductsExpanded)}
                            className={`w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-colors ${isProductsActive
                                ? 'bg-primary text-white shadow-lg shadow-primary/20'
                                : 'text-slate-700 hover:bg-slate-100 hover:text-slate-800'
                                }`}
                        >
                            <div className="flex items-center gap-3">
                                <span className="material-symbols-outlined text-xl">inventory_2</span>
                                <span className="font-medium">Sản phẩm</span>
                            </div>
                            <span className={`material-symbols-outlined text-lg transition-transform duration-200 ${isProductsExpanded ? 'rotate-180' : ''}`}>
                                expand_more
                            </span>
                        </button>

                        {/* Dropdown Menu */}
                        <div className={`overflow-hidden transition-all duration-300 ${isProductsExpanded ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0'}`}>
                            <div className="pl-4 space-y-1 pt-1">
                                {/* All Products */}
                                <button
                                    onClick={() => handleCategoryClick('ALL')}
                                    className={`w-full flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-colors ${location.pathname === '/products' && !location.search
                                        ? 'bg-primary/10 text-primary font-semibold'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800'
                                        }`}
                                >
                                    <span className="material-symbols-outlined text-sm">apps</span>
                                    <span>Tất cả</span>
                                </button>

                                {/* Category Items */}
                                {categories.filter(cat => cat.id !== 'ALL').map((category) => (
                                    <button
                                        key={category.id}
                                        onClick={() => handleCategoryClick(category.id)}
                                        className={`w-full flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-colors ${location.search.includes(`category=${category.id}`)
                                            ? 'bg-primary/10 text-primary font-semibold'
                                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800'
                                            }`}
                                    >
                                        <span className="material-symbols-outlined text-sm">label</span>
                                        <span>{category.label}</span>
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Categories Management */}
                    <NavLink
                        to="/categories"
                        onClick={onClose}
                        className={({ isActive }) =>
                            `flex items-center gap-3 px-4 py-3 rounded-xl transition-colors ${isActive
                                ? 'bg-primary text-white shadow-lg shadow-primary/20'
                                : 'text-slate-700 hover:bg-slate-100 hover:text-slate-800'
                            }`
                        }
                    >
                        <span className="material-symbols-outlined text-xl">category</span>
                        <span className="font-medium">Danh mục</span>
                    </NavLink>
                </nav>

                {/* User */}
                <div className="p-4 border-t border-slate-200">
                    <div className="flex items-center gap-3 px-3 py-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div className="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center border border-slate-200 flex-shrink-0">
                            <span className="material-symbols-outlined text-slate-600 text-sm">person</span>
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-slate-800 text-xs font-bold truncate">Admin</p>
                            <p className="text-slate-500 text-[10px] truncate">Quản trị</p>
                        </div>
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
