import Hero from '../components/Home/Hero'
import ProductCategories from '../components/Home/ProductCategories'
import FeaturedProducts from '../components/Home/FeaturedProducts'
import WhyChooseUs from '../components/Home/WhyChooseUs'
import './Home.css'

const Home = () => {
  return (
    <div className="home-page">
      <Hero />
      <ProductCategories />
      <FeaturedProducts />
      <WhyChooseUs />
    </div>
  )
}

export default Home

