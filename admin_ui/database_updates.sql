-- =====================================================
-- SINOTRUK Admin Updates - Database Schema
-- Run this in Supabase SQL Editor
-- =====================================================

-- 1. Create catalog_articles table for EditorJS content
CREATE TABLE IF NOT EXISTS catalog_articles (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  content JSONB, -- EditorJS content stored as JSON
  thumbnail VARCHAR(500),
  is_published BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- 2. Update products table
-- Remove price/stock fields, add homepage visibility and thumbnail
ALTER TABLE products 
  DROP COLUMN IF EXISTS price,
  DROP COLUMN IF EXISTS wholesale_price,
  DROP COLUMN IF EXISTS stock;

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS show_on_homepage BOOLEAN DEFAULT true,
  ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(500);

-- 3. Update categories table
-- Add code, thumbnail, visibility
ALTER TABLE categories 
  ADD COLUMN IF NOT EXISTS code VARCHAR(50),
  ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(500),
  ADD COLUMN IF NOT EXISTS is_visible BOOLEAN DEFAULT true;

-- 4. Enable RLS policies for catalog_articles
ALTER TABLE catalog_articles ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow public read for published articles" ON catalog_articles
  FOR SELECT USING (is_published = true);

CREATE POLICY "Allow all for authenticated" ON catalog_articles
  FOR ALL USING (true);

-- Done!
SELECT 'Schema updated successfully!' as status;
