import React, { useState } from 'react';
import { useLocation } from 'react-router-dom';
import { useNotification } from '../shared/Notification';
import NotificationDropdown from '../NotificationDropdown';
import SettingsModal from '../SettingsModal';

interface HeaderProps {
    onMenuClick?: () => void;
}

const Header: React.FC<HeaderProps> = ({ onMenuClick }) => {
    const notification = useNotification();
    const [showSettings, setShowSettings] = useState(false);
    const location = useLocation();

    const getPageTitle = () => {
        const path = location.pathname;
        if (path.includes('/dashboard')) return 'Tổng quan hệ thống';
        if (path.includes('/products')) return 'Danh mục Phụ tùng';
        return 'Admin Panel';
    };

    const handleExportReport = () => {
        notification.info('Hệ thống đang trích xuất dữ liệu Catalog...');
    };

    return (
        <>
            <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 shadow-sm sticky top-0 z-10">
                <div className="flex items-center gap-4">
                    {/* Mobile Menu Toggle */}
                    <button
                        onClick={onMenuClick}
                        className="md:hidden p-2 text-slate-600 hover:text-primary transition-colors hover:bg-slate-50 rounded-xl"
                    >
                        <span className="material-symbols-outlined">menu</span>
                    </button>

                    {/* Section Indicator */}
                    <div className="flex items-center gap-3">
                        <div className="hidden md:flex items-center gap-2 px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg">
                            <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-widest">System Online</span>
                        </div>
                        <div className="h-6 w-[1px] bg-slate-200 hidden md:block"></div>
                        <h2 className="text-slate-800 font-bold text-sm md:text-base tracking-tight">{getPageTitle()}</h2>
                    </div>
                </div>

                {/* Actions */}
                <div className="flex items-center gap-2 md:gap-4">
                    <button
                        onClick={handleExportReport}
                        className="hidden sm:flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-primary transition-all text-xs font-bold border border-transparent hover:border-slate-100 hover:bg-slate-50 rounded-xl"
                    >
                        <span className="material-symbols-outlined text-sm">download</span>
                        <span>Trích xuất Data</span>
                    </button>

                    <div className="h-6 w-[1px] bg-slate-200 hidden sm:block mx-1"></div>

                    <NotificationDropdown />

                    <button
                        onClick={() => setShowSettings(true)}
                        className="p-2 text-slate-600 hover:text-primary transition-all hover:bg-slate-50 rounded-xl"
                        title="Cấu hình Catalog"
                    >
                        <span className="material-symbols-outlined text-xl md:text-2xl">settings_applications</span>
                    </button>
                </div>
            </header>

            {showSettings && <SettingsModal onClose={() => setShowSettings(false)} />}
        </>
    );
};

export default Header;
