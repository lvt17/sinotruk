import { useState, useEffect, useRef } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { Menu, X, Phone, Truck, Shield } from 'lucide-react'
import { gsap } from 'gsap'
import './Header.css'

const Header = ({ isScrolled }) => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)
  const [activeDropdown, setActiveDropdown] = useState(null)
  const location = useLocation()
  const headerRef = useRef(null)
  const logoRef = useRef(null)

  useEffect(() => {
    if (isScrolled) {
      gsap.to(headerRef.current, {
        backgroundColor: 'rgba(255, 255, 255, 0.98)',
        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)',
        duration: 0.3,
      })
    } else {
      gsap.to(headerRef.current, {
        backgroundColor: 'rgba(255, 255, 255, 1)',
        boxShadow: 'none',
        duration: 0.3,
      })
    }
  }, [isScrolled])

  useEffect(() => {
    gsap.from(logoRef.current, {
      opacity: 0,
      x: -20,
      duration: 0.6,
      ease: 'power3.out',
    })
  }, [])

  const menuItems = [
    { path: '/', label: 'TRANG CHỦ' },
    { path: '/about', label: 'GIỚI THIỆU' },
    {
      path: '/products',
      label: 'PHỤ TÙNG THEO XE',
      dropdown: [
        { path: '/products/howo-a7', label: 'HOWO A7' },
        { path: '/products/howo-ben', label: 'HOWO BEN' },
        { path: '/products/howo-sitrak-t7h', label: 'HOWO SITRAK T7H' },
        { path: '/products/may-dien-380', label: 'MÁY ĐIỆN 380' },
        { path: '/products/sitrak', label: 'SITRAK' },
        { path: '/products/so-mi-ro-moc', label: 'SƠ MI RƠ MOÓC' },
      ],
    },
    {
      path: '/products',
      label: 'PHỤ TÙNG BỘ PHẬN',
      dropdown: [
        { path: '/products/bo-phan-ly-hop', label: 'BỘ PHẬN LY HỢP' },
        { path: '/products/cabin-than-vo', label: 'CABIN & THÂN VỎ' },
        { path: '/products/dong-co', label: 'ĐỘNG CƠ' },
        { path: '/products/he-thong-cau', label: 'HỆ THỐNG CẦU' },
        { path: '/products/he-thong-dien', label: 'HỆ THỐNG ĐIỆN' },
        { path: '/products/he-thong-hut-xa', label: 'HỆ THỐNG HÚT XẢ' },
        { path: '/products/he-thong-lai', label: 'HỆ THỐNG LÁI' },
        { path: '/products/he-thong-phanh', label: 'HỆ THỐNG PHANH' },
        { path: '/products/hop-so', label: 'HỘP SỐ' },
      ],
    },
    {
      path: '/products',
      label: 'DỊCH VỤ & HẬU MÃI',
      dropdown: [
        { path: '/about#warranty', label: 'CHÍNH SÁCH BẢO HÀNH' },
        { path: '/about#supply', label: 'CHÍNH SÁCH CUNG CẤP' },
        { path: '/about#payment', label: 'PHƯƠNG THỨC THANH TOÁN' },
      ],
    },
    { path: '/catalog', label: 'CATALOG' },
    { path: '/image-library', label: 'THƯ VIỆN ẢNH' },
    { path: '/contact', label: 'LIÊN HỆ' },
  ]

  const isActive = (path) => {
    if (path === '/') {
      return location.pathname === '/'
    }
    return location.pathname.startsWith(path)
  }

  return (
    <header ref={headerRef} className={`header ${isScrolled ? 'scrolled' : ''}`}>
      <div className="header-top">
        <div className="container">
          <div className="header-top-content">
            <div className="logo" ref={logoRef}>
              <Link to="/">
                <span className="logo-icon">🚛</span>
                <span className="logo-text">
                  <span className="logo-brand">SINOTRUK</span>
                  <span className="logo-location">HÀ NỘI</span>
                </span>
              </Link>
            </div>

            <div className="header-info">
              <div className="info-item">
                <Phone className="info-icon" />
                <div className="info-content">
                  <div className="info-label">HOTLINE</div>
                  <div className="info-value">0382.890.990</div>
                  <div className="info-note">Các ngày trong tuần</div>
                </div>
              </div>

              <div className="info-item">
                <Truck className="info-icon" />
                <div className="info-content">
                  <div className="info-label">GIAO HÀNG NHANH</div>
                  <div className="info-note">Thanh toán linh hoạt</div>
                </div>
              </div>

              <div className="info-item">
                <Shield className="info-icon" />
                <div className="info-content">
                  <div className="info-label">BẢO HÀNH HẬU MÃI</div>
                  <div className="info-note">Đổi mới 100%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <nav className="header-nav">
        <div className="container">
          <div className="nav-content">
            <button
              className="mobile-menu-toggle"
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              aria-label="Toggle menu"
            >
              {isMobileMenuOpen ? <X /> : <Menu />}
            </button>

            <ul className={`nav-menu ${isMobileMenuOpen ? 'open' : ''}`}>
              {menuItems.map((item, index) => (
                <li
                  key={index}
                  className={`nav-item ${isActive(item.path) ? 'active' : ''} ${
                    item.dropdown ? 'has-dropdown' : ''
                  }`}
                  onMouseEnter={() => item.dropdown && setActiveDropdown(index)}
                  onMouseLeave={() => setActiveDropdown(null)}
                >
                  <Link to={item.path} className="nav-link">
                    {item.label}
                  </Link>
                  {item.dropdown && (
                    <ul
                      className={`dropdown-menu ${
                        activeDropdown === index ? 'show' : ''
                      }`}
                    >
                      {item.dropdown.map((subItem, subIndex) => (
                        <li key={subIndex}>
                          <Link to={subItem.path} className="dropdown-link">
                            {subItem.label}
                          </Link>
                        </li>
                      ))}
                    </ul>
                  )}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </nav>
    </header>
  )
}

export default Header