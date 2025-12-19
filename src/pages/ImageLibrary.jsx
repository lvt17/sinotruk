import { useRef } from 'react'
import { Image as ImageIcon } from 'lucide-react'
import './ImageLibrary.css'

const ImageLibrary = () => {
  const sectionRef = useRef(null)
  const galleryRef = useRef(null)

  // Mock images - sẽ thay bằng API
  const images = Array.from({ length: 12 }, (_, i) => ({
    id: i + 1,
    url: `/images/gallery/image-${i + 1}.jpg`,
    title: `Hình ảnh ${i + 1}`,
  }))

  return (
    <div ref={sectionRef} className="image-library-page">
      <div className="image-library-header">
        <div className="container">
          <h1 className="page-title">THƯ VIỆN ẢNH</h1>
          <p className="page-subtitle">
            Hình ảnh thực tế tại công ty CP SINOTRUK HÀ NỘI
          </p>
        </div>
      </div>

      <div className="container">
        <div className="image-library-intro">
          <p>
            Dưới đây là những hình ảnh thực tế ghi nhận tại công ty CP
            SINOTRUK HÀ NỘI, bao gồm các hoạt động xuất nhập khẩu, đóng gói và
            vận chuyển đơn hàng tới Quý khách hàng.
          </p>
          <p>
            Để biết thêm chi tiết về sản phẩm, dịch vụ và các chính sách, vui
            lòng liên hệ qua số hotline: <strong>0382.890.990</strong> hoặc
            email: <strong>hnsinotruk@gmail.com</strong>
          </p>
        </div>

        <div ref={galleryRef} className="image-gallery">
          {images.map((image) => (
            <div key={image.id} className="gallery-item">
              <div className="gallery-image-placeholder">
                <ImageIcon className="placeholder-icon" />
              </div>
              <div className="gallery-overlay">
                <span>{image.title}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default ImageLibrary


