import AdminLayout from '../layouts/AdminLayout';
import UserList from '../features/users/UserList';

const AccountsPage = () => (
  <AdminLayout>
    <h1 className="text-2xl font-bold mb-4">إدارة الحسابات</h1>
    <UserList />
  </AdminLayout>
);

export default AccountsPage;
