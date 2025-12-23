export interface Product {
    id: number;
    code: string;
    name: string;
    price: number;
    total: number;
    category: string;
    image: string;
}

export interface Category {
    id: string;
    label: string;
}

// Default categories
const defaultCategories: Category[] = [
    { id: 'ALL', label: 'Tất cả' },
    { id: 'CABIN', label: 'CABIN' },
    { id: 'ĐỘNG CƠ', label: 'ĐỘNG CƠ' },
    { id: 'LY HỢP', label: 'LY HỢP' },
    { id: 'PHANH', label: 'PHANH' },
];

// LocalStorage key for categories
const CATEGORIES_STORAGE_KEY = 'sinotruk_admin_categories';

// Get categories from localStorage or use defaults
export function getCategories(): Category[] {
    try {
        const stored = localStorage.getItem(CATEGORIES_STORAGE_KEY);
        if (stored) {
            return JSON.parse(stored);
        }
    } catch (e) {
        console.error('Error loading categories:', e);
    }
    return defaultCategories;
}

// Save categories to localStorage
function saveCategories(categories: Category[]): void {
    localStorage.setItem(CATEGORIES_STORAGE_KEY, JSON.stringify(categories));
}

// Add a new category
export function addCategory(label: string): Category {
    const categories = getCategories();
    const id = label.toUpperCase().trim();

    // Check if already exists
    if (categories.some(c => c.id === id)) {
        throw new Error('Danh mục này đã tồn tại');
    }

    const newCategory: Category = { id, label: label.trim() };
    const updatedCategories = [...categories, newCategory];
    saveCategories(updatedCategories);

    return newCategory;
}

// Delete a category (cannot delete 'ALL')
export function deleteCategory(id: string): void {
    if (id === 'ALL') {
        throw new Error('Không thể xóa danh mục "Tất cả"');
    }

    const categories = getCategories();
    const updatedCategories = categories.filter(c => c.id !== id);
    saveCategories(updatedCategories);
}

// Reset categories to defaults
export function resetCategories(): void {
    saveCategories(defaultCategories);
}

// Export productCategories for backward compatibility
export const productCategories = getCategories();

export const mockProducts: Product[] = [
    { id: 1, code: 'XLKVX', name: 'Xilanh kích cabin VX350', price: 850000, total: 15, category: 'CABIN', image: '/images/xilanh.jpg' },
    { id: 2, code: 'TBTSI', name: 'Tăm bét trước SITRAK', price: 1200000, total: 8, category: 'ĐỘNG CƠ', image: '/images/sitrakg7s.webp' },
    { id: 3, code: 'LDDC-A7', name: 'Lọc dầu động cơ HOWO A7', price: 350000, total: 25, category: 'ĐỘNG CƠ', image: '/images/locdau.webp' },
    { id: 4, code: 'LC420', name: 'Lá côn HOWO 420', price: 2500000, total: 4, category: 'LY HỢP', image: '/images/daulockhi.webp' },
    { id: 5, code: 'PTTS', name: 'Phanh tang trống sau', price: 1800000, total: 12, category: 'PHANH', image: '/images/phanhchongtang.jpg' },
    { id: 6, code: 'LGN-VX', name: 'Lọc gió ngoài VX', price: 450000, total: 0, category: 'CABIN', image: '/images/locdau.webp' },
    { id: 7, code: 'GPT-S', name: 'Gioăng phớt tổng SITRAK', price: 950000, total: 3, category: 'ĐỘNG CƠ', image: '/images/xilanh.jpg' },
    { id: 8, code: 'BXL-H', name: 'Bình xăng lớn HOWO', price: 5500000, total: 45, category: 'LY HỢP', image: '/images/daulockhi.webp' },
    // Additional products for pagination demo
    { id: 9, code: 'XLKC-H', name: 'Xilanh kích cabin HOWO', price: 900000, total: 10, category: 'CABIN', image: '/images/xilanh.jpg' },
    { id: 10, code: 'LDN-A7', name: 'Lọc dầu nhớt A7', price: 280000, total: 30, category: 'ĐỘNG CƠ', image: '/images/locdau.webp' },
    { id: 11, code: 'BPH-ST', name: 'Bố phanh SITRAK', price: 2200000, total: 7, category: 'PHANH', image: '/images/phanhchongtang.jpg' },
    { id: 12, code: 'LC380', name: 'Lá côn HOWO 380', price: 2100000, total: 6, category: 'LY HỢP', image: '/images/daulockhi.webp' },
    { id: 13, code: 'GLKN', name: 'Gương lái kính ngoài', price: 750000, total: 20, category: 'CABIN', image: '/images/xilanh.jpg' },
    { id: 14, code: 'DXLP', name: 'Đèn xi nhan lái phải', price: 450000, total: 18, category: 'CABIN', image: '/images/xilanh.jpg' },
    { id: 15, code: 'TKDC', name: 'Tăng cường động cơ', price: 3500000, total: 5, category: 'ĐỘNG CƠ', image: '/images/sitrakg7s.webp' },
];
