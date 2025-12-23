
import React from 'react';

export const AboutSection: React.FC = () => {
  return (
    <section className="relative py-32 overflow-hidden bg-fixed bg-center bg-cover" style={{ backgroundImage: 'url("https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=2000")' }}>
      <div className="absolute inset-0 bg-black/85"></div>

      <div className="relative z-10 container mx-auto px-4 text-center max-w-4xl">
        <h2 className="text-primary font-bold text-lg mb-6 tracking-[0.4em] uppercase">Về Chúng Tôi</h2>
        <h3 className="text-white text-4xl md:text-6xl font-bold mb-8 leading-tight tracking-tight">Phụ Tùng Chính Hãng <br /> Chất Lượng Hàng Đầu</h3>
        <p className="text-gray-300 text-lg md:text-xl mb-16 leading-relaxed">
          Với hơn 15 năm kinh nghiệm trong ngành, chúng tôi tự hào là đơn vị cung cấp phụ tùng xe tải SINOTRUK hàng đầu tại Việt Nam.
          Tất cả sản phẩm đều là phụ tùng chính hãng, được nhập khẩu trực tiếp và có đầy đủ chứng từ.
          Cam kết giá tốt nhất, giao hàng nhanh chóng toàn quốc.
        </p>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-12">
          {[
            { label: 'Chính Hãng 100%', icon: 'verified_user', desc: 'Phụ tùng nhập khẩu trực tiếp' },
            { label: 'Bảo Hành Uy Tín', icon: 'workspace_premium', desc: 'Đổi trả nếu không đúng mẫu' },
            { label: 'Giá Cạnh Tranh', icon: 'savings', desc: 'Chiết khấu cao cho đại lý' }
          ].map((item, i) => (
            <div key={i} className="group cursor-pointer">
              <div className="w-20 h-20 mx-auto rounded-3xl bg-primary/10 flex items-center justify-center border border-primary/30 text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-xl group-hover:shadow-primary/40 group-hover:-translate-y-2">
                <span className="material-symbols-outlined text-4xl">{item.icon}</span>
              </div>
              <p className="text-white font-bold text-lg">{item.label}</p>
              <p className="text-gray-500 text-sm mt-2">{item.desc}</p>
            </div>
          ))}
        </div>
      </div>

      {/* Dynamic background element */}
      <div className="absolute top-1/2 left-0 -translate-y-1/2 w-96 h-96 bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
    </section>
  );
};
