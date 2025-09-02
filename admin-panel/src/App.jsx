import { BrowserRouter, Routes, Route } from 'react-router-dom';
import DashboardHome from './pages/DashboardHome';
import AccountsPage from './pages/AccountsPage';
// import باقي الصفحات

function App() {
  return (
    // <BrowserRouter>
    //   <Routes>
    //     <Route path="/" element={<DashboardHome />} />
    //     <Route path="/accounts" element={<AccountsPage />} />
    //     {/* باقي الراوتس */}
    //   </Routes>
    // </BrowserRouter>
    <div className="app-container">
      <h1>مرحبًا بك في لوحة التحكم</h1>
      <p>هذه هي الصفحة الرئيسية للوحة التحكم.</p>
      {/* يمكنك إضافة المزيد من المحتوى هنا */}
    </div>
  );
}

export default App;
