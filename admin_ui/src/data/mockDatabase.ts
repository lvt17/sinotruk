export interface Product {
    id: number;
    code: string;
    name: string;
    price: number;
    total: number;
    category: string;
    image: string;
}

export const mockProducts: Product[] = [
    { id: 1, code: 'XLKVX', name: 'Xilanh kích cabin VX350', price: 850000, total: 15, category: 'CABIN', image: '/images/xilanh.jpg' },
    { id: 2, code: 'TBTSI', name: 'Tăm bét trước SITRAK', price: 1200000, total: 8, category: 'ĐỘNG CƠ', image: '/images/sitrakg7s.webp' },
    { id: 3, code: 'LDDC-A7', name: 'Lọc dầu động cơ HOWO A7', price: 350000, total: 25, category: 'ĐỘNG CƠ', image: '/images/locdau.webp' },
    { id: 4, code: 'LC420', name: 'Lá côn HOWO 420', price: 2500000, total: 4, category: 'LY HỢP', image: '/images/daulockhi.webp' },
    { id: 5, code: 'PTTS', name: 'Phanh tang trống sau', price: 1800000, total: 12, category: 'PHANH', image: '/images/phanhchongtang.jpg' },
    { id: 6, code: 'LGN-VX', name: 'Lọc gió ngoài VX', price: 450000, total: 0, category: 'CABIN', image: '/images/locdau.webp' },
    { id: 7, code: 'GPT-S', name: 'Gioăng phớt tổng SITRAK', price: 950000, total: 3, category: 'ĐỘNG CƠ', image: '/images/xilanh.jpg' },
    { id: 8, code: 'BXL-H', name: 'Bình xăng lớn HOWO', price: 5500000, total: 45, category: 'LY HỢP', image: '/images/daulockhi.webp' },
];

export const productCategories = [
    { id: 'ALL', label: 'Tất cả' },
    { id: 'CABIN', label: 'CABIN' },
    { id: 'ĐỘNG CƠ', label: 'ĐỘNG CƠ' },
    { id: 'LY HỢP', label: 'LY HỢP' },
    { id: 'PHANH', label: 'PHANH' },
];
