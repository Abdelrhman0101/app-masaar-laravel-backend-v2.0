// admin-panel/src/main.jsx

import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App.jsx';
import './index.css';

// تأكد من أن getElementById يطابق الـ id الموجود في ملف admin.blade.php
ReactDOM.createRoot(document.getElementById('admin-root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
);