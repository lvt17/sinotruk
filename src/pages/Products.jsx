import { useState, useEffect } from 'react'
import { useSearchParams } from 'react-router-dom'
import { Search, Filter } from 'lucide-react'
import ProductCard from '../components/Product/ProductCard'
import './Products.css'

// Mock data - sẽ thay bằng API
const allProducts = [
  {
    id: 1,
    name: 'Xilanh kích cabin VX350 VX400 chính hãng',
    code: 'XLKVX',
    manufacturerCode: 'WG9525820140/2',
    image: '/images/products/xilanh-kich-cabin.jpg',
    category: 'CABIN & THÂN VỎ',
  },
  {
    id: 2,
    name: 'Tăm bét trước VGD95 SITRAK T7H',
    code: 'TBTSI.L',
    manufacturerCode: 'AZ4095410005 - AZ4095410006',
    image: '/images/products/tam-bet-truco.jpg',
    category: 'ĐỘNG CƠ',
  },
  // Thêm nhiều sản phẩm khác...
]

const Products = () => {
  const [searchParams] = useSearchParams()
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCategory, setSelectedCategory] = useState('all')
  const [filteredProducts, setFilteredProducts] = useState(allProducts)
  const [showFilters, setShowFilters] = useState(false)

  const categories = [
    'all',
    'CABIN & THÂN VỎ',
    'ĐỘNG CƠ',
    'HỘP SỐ',
    'HỆ THỐNG HÚT XẢ',
    'HT LÀM MÁT',
  ]

  useEffect(() => {
    let filtered = allProducts

    // Filter by category
    if (selectedCategory !== 'all') {
      filtered = filtered.filter((p) => p.category === selectedCategory)
    }

    // Filter by search term
    if (searchTerm) {
      filtered = filtered.filter(
        (p) =>
          p.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
          p.code.toLowerCase().includes(searchTerm.toLowerCase()) ||
          p.manufacturerCode.toLowerCase().includes(searchTerm.toLowerCase())
      )
    }

    setFilteredProducts(filtered)
  }, [searchTerm, selectedCategory])

  return (
    <div className="products-page">
      <div className="products-header">
        <div className="container">
          <h1 className="page-title">SẢN PHẨM</h1>
          <p className="page-subtitle">
            Tìm kiếm và lọc sản phẩm theo nhu cầu của bạn
          </p>
        </div>
      </div>

      <div className="container">
        <div className="products-controls">
          <div className="search-section">
            <div className="search-box">
              <Search className="search-icon" />
              <input
                type="text"
                placeholder="Tìm kiếm sản phẩm..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="search-input"
              />
              {searchTerm && (
                <span className="search-results-count">
                  {filteredProducts.length} kết quả
                </span>
              )}
            </div>
          </div>

          <button
            className="filter-toggle"
            onClick={() => setShowFilters(!showFilters)}
          >
            <Filter />
            Lọc
          </button>
        </div>

        {showFilters && (
          <div className="filters-panel">
            <div className="filter-group">
              <label>Danh mục:</label>
              <div className="filter-options">
                {categories.map((cat) => (
                  <button
                    key={cat}
                    className={`filter-btn ${
                      selectedCategory === cat ? 'active' : ''
                    }`}
                    onClick={() => setSelectedCategory(cat)}
                  >
                    {cat === 'all' ? 'Tất cả' : cat}
                  </button>
                ))}
              </div>
            </div>
          </div>
        )}

        {filteredProducts.length > 0 ? (
          <div className="products-grid">
            {filteredProducts.map((product, index) => (
              <ProductCard key={product.id} product={product} index={index} />
            ))}
          </div>
        ) : (
          <div className="no-results">
            <p>Không tìm thấy sản phẩm nào phù hợp</p>
          </div>
        )}
      </div>
    </div>
  )
}

export default Products


