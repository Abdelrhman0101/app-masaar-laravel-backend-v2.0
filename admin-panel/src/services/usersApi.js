import api from './api';

export const fetchUsers = (token) =>
  api.get('/users', { headers: { Authorization: `Bearer ${token}` } });

// لاحقًا أضف دوال الحذف/التفعيل هنا...
