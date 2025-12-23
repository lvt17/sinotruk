import { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { supabase } from '../services/supabase'

// Catalog items - technical diagrams with part lists
const catalogSections = [
  {
    id: 'cooling',
    title: 'COOLING SYSTEM',
    image: '/catalog/cooling-system.png',
    parts: [
      { no: 1, partNo: '190003989315', name: 'Clamp', qty: 10 },
      { no: 2, partNo: 'WG9719530108', name: 'Hose', qty: 1 },
      { no: 3, partNo: 'WG9725530036', name: 'Radiator', qty: 1 },
    ]
  },
  {
    id: 'fuel',
    title: '350L FUEL TANK',
    image: '/catalog/fuel-tank.png',
    parts: [
      { no: 1, partNo: 'AZ9112550210', name: '350L Fuel tank', qty: 1 },
      { no: 2, partNo: 'WG9725550006', name: 'Fuel pipe', qty: 2 },
    ]
  },
  {
    id: 'airfilter',
    title: 'AIR FILTER',
    image: '/catalog/air-filter.png',
    parts: [
      { no: 1, partNo: 'WG9719190002', name: 'Supporter', qty: 1 },
      { no: 2, partNo: 'WG9100190026', name: 'Strap', qty: 2 },
    ]
  },
  {
    id: 'operation',
    title: 'OPERATION DEVICE',
    image: '/catalog/operation-device.png',
    parts: [
      { no: 1, partNo: 'WG9719570001', name: 'Rubber footboard', qty: 1 },
      { no: 2, partNo: 'AZ9719570002', name: 'Pedal', qty: 1 },
    ]
  },
  {
    id: 'suspension',
    title: 'SUSPENSION FOR ENGINE',
    image: '/catalog/suspension-engine.png',
    parts: [
      { no: 1, partNo: 'Q40308', name: 'Spring washer', qty: 4 },
      { no: 2, partNo: 'Q150B0816', name: 'Bolt', qty: 4 },
    ]
  },
  {
    id: 'oilsep',
    title: 'WD615 ENGINE SERIES OIL SEPARATOR',
    image: '/catalog/oil-separator.png',
    parts: [
      { no: 1, partNo: 'VG2600010267', name: 'Oil Separator', qty: 1 },
      { no: 2, partNo: 'VG1500019045A', name: 'Oil filter', qty: 1 },
    ]
  },
]

const Catalog = () => {
  const [catalogs, setCatalogs] = useState([])
  const [loading, setLoading] = useState(true)
  const [selectedCatalog, setSelectedCatalog] = useState(null)

  // Load catalogs from database
  useEffect(() => {
    const loadCatalogs = async () => {
      try {
        const { data, error } = await supabase
          .from('catalogs')
          .select('*')
          .order('title')

        if (!error && data && data.length > 0) {
          setCatalogs(data)
        }
      } catch (err) {
        console.error('Error loading catalogs:', err)
      } finally {
        setLoading(false)
      }
    }
    loadCatalogs()
  }, [])

  return (
    <div className="min-h-screen bg-background">
      {/* Header - same style as About */}
      <div className="relative h-[30vh] md:h-[40vh] lg:h-[50vh] overflow-hidden">
        <div className="absolute inset-0 z-0 bg-gradient-to-br from-primary/10 via-white to-sky-50" />
        <div className="absolute inset-0 z-10 flex items-center justify-center px-4">
          <motion.div
            initial={{ y: 30, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            className="text-center"
          >
            <span className="text-primary font-bold text-xs md:text-sm tracking-[0.2em] md:tracking-[0.3em] uppercase mb-2 md:mb-4 block">
              SINOTRUK HÀ NỘI
            </span>
            <h1 className="text-3xl sm:text-4xl md:text-5xl lg:text-8xl font-bold text-slate-800 tracking-tighter mb-3 md:mb-6">
              CATA<span className="text-primary">LOG</span>
            </h1>
            <p className="text-slate-600 text-sm md:text-lg max-w-xl mx-auto">
              Sơ đồ kỹ thuật và danh sách mã phụ tùng chính hãng
            </p>
          </motion.div>
        </div>
      </div>

      <div className="container mx-auto px-4 md:px-10 lg:px-20 py-8 md:py-16">
        {/* PDF Catalogs Download Section */}
        {catalogs.length > 0 && (
          <div className="mb-16">
            <h2 className="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
              <span className="material-symbols-outlined text-primary">picture_as_pdf</span>
              Tải Catalog PDF
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {catalogs.map(catalog => (
                <a
                  key={catalog.id}
                  href={catalog.file_url || '#'}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-200 hover:border-primary hover:shadow-lg transition-all group"
                >
                  <div className="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center group-hover:bg-red-100 transition-colors">
                    <span className="material-symbols-outlined text-3xl text-red-500">picture_as_pdf</span>
                  </div>
                  <div className="flex-1">
                    <h3 className="font-bold text-slate-800 group-hover:text-primary transition-colors">{catalog.title}</h3>
                    <p className="text-sm text-slate-400">{catalog.pages || 0} trang</p>
                  </div>
                  <span className="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">download</span>
                </a>
              ))}
            </div>
          </div>
        )}

        {/* Technical Parts Diagrams */}
        <div>
          <h2 className="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <span className="material-symbols-outlined text-primary">engineering</span>
            Sơ Đồ Bộ Phận Kỹ Thuật
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {catalogSections.map((section, index) => (
              <motion.div
                key={section.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 }}
                className="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition-all group cursor-pointer"
                onClick={() => setSelectedCatalog(selectedCatalog === section.id ? null : section.id)}
              >
                {/* Diagram Image */}
                <div className="aspect-square bg-gradient-to-br from-slate-50 to-slate-100 p-6 flex items-center justify-center relative">
                  <div className="w-full h-full flex items-center justify-center">
                    <span className="material-symbols-outlined text-8xl text-slate-300 group-hover:text-primary/50 transition-colors">settings</span>
                  </div>
                  <div className="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent"></div>
                </div>

                {/* Title */}
                <div className="p-4 border-t border-slate-100">
                  <h3 className="font-bold text-slate-800 uppercase tracking-wide text-sm">{section.title}</h3>
                </div>

                {/* Parts List */}
                <div className={`border-t border-slate-100 transition-all overflow-hidden ${selectedCatalog === section.id ? 'max-h-96' : 'max-h-0'}`}>
                  <div className="p-4 bg-slate-50">
                    <div className="grid grid-cols-4 gap-2 text-xs font-bold text-slate-500 uppercase mb-2 pb-2 border-b border-slate-200">
                      <span>NO.</span>
                      <span>Part No.</span>
                      <span>Part Name</span>
                      <span className="text-right">Qty</span>
                    </div>
                    {section.parts.map((part, i) => (
                      <div key={i} className="grid grid-cols-4 gap-2 text-sm py-1.5 border-b border-slate-100 last:border-0">
                        <span className="text-slate-500">{part.no}</span>
                        <span className="font-mono text-primary text-xs">{part.partNo}</span>
                        <span className="text-slate-700 truncate">{part.name}</span>
                        <span className="text-right text-slate-700">{part.qty}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>

        {/* Contact CTA */}
        <div className="mt-16 text-center p-12 bg-gradient-to-r from-primary to-sky-600 rounded-3xl shadow-lg">
          <span className="material-symbols-outlined text-6xl text-white/80 mb-4">support_agent</span>
          <h3 className="text-white text-2xl font-bold mb-2">Cần tra mã phụ tùng?</h3>
          <p className="text-white/80 mb-6">
            Liên hệ hotline để được hỗ trợ tra cứu và báo giá nhanh nhất
          </p>
          <a
            href="tel:0382890990"
            className="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary font-bold rounded-xl hover:bg-slate-100 transition-colors shadow-lg"
          >
            <span className="material-symbols-outlined">call</span>
            0382.890.990
          </a>
        </div>
      </div>
    </div>
  )
}

export default Catalog