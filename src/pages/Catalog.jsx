import { motion } from 'framer-motion'

const catalogs = [
  {
    title: 'Catalog HOWO A7',
    description: 'Phụ tùng đầy đủ cho dòng xe HOWO A7',
    icon: 'picture_as_pdf',
    pages: 156
  },
  {
    title: 'Catalog SITRAK T7H',
    description: 'Phụ tùng chính hãng SITRAK T7H',
    icon: 'picture_as_pdf',
    pages: 124
  },
  {
    title: 'Catalog HOWO Ben',
    description: 'Phụ tùng cho xe ben HOWO các loại',
    icon: 'picture_as_pdf',
    pages: 98
  },
  {
    title: 'Catalog Sơ Mi Rơ Moóc',
    description: 'Phụ tùng sơ mi rơ moóc CIMC',
    icon: 'picture_as_pdf',
    pages: 76
  },
]

const Catalog = () => {
  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div className="relative py-20 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-b from-primary/10 to-transparent"></div>
        <div className="container mx-auto px-4 md:px-10 lg:px-20 relative z-10">
          <motion.div
            initial={{ y: 30, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            className="text-center"
          >
            <h1 className="text-5xl md:text-7xl font-bold text-slate-800 tracking-tighter mb-4">
              CATA<span className="text-primary">LOG</span>
            </h1>
            <p className="text-slate-500 text-lg max-w-xl mx-auto">
              Tải về catalog phụ tùng chính hãng
            </p>
          </motion.div>
        </div>
      </div>

      <div className="container mx-auto px-4 md:px-10 lg:px-20 pb-20">
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {catalogs.map((catalog, i) => (
            <motion.div
              key={i}
              initial={{ y: 30, opacity: 0 }}
              whileInView={{ y: 0, opacity: 1 }}
              transition={{ delay: i * 0.1 }}
              viewport={{ once: true }}
              whileHover={{ y: -5 }}
              className="group bg-white border border-slate-200 rounded-3xl p-8 hover:border-primary/50 transition-all cursor-pointer shadow-sm hover:shadow-lg"
            >
              <div className="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all">
                <span className="material-symbols-outlined text-4xl">{catalog.icon}</span>
              </div>
              <h3 className="text-slate-800 font-bold text-xl mb-2 group-hover:text-primary transition-colors">
                {catalog.title}
              </h3>
              <p className="text-slate-400 text-sm mb-4">{catalog.description}</p>
              <div className="flex items-center justify-between">
                <span className="text-gray-600 text-xs">{catalog.pages} trang</span>
                <span className="flex items-center gap-1 text-primary text-sm font-bold group-hover:translate-x-1 transition-transform">
                  Tải xuống
                  <span className="material-symbols-outlined text-sm">download</span>
                </span>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Coming Soon Notice */}
        <div className="mt-16 text-center p-12 bg-white border border-slate-200 rounded-3xl shadow-sm">
          <span className="material-symbols-outlined text-6xl text-slate-300 mb-4">construction</span>
          <h3 className="text-slate-800 text-2xl font-bold mb-2">Đang cập nhật</h3>
          <p className="text-slate-500">
            Chúng tôi đang cập nhật thêm nhiều catalog mới. Vui lòng liên hệ hotline để nhận catalog trực tiếp.
          </p>
        </div>
      </div>
    </div>
  )
}

export default Catalog