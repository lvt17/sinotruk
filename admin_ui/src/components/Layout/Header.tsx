import React, { useState, useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import SettingsModal from '../SettingsModal';

interface HeaderProps {
    onMenuClick?: () => void;
}

const Header: React.FC<HeaderProps> = ({ onMenuClick }) => {
    const [showSettings, setShowSettings] = useState(false);
    const [currentTime, setCurrentTime] = useState(new Date());
    const location = useLocation();

    // Update time every minute
    useEffect(() => {
        const timer = setInterval(() => setCurrentTime(new Date()), 60000);
        return () => clearInterval(timer);
    }, []);

    useEffect(() => {
        if (showSettings) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    }, [showSettings]);

    const getPageInfo = () => {
        const path = location.pathname;
        if (path.includes('/dashboard')) return { title: 'Dashboard', icon: 'dashboard' };
        if (path.includes('/products')) return { title: 'Sản phẩm', icon: 'inventory_2' };
        if (path.includes('/categories')) return { title: 'Danh mục', icon: 'category' };
        return { title: 'Admin', icon: 'admin_panel_settings' };
    };

    const pageInfo = getPageInfo();
    const formattedDate = currentTime.toLocaleDateString('vi-VN', {
        weekday: 'long',
        day: 'numeric',
        month: 'long'
    });

    return (
        <>
            <header className="h-14 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
                {/* Left Section */}
                <div className="flex items-center gap-3">
                    {/* Mobile Menu Toggle */}
                    <button
                        onClick={onMenuClick}
                        className="md:hidden p-2 text-slate-500 hover:text-primary transition-colors rounded-lg hover:bg-slate-50"
                    >
                        <span className="material-symbols-outlined text-xl">menu</span>
                    </button>

                    {/* Breadcrumb Style Page Title */}
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span className="material-symbols-outlined text-primary text-lg">{pageInfo.icon}</span>
                        </div>
                        <div className="flex flex-col">
                            <span className="text-xs text-slate-400 font-medium hidden sm:block">Sinotruk Admin</span>
                            <h1 className="text-sm md:text-base font-bold text-slate-800 leading-tight">{pageInfo.title}</h1>
                        </div>
                    </div>
                </div>

                {/* Right Section */}
                <div className="flex items-center gap-3">
                    {/* Date & Time */}
                    <div className="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-lg border border-slate-100">
                        <span className="material-symbols-outlined text-slate-400 text-sm">calendar_today</span>
                        <span className="text-xs font-medium text-slate-600">{formattedDate}</span>
                    </div>

                    {/* Status Indicator */}
                    <div className="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 bg-green-50 rounded-lg border border-green-100">
                        <span className="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <span className="text-[10px] font-bold text-green-600 uppercase tracking-wide">Online</span>
                    </div>

                    {/* Settings Button */}
                    <button
                        onClick={() => setShowSettings(true)}
                        className="p-2 text-slate-500 hover:text-primary transition-all hover:bg-slate-50 rounded-lg"
                        title="Cài đặt"
                    >
                        <span className="material-symbols-outlined text-xl">settings</span>
                    </button>
                </div>
            </header>

            {showSettings && <SettingsModal onClose={() => setShowSettings(false)} />}
        </>
    );
};

export default Header;
