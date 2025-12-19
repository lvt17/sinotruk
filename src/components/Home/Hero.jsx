import { Link } from 'react-router-dom'
import { ArrowRight, Truck } from 'lucide-react'
import './Hero.css'

const Hero = () => {
  return (
    <section className="hero">
      <div className="hero-background">
        <div className="hero-overlay"></div>
        <div className="hero-image">
          {/* Placeholder for hero image - sẽ thay bằng ảnh thật */}
          <div className="hero-image-placeholder">
            <Truck size={200} />
          </div>
        </div>
      </div>

      <div className="container">
        <div className="hero-content">
          <div className="hero-text">
            <h1 className="hero-title">SINOTRUK HÀ NỘI</h1>
            <p className="hero-subtitle">
              Hotline: <strong>0382.890.990</strong>
            </p>
            <p className="hero-description">
              Chuyên cung cấp phụ tùng các dòng xe Howo 371, 375, 380, 420,
              SITRAK & Rơ moóc chính hãng. Làm việc uy tín, trách nhiệm với
              đội ngũ chuyên nghiệp và sản phẩm chất lượng cao.
            </p>
            <div className="hero-buttons">
              <Link to="/products" className="btn btn-primary">
                Xem Sản Phẩm
                <ArrowRight className="btn-icon" />
              </Link>
              <Link to="/contact" className="btn btn-secondary">
                Liên Hệ Ngay
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div className="hero-scroll-indicator">
        <div className="scroll-mouse">
          <div className="scroll-wheel"></div>
        </div>
        <span>Cuộn xuống</span>
      </div>
    </section>
  )
}

export default Hero


