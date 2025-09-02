import { Link } from "react-router-dom";

export default function Sidebar() {
  return (
    <aside className="w-56 bg-white shadow flex flex-col p-4">
      <h2 className="text-xl font-bold mb-8">لوحة التحكم</h2>
      <nav className="flex flex-col gap-3">
        <Link to="/accounts" className="text-gray-800 hover:text-blue-700">الحسابات</Link>
        {/* أضف باقي روابط الأقسام هنا */}
      </nav>
    </aside>
  );
}
