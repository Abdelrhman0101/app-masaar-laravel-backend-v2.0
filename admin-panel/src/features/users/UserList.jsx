import { useEffect, useState } from "react";
import { fetchUsers } from "../../services/usersApi";

const UserList = () => {
  const [users, setUsers] = useState([]);
  const [selected, setSelected] = useState(null);

  useEffect(() => {
    const token = localStorage.getItem('adminToken'); // حسب تخزين التوكن عندك
    fetchUsers(token).then(res => setUsers(res.data.users));
  }, []);

  return (
    <div>
      <table className="min-w-full border">
        <thead>
          <tr>
            <th className="border px-3 py-2">#</th>
            <th className="border px-3 py-2">الاسم</th>
            <th className="border px-3 py-2">البريد</th>
            <th className="border px-3 py-2">النوع</th>
            <th className="border px-3 py-2">عمليات</th>
          </tr>
        </thead>
        <tbody>
          {users.map((u, i) => (
            <tr key={u.id}>
              <td className="border px-3 py-2">{i+1}</td>
              <td className="border px-3 py-2">{u.name}</td>
              <td className="border px-3 py-2">{u.email}</td>
              <td className="border px-3 py-2">{u.user_type}</td>
              <td className="border px-3 py-2">
                <button
                  className="px-2 py-1 bg-blue-600 text-white rounded"
                  onClick={() => setSelected(u)}
                >
                  تفاصيل
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {selected && (
        <div className="mt-6 border rounded p-4 bg-gray-50">
          <h2 className="font-semibold text-lg mb-2">تفاصيل الحساب</h2>
          <pre className="text-xs">{JSON.stringify(selected, null, 2)}</pre>
          {/* هنا مستقبلاً تعرض تفاصيل متقسمة وجميلة */}
          <div className="mt-2 flex gap-2">
            {/* زر قبول الحساب */}
            <button className="bg-green-600 px-3 py-1 text-white rounded">
              قبول
            </button>
            {/* زر رفض أو حذف الحساب */}
            <button className="bg-red-600 px-3 py-1 text-white rounded">
              حذف
            </button>
            <button
              className="bg-gray-400 px-3 py-1 text-white rounded"
              onClick={() => setSelected(null)}
            >
              إغلاق
            </button>
          </div>
        </div>
      )}
    </div>
  );
};
export default UserList;
