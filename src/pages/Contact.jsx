import { useRef, useState } from 'react'
import { MapPin, Phone, Mail, Send } from 'lucide-react'
import './Contact.css'

const Contact = () => {
  const sectionRef = useRef(null)
  const formRef = useRef(null)
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    message: '',
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    // Handle form submission
    console.log('Form submitted:', formData)
    alert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.')
    setFormData({ name: '', phone: '', email: '', message: '' })
  }

  return (
    <div ref={sectionRef} className="contact-page">
      <div className="contact-header">
        <div className="container">
          <h1 className="page-title">LIÊN HỆ</h1>
          <p className="page-subtitle">
            Chúng tôi luôn sẵn sàng hỗ trợ bạn
          </p>
        </div>
      </div>

      <div className="container">
        <div className="contact-content">
          <div className="contact-info">
            <div className="info-card">
              <MapPin className="info-icon" />
              <h3>Địa chỉ</h3>
              <p>
                Thôn 1, Xã Lại Yên, Hoài Đức, Hà Nội
                <br />
                (Cách cầu vượt An Khánh 300m)
              </p>
            </div>

            <div className="info-card">
              <Phone className="info-icon" />
              <h3>Hotline 24/7</h3>
              <p>
                <a href="tel:0382890990">0382.890.990</a>
              </p>
            </div>

            <div className="info-card">
              <Mail className="info-icon" />
              <h3>Email</h3>
              <p>
                <a href="mailto:hnsinotruk@gmail.com">hnsinotruk@gmail.com</a>
              </p>
            </div>
          </div>

          <div ref={formRef} className="contact-form-wrapper">
            <h2>Gửi tin nhắn cho chúng tôi</h2>
            <form className="contact-form" onSubmit={handleSubmit}>
              <div className="form-group">
                <label htmlFor="name">Họ và tên *</label>
                <input
                  type="text"
                  id="name"
                  required
                  value={formData.name}
                  onChange={(e) =>
                    setFormData({ ...formData, name: e.target.value })
                  }
                />
              </div>

              <div className="form-group">
                <label htmlFor="phone">Số điện thoại *</label>
                <input
                  type="tel"
                  id="phone"
                  required
                  value={formData.phone}
                  onChange={(e) =>
                    setFormData({ ...formData, phone: e.target.value })
                  }
                />
              </div>

              <div className="form-group">
                <label htmlFor="email">Email</label>
                <input
                  type="email"
                  id="email"
                  value={formData.email}
                  onChange={(e) =>
                    setFormData({ ...formData, email: e.target.value })
                  }
                />
              </div>

              <div className="form-group">
                <label htmlFor="message">Tin nhắn *</label>
                <textarea
                  id="message"
                  rows="5"
                  required
                  value={formData.message}
                  onChange={(e) =>
                    setFormData({ ...formData, message: e.target.value })
                  }
                ></textarea>
              </div>

              <button type="submit" className="btn-submit">
                <Send className="btn-icon" />
                Gửi tin nhắn
              </button>
            </form>
          </div>
        </div>

        <div className="map-wrapper">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.123456789!2d105.1234567!3d21.1234567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjHCsDA3JzI0LjQiTiAxMDXCsDA3JzI0LjQiRQ!5e0!3m2!1svi!2s!4v1234567890123!5m2!1svi!2s"
            width="100%"
            height="450"
            style={{ border: 0, borderRadius: 'var(--radius-lg)' }}
            allowFullScreen=""
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
            title="SINOTRUK HÀ NỘI"
          ></iframe>
        </div>
      </div>
    </div>
  )
}

export default Contact


