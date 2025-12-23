import { Link } from 'react-router-dom'

const categories = [
    {
        title: 'Phụ Tùng Động Cơ',
        desc: 'Lọc dầu, lọc gió, piston, kim phun...',
        icon: 'manufacturing',
        path: '/products'
    },
    {
        title: 'Phụ Tùng Phanh',
        desc: 'Má phanh, đĩa phanh, bầu phanh...',
        icon: 'car_crash',
        path: '/products'
    },
    {
        title: 'Phụ Tùng Cabin',
        desc: 'Gương, kính, bơm cabin, ghế...',
        icon: 'airline_seat_recline_extra',
        path: '/products'
    },
    {
        title: 'Phụ Tùng Ly Hợp',
        desc: 'Đĩa ly hợp, bàn ép, bi ly hợp...',
        icon: 'settings',
        path: '/products'
    },
    {
        title: 'Phụ Tùng Hộp Số',
        desc: 'Bánh răng, đồng tốc, vòng bi...',
        icon: 'precision_manufacturing',
        path: '/products'
    },
    {
        title: 'Phụ Tùng Điện',
        desc: 'Máy phát, củ đề, cảm biến...',
        icon: 'electric_bolt',
        path: '/products'
    }
]

const CategorySection = () => {
    return (
        <section className="py-24 bg-gray-100">
            <div className="container mx-auto px-4 md:px-10 lg:px-20">
                <div className="text-center mb-16 space-y-4">
                    <h2 className="text-4xl md:text-5xl font-bold text-gray-800 tracking-tight">Danh Mục Phụ Tùng</h2>
                    <div className="h-1.5 w-24 bg-gradient-to-r from-gray-400 to-primary mx-auto rounded-full shadow-lg shadow-primary/30"></div>
                    <p className="text-slate-500 max-w-2xl mx-auto">Đầy đủ phụ tùng chính hãng cho xe tải HOWO, SITRAK, SINOTRUK</p>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-3 gap-6">
                    {categories.map((cat, i) => (
                        <Link to={cat.path} key={i} className="group cursor-pointer">
                            <div className="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300 h-full">
                                <div className="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                                    <span className="material-symbols-outlined text-3xl">{cat.icon}</span>
                                </div>
                                <h4 className="text-lg font-bold text-slate-800 mb-2 group-hover:text-primary transition-colors">{cat.title}</h4>
                                <p className="text-slate-500 text-sm">{cat.desc}</p>
                            </div>
                        </Link>
                    ))}
                </div>
            </div>
        </section>
    )
}

export default CategorySection
