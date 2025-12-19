# SINOTRUK HÀ NỘI - Website Mới

Website mới cho công ty SINOTRUK HÀ NỘI - Chuyên cung cấp phụ tùng xe tải chính hãng.

## 🚀 Tính năng

- ✅ Thiết kế hiện đại, sắc nét, phù hợp với ngành vận tải
- ✅ Responsive design - Tối ưu cho mọi thiết bị
- ✅ Animations mượt mà với GSAP
- ✅ Tìm kiếm sản phẩm nâng cao
- ✅ Lọc sản phẩm theo danh mục
- ✅ UX được tối ưu hóa

## 🛠️ Công nghệ sử dụng

- **React 18** - UI Framework
- **Vite** - Build tool nhanh
- **GSAP** - Animations và interactions
- **React Router** - Điều hướng trang
- **Lucide React** - Icon library

## 📦 Cài đặt

```bash
# Cài đặt dependencies
npm install

# Chạy development server
npm run dev

# Build cho production
npm run build

# Preview build
npm run preview
```

## 📁 Cấu trúc dự án

```
src/
├── components/          # Các components tái sử dụng
│   ├── Layout/         # Header, Footer, Layout
│   ├── Home/           # Components cho trang chủ
│   └── Product/        # Components sản phẩm
├── pages/              # Các trang chính
│   ├── Home.jsx
│   ├── Products.jsx
│   ├── About.jsx
│   ├── Contact.jsx
│   ├── Catalog.jsx
│   └── ImageLibrary.jsx
├── styles/              # Global styles
└── App.jsx             # Root component
```

## 🎨 Thiết kế

### Màu sắc chủ đạo
- **Primary**: #1a1a1a (Đen công nghiệp)
- **Secondary**: #ff6b35 (Cam năng động)
- **Accent**: #f7931e (Cam nhạt)

### Typography
- Font: Inter (Google Fonts)
- Responsive font sizes với clamp()

## 🔄 Tích hợp API (Sau này)

Website hiện đang sử dụng mock data. Để tích hợp với backend:

1. Tạo file `src/services/api.js`
2. Cập nhật các components để fetch data từ API
3. Thêm error handling và loading states

## 📝 Ghi chú

- Tất cả hình ảnh hiện là placeholder, cần thay thế bằng ảnh thật
- Mock data trong các components cần được thay bằng API calls
- Google Maps embed cần cập nhật với địa chỉ thật

## 📞 Liên hệ

- Hotline: 0382.890.990
- Email: hnsinotruk@gmail.com
- Địa chỉ: Thôn 1, Xã Lại Yên, Hoài Đức, Hà Nội


