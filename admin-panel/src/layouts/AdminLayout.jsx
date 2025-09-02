import Sidebar from '../components/Sidebar';
import Topbar from '../components/Topbar';

const AdminLayout = ({ children }) => (
  <div className="flex">
    <Sidebar />
    <div className="flex-1 flex flex-col">
      <Topbar />
      <main className="p-6">{children}</main>
    </div>
  </div>
);

export default AdminLayout;
