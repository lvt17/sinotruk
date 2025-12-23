import React, { createContext, useContext, useState, useCallback } from 'react';

export type NotificationType = 'success' | 'error' | 'info' | 'warning';

interface Notification {
    id: string;
    type: NotificationType;
    message: string;
}

interface NotificationContextType {
    notifications: Notification[];
    success: (message: string) => void;
    error: (message: string) => void;
    info: (message: string) => void;
    warning: (message: string) => void;
    remove: (id: string) => void;
}

const NotificationContext = createContext<NotificationContextType | undefined>(undefined);

export const useNotification = () => {
    const context = useContext(NotificationContext);
    if (!context) {
        throw new Error('useNotification must be used within NotificationProvider');
    }
    return context;
};

interface NotificationProviderProps {
    children: React.ReactNode;
}

export const NotificationProvider: React.FC<NotificationProviderProps> = ({ children }) => {
    const [notifications, setNotifications] = useState<Notification[]>([]);

    const addNotification = useCallback((type: NotificationType, message: string) => {
        const id = `notification-${Date.now()}-${Math.random()}`;
        const notification: Notification = { id, type, message };

        setNotifications(prev => [...prev, notification]);

        // Auto dismiss after 3 seconds
        setTimeout(() => {
            setNotifications(prev => prev.filter(n => n.id !== id));
        }, 3000);
    }, []);

    const success = useCallback((message: string) => addNotification('success', message), [addNotification]);
    const error = useCallback((message: string) => addNotification('error', message), [addNotification]);
    const info = useCallback((message: string) => addNotification('info', message), [addNotification]);
    const warning = useCallback((message: string) => addNotification('warning', message), [addNotification]);

    const remove = useCallback((id: string) => {
        setNotifications(prev => prev.filter(n => n.id !== id));
    }, []);

    return (
        <NotificationContext.Provider value={{ notifications, success, error, info, warning, remove }}>
            {children}
            <NotificationContainer notifications={notifications} onRemove={remove} />
        </NotificationContext.Provider>
    );
};

interface NotificationContainerProps {
    notifications: Notification[];
    onRemove: (id: string) => void;
}

const NotificationContainer: React.FC<NotificationContainerProps> = ({ notifications, onRemove }) => {
    return (
        <div className="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none">
            {notifications.map(notification => (
                <NotificationToast
                    key={notification.id}
                    notification={notification}
                    onRemove={onRemove}
                />
            ))}
        </div>
    );
};

interface NotificationToastProps {
    notification: Notification;
    onRemove: (id: string) => void;
}

const NotificationToast: React.FC<NotificationToastProps> = ({ notification, onRemove }) => {
    const getIcon = () => {
        switch (notification.type) {
            case 'success':
                return 'check_circle';
            case 'error':
                return 'error';
            case 'warning':
                return 'warning';
            case 'info':
                return 'info';
        }
    };

    const getStyles = () => {
        switch (notification.type) {
            case 'success':
                return 'bg-green-50 border-green-500 text-green-800';
            case 'error':
                return 'bg-red-50 border-red-500 text-red-800';
            case 'warning':
                return 'bg-yellow-50 border-yellow-500 text-yellow-800';
            case 'info':
                return 'bg-blue-50 border-blue-500 text-blue-800';
        }
    };

    const getIconColor = () => {
        switch (notification.type) {
            case 'success':
                return 'text-green-600';
            case 'error':
                return 'text-red-600';
            case 'warning':
                return 'text-yellow-600';
            case 'info':
                return 'text-blue-600';
        }
    };

    return (
        <div
            className={`pointer-events-auto flex items-center gap-3 min-w-[320px] max-w-md p-4 rounded-xl border-l-4 shadow-xl backdrop-blur-sm animate-slide-in-right ${getStyles()}`}
        >
            <span className={`material-symbols-outlined text-2xl ${getIconColor()}`}>
                {getIcon()}
            </span>
            <p className="flex-1 font-medium text-sm">
                {notification.message}
            </p>
            <button
                onClick={() => onRemove(notification.id)}
                className="p-1 hover:bg-black/5 rounded-lg transition-colors"
            >
                <span className="material-symbols-outlined text-lg opacity-60">
                    close
                </span>
            </button>
        </div>
    );
};
