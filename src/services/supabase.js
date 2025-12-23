import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://irncljhvsjtohiqllnsv.supabase.co';
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY || '';

console.log('Supabase URL:', supabaseUrl);
console.log('Supabase Key exists:', !!supabaseAnonKey);

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

export const getProducts = async (limit = 12) => {
    const { data, error } = await supabase
        .from('products')
        .select('*')
        .order('name')
        .limit(limit);

    if (error) {
        console.error('Error fetching products:', error);
        return [];
    }
    return data || [];
};

export const getCategories = async () => {
    const { data, error } = await supabase
        .from('categories')
        .select('*')
        .order('name');

    if (error) {
        console.error('Error fetching categories:', error);
        return [];
    }
    return data || [];
};

export const getCatalogs = async () => {
    const { data, error } = await supabase
        .from('catalogs')
        .select('*')
        .order('id');

    if (error) {
        console.error('Error fetching catalogs:', error);
        return [];
    }
    return data || [];
};
