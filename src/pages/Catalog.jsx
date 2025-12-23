import { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { getCatalogs } from '../services/supabase'

// Fallback catalogs
const fallbackCatalogs = [
  {
    id: 1,
    title: 'Catalog HOWO A7',
    description: 'Phụ tùng đầy đủ cho dòng xe HOWO A7',
    pages: 156,
    file_url: null
  },
  {
    id: 2,
    title: 'Catalog SITRAK T7H',
    description: 'Phụ tùng chính hãng SITRAK T7H',
    pages: 124,
    file_url: null
  },
  {
    id: 3,
    title: 'Catalog HOWO Ben',
    description: 'Phụ tùng cho xe ben HOWO các loại',
    pages: 98,
    file_url: null
  },
  {
    id: 4,
    title: 'Catalog Sơ Mi Rơ Moóc',
    description: 'Phụ tùng sơ mi rơ moóc CIMC',
    pages: 76,
    file_url: null
  },
]

const Catalog = () => {
  const [catalogs, setCatalogs] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const loadCatalogs = async () => {
      try {
        const data = await getCatalogs()
        setCatalogs(data.length > 0 ? data : fallbackCatalogs)
      } catch (err) {
        console.error('Error loading catalogs:', err)
        setCatalogs(fallbackCatalogs)
      } finally {
        setLoading(false)
      }
    }
    loadCatalogs()
  }, [])

  const displayCatalogs = catalogs.length > 0 ? catalogs : fallbackCatalogs

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
        {loading ? (
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {[1, 2, 3, 4].map((i) => (
              <div key={i} className="bg-white border border-slate-200 rounded-3xl p-8 animate-pulse">
                <div className="w-16 h-16 bg-slate-200 rounded-2xl mb-6"></div>
                <div className="h-6 bg-slate-200 rounded w-3/4 mb-2"></div>
                <div className="h-4 bg-slate-200 rounded w-full mb-4"></div>
                <div className="h-4 bg-slate-200 rounded w-1/2"></div>
              </div>
            ))}
          </div>
        ) : (
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {displayCatalogs.map((catalog, i) => (
              <motion.div
                key={catalog.id || i}
                initial={{ y: 30, opacity: 0 }}
                whileInView={{ y: 0, opacity: 1 }}
                transition={{ delay: i * 0.1 }}
                viewport={{ once: true }}
                whileHover={{ y: -5 }}
                className="group bg-white border border-slate-200 rounded-3xl p-8 hover:border-primary/50 transition-all cursor-pointer shadow-sm hover:shadow-lg"
              >
                <div className="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all">
                  <span className="material-symbols-outlined text-4xl">picture_as_pdf</span>
                </div>
                <h3 className="text-slate-800 font-bold text-xl mb-2 group-hover:text-primary transition-colors">
                  {catalog.title}
                </h3>
                <p className="text-slate-400 text-sm mb-4">{catalog.description}</p>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600 text-xs">{catalog.pages} trang</span>
                  {catalog.file_url ? (
                    <a
                      href={catalog.file_url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="flex items-center gap-1 text-primary text-sm font-bold group-hover:translate-x-1 transition-transform"
                    >
                      Tải xuống
                      <span className="material-symbols-outlined text-sm">download</span>
                    </a>
                  ) : (
                    <span className="flex items-center gap-1 text-slate-400 text-sm font-bold">
                      Liên hệ
                      <span className="material-symbols-outlined text-sm">call</span>
                    </span>
                  )}
                </div>
              </motion.div>
            ))}
          </div>
        )}

        {/* Coming Soon Notice */}
        <div className="mt-16 text-center p-12 bg-white border border-slate-200 rounded-3xl shadow-sm">
          <span className="material-symbols-outlined text-6xl text-primary mb-4">call</span>
          <h3 className="text-slate-800 text-2xl font-bold mb-2">Hotline: 0382.890.990</h3>
          <p className="text-slate-500">
            Liên hệ để nhận catalog đầy đủ hoặc tư vấn phụ tùng theo nhu cầu
          </p>
        </div>
      </div>
    </div>
  )
}

export default Catalog