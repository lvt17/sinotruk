import React, { useState } from 'react';
import { useNotification } from '../shared/Notification';
import NotificationDropdown from '../NotificationDropdown';
import SettingsModal from '../SettingsModal';

interface HeaderProps {
    onMenuClick?: () => void;
}

const Header: React.FC<HeaderProps> = ({ onMenuClick }) => {
    const notification = useNotification();
    const [showSettings, setShowSettings] = useState(false);

    const handleExportReport = () => {
        // In production, call API to export report
        notification.info('Tính năng xuất báo cáo đang được phát triển. Báo cáo sẽ được tải xuống dạng Excel.');
        // Example: window.open('/api/reports/export', '_blank');
    };

    return (
        <>
            <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 shadow-sm">
                <div className="flex items-center gap-4 flex-1">
                    {/* Mobile Menu Toggle */}
                    <button
                        onClick={onMenuClick}
                        className="md:hidden p-2 text-slate-600 hover:text-primary transition-colors"
                    >
                        <span className="material-symbols-outlined">menu</span>
                    </button>

                    {/* Search - hidden on very small screens or made smaller */}
                    <div className="hidden sm:block flex-1 max-w-md">
                        <input
                            type="text"
                            placeholder="Tìm kiếm..."
                            className="input text-sm py-1.5"
                        />
                    </div>
                </div>

                {/* Actions */}
                <div className="flex items-center gap-2 md:gap-4">
                    <NotificationDropdown />
                    <button
                        onClick={() => setShowSettings(true)}
                        className="p-2 text-slate-600 hover:text-primary transition-colors"
                        title="Cài đặt"
                    >
                        <span className="material-symbols-outlined text-xl md:text-2xl">settings</span>
                    </button>

                    {/* CTA Button - replaced with icon on mobile */}
                    <button
                        onClick={handleExportReport}
                        className="flex items-center justify-center gap-2 px-3 md:px-6 py-2 bg-primary hover:bg-primary-dark text-white text-xs md:text-sm font-bold rounded-lg md:rounded-xl transition-colors shadow-lg shadow-primary/20"
                    >
                        <span className="hidden md:inline">Xuất báo cáo</span>
                        <span className="material-symbols-outlined text-sm">download</span>
                    </button>
                </div>
            </header>

            {showSettings && <SettingsModal onClose={() => setShowSettings(false)} />}
        </>
    );
};

export default Header;
