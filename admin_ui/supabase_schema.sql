-- Supabase PostgreSQL Schema for Admin UI
-- Run this in Supabase SQL Editor

-- Users table (admin accounts)
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL DEFAULT 'Admin',
    phone VARCHAR(50),
    avatar TEXT,
    is_admin BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Insert default admin user
INSERT INTO admin_users (username, password, full_name, phone, is_admin) 
VALUES ('admin', 'admin123', 'Administrator', '0382.890.990', true)
ON CONFLICT (username) DO NOTHING;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    code VARCHAR(255) UNIQUE,
    name VARCHAR(255) NOT NULL,
    price BIGINT DEFAULT 0,
    price_bulk BIGINT DEFAULT 0,
    total INT DEFAULT 0,
    category_id INT REFERENCES categories(id),
    image TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Insert default categories
INSERT INTO categories (name) VALUES 
    ('CABIN'),
    ('ĐỘNG CƠ'),
    ('LY HỢP'),
    ('HỘP SỐ'),
    ('PHANH'),
    ('ĐIỆN'),
    ('GẦM'),
    ('THỦY LỰC')
ON CONFLICT (name) DO NOTHING;

-- Function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers for updated_at
DROP TRIGGER IF EXISTS update_admin_users_updated_at ON admin_users;
CREATE TRIGGER update_admin_users_updated_at
    BEFORE UPDATE ON admin_users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_categories_updated_at ON categories;
CREATE TRIGGER update_categories_updated_at
    BEFORE UPDATE ON categories
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_products_updated_at ON products;
CREATE TRIGGER update_products_updated_at
    BEFORE UPDATE ON products
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
