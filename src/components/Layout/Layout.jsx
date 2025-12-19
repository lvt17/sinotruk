import { useState, useEffect } from 'react'
import Header from './Header'
import Footer from './Footer'
import './Layout.css'

const Layout = ({ children }) => {
  const [isScrolled, setIsScrolled] = useState(false)

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  return (
    <div className="layout">
      <Header isScrolled={isScrolled} />
      <main className="main-content">{children}</main>
      <Footer />
    </div>
  )
}

export default Layout


