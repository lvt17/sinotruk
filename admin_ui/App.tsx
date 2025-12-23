import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { NotificationProvider } from './src/components/shared/Notification';
import Sidebar from './components/Sidebar';
import Dashboard from './pages/Dashboard';
import Products from './pages/Products';
import Orders from './pages/Orders';
import Customers from './pages/Customers';
import Reports from './pages/Reports';
import './styles/global.css';

function App() {
  return (
    <NotificationProvider>
      <Router>
        <div className="admin-layout">
          <Sidebar />
          <main className="admin-main">
            <Routes>
              <Route path="/" element={<Navigate to="/dashboard" />} />
              <Route path="/dashboard" element={<Dashboard />} />
              <Route path="/products" element={<Products />} />
              <Route path="/orders" element={<Orders />} />
              <Route path="/customers" element={<Customers />} />
              <Route path="/reports" element={<Reports />} />
            </Routes>
          </main>
        </div>
      </Router>
    </NotificationProvider>
  );
}

export default App;
