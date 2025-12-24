// Admin Supabase Service - connects to Supabase database
import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://irncljhvsjtohiqllnsv.supabase.co';
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY || '';

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

// Types
export interface Product {
    id: number;
    code: string;
    name: string;
    category_id: number | null;
    category_ids?: number[];
    vehicle_ids?: number[];
    image: string | null;
    thumbnail?: string | null;
    description: string | null;
    show_on_homepage?: boolean;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    code?: string;
    thumbnail?: string;
    is_vehicle_name?: boolean;
    is_visible?: boolean;
    created_at: string;
    updated_at: string;
}

export interface CatalogArticle {
    id: number;
    title: string;
    slug: string;
    content: object; // EditorJS JSON
    thumbnail?: string;
    is_published: boolean;
    created_at: string;
    updated_at: string;
}

// Product Service
export const productService = {
    getAll: async (params?: { cursor?: number; limit?: number; category?: string; search?: string }) => {
        let query = supabase
            .from('products')
            .select('*')
            .order('id', { ascending: true });

        if (params?.limit) {
            query = query.limit(params.limit);
        } else {
            query = query.limit(20);
        }

        if (params?.cursor) {
            query = query.gt('id', params.cursor);
        }

        if (params?.category && params.category !== 'ALL') {
            query = query.eq('category_id', parseInt(params.category));
        }

        if (params?.search) {
            query = query.or(`name.ilike.%${params.search}%,code.ilike.%${params.search}%`);
        }

        const { data, error } = await query;
        if (error) throw error;
        return data || [];
    },

    getById: async (id: number) => {
        const { data, error } = await supabase
            .from('products')
            .select('*')
            .eq('id', id)
            .single();
        if (error) throw error;
        return data;
    },

    create: async (product: Omit<Product, 'id' | 'created_at' | 'updated_at'>) => {
        const { data, error } = await supabase
            .from('products')
            .insert([product])
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    update: async (id: number, product: Partial<Product>) => {
        const { data, error } = await supabase
            .from('products')
            .update(product)
            .eq('id', id)
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    delete: async (id: number) => {
        const { error } = await supabase
            .from('products')
            .delete()
            .eq('id', id);
        if (error) throw error;
    }
};

// Category Service
export const categoryService = {
    getAll: async () => {
        const { data, error } = await supabase
            .from('categories')
            .select('*')
            .order('name');
        if (error) throw error;
        return data || [];
    },

    create: async (category: { name: string; code?: string; thumbnail?: string; is_vehicle_name?: boolean; is_visible?: boolean }) => {
        const { data, error } = await supabase
            .from('categories')
            .insert([category])
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    update: async (id: number, category: { name?: string; code?: string; thumbnail?: string; is_vehicle_name?: boolean; is_visible?: boolean }) => {
        const { data, error } = await supabase
            .from('categories')
            .update(category)
            .eq('id', id)
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    delete: async (id: number) => {
        const { error } = await supabase
            .from('categories')
            .delete()
            .eq('id', id);
        if (error) throw error;
    }
};

// Catalog Article Service
export const catalogService = {
    getAll: async (publishedOnly: boolean = false) => {
        let query = supabase
            .from('catalog_articles')
            .select('*')
            .order('created_at', { ascending: false });

        if (publishedOnly) {
            query = query.eq('is_published', true);
        }

        const { data, error } = await query;
        if (error) throw error;
        return data || [];
    },

    getById: async (id: number) => {
        const { data, error } = await supabase
            .from('catalog_articles')
            .select('*')
            .eq('id', id)
            .single();
        if (error) throw error;
        return data;
    },

    getBySlug: async (slug: string) => {
        const { data, error } = await supabase
            .from('catalog_articles')
            .select('*')
            .eq('slug', slug)
            .single();
        if (error) throw error;
        return data;
    },

    create: async (article: { title: string; slug: string; content: object; thumbnail?: string; is_published?: boolean }) => {
        const { data, error } = await supabase
            .from('catalog_articles')
            .insert([article])
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    update: async (id: number, article: { title?: string; slug?: string; content?: object; thumbnail?: string; is_published?: boolean }) => {
        const { data, error } = await supabase
            .from('catalog_articles')
            .update({ ...article, updated_at: new Date().toISOString() })
            .eq('id', id)
            .select()
            .single();
        if (error) throw error;
        return data;
    },

    delete: async (id: number) => {
        const { error } = await supabase
            .from('catalog_articles')
            .delete()
            .eq('id', id);
        if (error) throw error;
    }
};

export default {
    products: productService,
    categories: categoryService,
    catalogs: catalogService,
    supabase
};
