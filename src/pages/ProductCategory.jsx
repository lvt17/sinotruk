import { useParams } from 'react-router-dom'
import Products from './Products'

const ProductCategory = () => {
  const { category } = useParams()

  // Map URL params to category names
  const categoryMap = {
    'howo-a7': 'HOWO A7',
    'howo-ben': 'HOWO BEN',
    'howo-sitrak-t7h': 'SITRAK T7H',
    'may-dien-380': 'MÁY ĐIỆN 380',
    'sitrak': 'SITRAK',
    'so-mi-ro-moc': 'SƠ MI RƠ MOÓC',
    'bo-phan-ly-hop': 'BỘ PHẬN LY HỢP',
    'cabin-than-vo': 'CABIN & THÂN VỎ',
    'dong-co': 'ĐỘNG CƠ',
    'he-thong-cau': 'HỆ THỐNG CẦU',
    'he-thong-dien': 'HỆ THỐNG ĐIỆN',
    'he-thong-hut-xa': 'HỆ THỐNG HÚT XẢ',
    'he-thong-lai': 'HỆ THỐNG LÁI',
    'he-thong-phanh': 'HỆ THỐNG PHANH',
    'hop-so': 'HỘP SỐ',
  }

  return <Products category={categoryMap[category] || category} />
}

export default ProductCategory


