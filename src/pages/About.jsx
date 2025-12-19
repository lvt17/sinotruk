import { useRef } from 'react'
import { Shield, Package, CreditCard } from 'lucide-react'
import './About.css'

const About = () => {
  const sectionRef = useRef(null)
  const contentRef = useRef(null)

  return (
    <div ref={sectionRef} className="about-page">
      <div className="about-header">
        <div className="container">
          <h1 className="page-title">GIỚI THIỆU</h1>
        </div>
      </div>

      <div className="container">
        <div ref={contentRef} className="about-content">
          <section className="about-section">
            <h2 className="section-title">CÔNG TY CỔ PHẦN SINOTRUK HÀ NỘI</h2>
            <div className="about-text">
              <p>
                Chúng tôi xin gửi lời cảm ơn chân thành đến Quý khách hàng đã
                tin tưởng và đồng hành cùng chúng tôi trong suốt thời gian qua.
              </p>
              <p>
                Công ty Cổ phần SINOTRUK HÀ NỘI chuyên nhập khẩu và phân phối
                phụ tùng xe tải hạng nặng, xe chuyên dụng và máy công trình từ
                Trung Quốc (HOWO/SINOTRUK, CHENGLONG). Chúng tôi cung cấp dịch
                vụ bảo hành và thay thế phụ tùng cho các dòng xe Sinotruk như:
                HOWO A7, 420, 375, 380, Howo 336-371 xe trộn bê tông/xe ben,
                và sơ mi rơ moóc CIMC.
              </p>
              <p>
                Với mong muốn trở thành đối tác tin cậy trên toàn quốc, chúng
                tôi tự hào về sự hiểu biết sâu sắc về xe tải hạng nặng Trung
                Quốc, dịch vụ chuyên nghiệp, giá cả cạnh tranh và chính sách
                bảo hành sản phẩm.
              </p>
            </div>
          </section>

          <section id="warranty" className="about-section">
            <h2 className="section-title">
              <Shield className="section-icon" />
              CHÍNH SÁCH BẢO HÀNH
            </h2>
            <div className="about-text">
              <h3>1. Chính sách bảo hành</h3>
              <ul>
                <li>Sản phẩm phải được mua tại công ty</li>
                <li>Sản phẩm phải còn nguyên tem bảo hành</li>
                <li>Sản phẩm phải trong thời hạn bảo hành từ ngày mua</li>
                <li>
                  Sản phẩm có lỗi kỹ thuật do nhà cung cấp; không bảo hành
                  các lỗi do người dùng gây ra (rơi vỡ, dùng sai...)
                </li>
              </ul>

              <h3>2. Chính sách đổi trả</h3>
              <p>
                Chúng tôi nhận đổi trả ngay lập tức nếu phụ tùng xe tải do công
                ty cung cấp không đúng mã sản phẩm, chất lượng hoặc mô tả so
                với đơn hàng của khách hàng.
              </p>
              <p>
                <strong>Thời gian đổi trả:</strong> Trong vòng 03 ngày kể từ
                ngày khách hàng nhận hàng, với điều kiện hàng hóa còn nguyên
                vẹn, chưa sử dụng.
              </p>
            </div>
          </section>

          <section id="supply" className="about-section">
            <h2 className="section-title">
              <Package className="section-icon" />
              CHÍNH SÁCH CUNG CẤP
            </h2>
            <div className="about-text">
              <p>
                Chúng tôi cam kết cung cấp sản phẩm chính hãng với chất lượng
                cao nhất. Tất cả sản phẩm đều được kiểm tra kỹ lưỡng trước khi
                giao đến tay khách hàng.
              </p>
            </div>
          </section>

          <section id="payment" className="about-section">
            <h2 className="section-title">
              <CreditCard className="section-icon" />
              PHƯƠNG THỨC THANH TOÁN
            </h2>
            <div className="about-text">
              <p>Chúng tôi chấp nhận các hình thức thanh toán sau:</p>
              <ul>
                <li>Thanh toán tiền mặt khi nhận hàng (COD)</li>
                <li>Chuyển khoản ngân hàng</li>
                <li>Thanh toán qua ví điện tử</li>
              </ul>
            </div>
          </section>
        </div>
      </div>
    </div>
  )
}

export default About


