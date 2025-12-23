import React from 'react';
import { PieChart, Pie, Cell, Tooltip, Legend, ResponsiveContainer } from 'recharts';

import { mockProducts } from '../data/mockDatabase';

const statsConfig = [
    { label: 'Tổng sản phẩm', icon: 'inventory_2', color: 'bg-blue-500' },
    { label: 'Sắp hết hàng', icon: 'warning', color: 'bg-yellow-500' },
    { label: 'Hết hàng', icon: 'error', color: 'bg-red-500' },
    { label: 'Danh mục', icon: 'category', color: 'bg-primary' },
];

const Dashboard: React.FC = () => {
    // Dynamically calculate stats from mockDatabase
    const totalProducts = mockProducts.length;
    const lowStockCount = mockProducts.filter(p => p.total > 0 && p.total <= 10).length;
    const outOfStockCount = mockProducts.filter(p => p.total === 0).length;
    const categoriesCount = new Set(mockProducts.map(p => p.category)).size;

    const stats = [
        { ...statsConfig[0], value: totalProducts.toLocaleString() },
        { ...statsConfig[1], value: lowStockCount.toLocaleString() },
        { ...statsConfig[2], value: outOfStockCount.toLocaleString() },
        { ...statsConfig[3], value: categoriesCount.toLocaleString() },
    ];

    // Calculate category distribution for pie chart
    const catsMap = mockProducts.reduce((acc: any, p) => {
        acc[p.category] = (acc[p.category] || 0) + 1;
        return acc;
    }, {});

    const colors = ['#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#94a3b8', '#ec4899'];
    const categoryDistribution = Object.keys(catsMap).map((cat, i) => ({
        name: cat,
        value: catsMap[cat],
        color: colors[i % colors.length]
    }));

    return (
        <div className="space-y-6">
            {/* Page Header */}
            <div className="mb-2 md:mb-0">
                <h1 className="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h1>
                <p className="text-slate-500 text-sm md:text-base">Tổng quan kho sản phẩm Sinotruk</p>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                {stats.map((stat, i) => (
                    <div key={i} className="card flex items-center gap-4 p-4 md:p-6 hover:border-primary/30 transition-colors">
                        <div className={`w-10 h-10 md:w-12 md:h-12 ${stat.color} rounded-xl flex items-center justify-center shadow-lg flex-shrink-0`}>
                            <span className="material-symbols-outlined text-white text-xl md:text-2xl">{stat.icon}</span>
                        </div>
                        <div className="min-w-0">
                            <p className="text-slate-500 text-xs md:text-sm truncate">{stat.label}</p>
                            <p className="text-slate-800 text-xl md:text-2xl font-bold tracking-tight">{stat.value}</p>
                        </div>
                    </div>
                ))}
            </div>

            {/* Charts Grid */}
            <div className="grid grid-cols-1 gap-6">
                {/* Category Distribution Chart */}
                <div className="card">
                    <h2 className="text-xl font-bold text-slate-800 tracking-tight mb-6">Phân bổ theo danh mục</h2>
                    <ResponsiveContainer width="100%" height={300}>
                        <PieChart>
                            <Pie
                                data={categoryDistribution}
                                cx="50%"
                                cy="50%"
                                innerRadius={60}
                                outerRadius={100}
                                paddingAngle={5}
                                dataKey="value"
                            >
                                {categoryDistribution.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.color} />
                                ))}
                            </Pie>
                            <Tooltip
                                contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)' }}
                            />
                            <Legend verticalAlign="bottom" height={36} />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
