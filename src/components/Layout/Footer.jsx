import { Link } from 'react-router-dom'
import { MapPin, Phone, Mail } from 'lucide-react'
import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import './Footer.css'

const Footer = () => {
  const footerRef = useRef(null)
  const sectionsRef = useRef([])

  useEffect(() => {
    gsap.from(sectionsRef.current, {
      opacity: 0,
      y: 30,
      duration: 0.6,
      stagger: 0.1,
      scrollTrigger: {
        trigger: footerRef.current,
        start: 'top 80%',
      },
    })
  }, [])

  const footerLinks = {
    services: [
      { path: '/about#warranty', label: 'Dịch vụ & hậu mãi' },
      { path: '/about#supply', label: 'Chính sách cung cấp' },
      { path: '/about#warranty', label: 'Chính sách bảo hành' },
      { path: '/about#payment', label: 'Phương thức thanh toán' },
    ],
    support: [
      { path: '/image-library', label: 'Thư viện ảnh' },
      { path: '/catalog', label: 'Catalog' },
      { path: '/contact', label: 'Liên hệ' },
    ],
  }

  return (
    <footer ref={footerRef} className="footer">
      <div className="container">
        <div className="footer-content">
          <div
            ref={(el) => (sectionsRef.current[0] = el)}
            className="footer-section"
          >
            <div className="footer-logo">
              <span className="logo-icon">🚛</span>
              <div className="logo-text">
                <span className="logo-brand">SINOTRUK</span>
                <span className="logo-location">HÀ NỘI</span>
              </div>
            </div>
            <p className="footer-tagline">PHỤ TÙNG CHÍNH HÃNG</p>
            <div className="footer-address">
              <MapPin className="address-icon" />
              <p>
                Địa chỉ: Thôn 1, Xã Lại Yên, Hoài Đức, Hà Nội
                <br />
                (Cách cầu vượt An Khánh 300m)
              </p>
            </div>
          </div>

          <div
            ref={(el) => (sectionsRef.current[1] = el)}
            className="footer-section"
          >
            <h3 className="footer-title">DỊCH VỤ</h3>
            <ul className="footer-links">
              {footerLinks.services.map((link, index) => (
                <li key={index}>
                  <Link to={link.path}>{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>

          <div
            ref={(el) => (sectionsRef.current[2] = el)}
            className="footer-section"
          >
            <h3 className="footer-title">HỖ TRỢ</h3>
            <ul className="footer-links">
              {footerLinks.support.map((link, index) => (
                <li key={index}>
                  <Link to={link.path}>{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>

          <div
            ref={(el) => (sectionsRef.current[3] = el)}
            className="footer-section"
          >
            <h3 className="footer-title">LIÊN HỆ</h3>
            <div className="footer-contact">
              <div className="contact-item">
                <Phone className="contact-icon" />
                <div>
                  <p className="contact-label">Hotline 24/7</p>
                  <a href="tel:0382890990" className="contact-value">
                    0382.890.990
                  </a>
                </div>
              </div>
              <div className="contact-item">
                <Mail className="contact-icon" />
                <div>
                  <p className="contact-label">Email</p>
                  <a
                    href="mailto:hnsinotruk@gmail.com"
                    className="contact-value"
                  >
                    hnsinotruk@gmail.com
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="footer-bottom">
          <p>&copy; 2025 SINOTRUK HÀ NỘI. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </footer>
  )
}

export default Footer