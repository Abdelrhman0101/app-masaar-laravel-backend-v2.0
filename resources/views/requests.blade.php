@extends('layouts.dashboard')

@section('title', 'إدارة الطلبات')

@section('content')
<style>
/* تصميم حديث للجدول */
.modern-requests-table-container {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    margin: 20px 0;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-requests-table {
    margin: 0;
    border: none;
    background: transparent;
    text-align: center;
}

.modern-requests-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ff8c42 100%);
    position: relative;
    overflow: hidden;
}

.modern-requests-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmerHeader 3s infinite;
}

@keyframes shimmerHeader {
    0% { left: -100%; }
    100% { left: 100%; }
}

.modern-requests-th {
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 20px 15px;
    border: none;
    font-size: 14px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    position: relative;
    z-index: 1;
}

.modern-requests-th i {
    color: rgba(255,255,255,0.9);
    font-size: 16px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.modern-requests-body tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    animation: rowSlideIn 0.6s ease-out;
}

@keyframes rowSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-requests-body tr:hover {
    background: linear-gradient(135deg, rgba(255,107,53,0.05) 0%, rgba(247,147,30,0.05) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,107,53,0.15);
}

.modern-requests-body tr.status-updated {
    animation: statusUpdate 1s ease-in-out;
}

@keyframes statusUpdate {
    0% { background-color: rgba(40,167,69,0.2); }
    50% { background-color: rgba(40,167,69,0.3); transform: scale(1.01); }
    100% { background-color: transparent; transform: scale(1); }
}

.modern-requests-body td {
    padding: 18px 15px;
    border: none;
    vertical-align: middle;
    font-size: 14px;
    color: #2c3e50;
    position: relative;
}

.modern-requests-body td:first-child {
    font-weight: 700;
    color: #ff6b35;
    font-size: 16px;
    text-align: center;
}

.modern-requests-body td img {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.8);
}

.modern-requests-body td img:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.modern-requests-body .badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
}

.badge.bg-success { background: linear-gradient(135deg, #28a745, #20c997) !important; }
.badge.bg-danger { background: linear-gradient(135deg, #dc3545, #e74c3c) !important; }
.badge.bg-secondary { background: linear-gradient(135deg, #6c757d, #495057) !important; }
.badge.bg-warning { background: linear-gradient(135deg, #ffc107, #fd7e14) !important; }

.modern-requests-body .btn {
    border-radius: 20px;
    padding: 8px 20px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
    margin: 2px;
}

.modern-requests-body .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.modern-requests-body .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.modern-requests-body .btn:hover::before {
    left: 100%;
}

.modern-requests-body .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
}

.modern-requests-body .btn-success:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40,167,69,0.4);
    background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
}

.modern-requests-body .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
    border-color: #dc3545;
}

.modern-requests-body .btn-danger:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(220,53,69,0.4);
    background: linear-gradient(135deg, #e74c3c 0%, #dc3545 100%);
}

/* Modern Cards Styles */
.modern-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
    padding: 20px;
}

.modern-rent-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 1px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.modern-rent-card.status-updated {
    animation: cardStatusUpdate 1s ease-in-out;
}

@keyframes cardStatusUpdate {
    0% { box-shadow: 0 0 0 0 rgba(40,167,69,0.7); }
    50% { box-shadow: 0 0 0 10px rgba(40,167,69,0.3); transform: scale(1.02); }
    100% { box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); transform: scale(1); }
}

.modern-rent-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff6b35, #f7931e, #ff6b35);
    background-size: 200% 100%;
    animation: gradientShift 3s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.modern-rent-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 8px 16px rgba(0, 0, 0, 0.1);
}

.modern-card-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.modern-card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmerFlow 3s infinite;
}

@keyframes shimmerFlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.card-header-content {
    display: flex;
    gap: 15px;
    align-items: center;
    z-index: 2;
    position: relative;
}

.card-id-badge,
.card-type-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 8px 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.card-id-badge:hover,
.card-type-badge:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.card-id-badge i,
.card-type-badge i {
    font-size: 0.8rem;
    opacity: 0.9;
}

.card-status {
    z-index: 2;
    position: relative;
}

.modern-card-body {
    padding: 25px;
}

.card-info-section {
    display: grid;
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(248, 250, 252, 0.8) 100%);
    border-radius: 15px;
    border: 1px solid rgba(255, 107, 53, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #ff6b35, #f7931e);
    transition: width 0.3s ease;
}

.info-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.2);
}

.info-item:hover::before {
    width: 8px;
}

.info-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    transition: all 0.3s ease;
}

.info-item:hover .info-icon {
    transform: rotate(5deg) scale(1.1);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.info-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: #1e293b;
    font-weight: 600;
}

.card-data-section {
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.05) 0%, rgba(247, 147, 30, 0.05) 100%);
    border-radius: 15px;
    border: 1px solid rgba(255, 107, 53, 0.1);
}

.data-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    color: #ff6b35;
    font-weight: 600;
    font-size: 1rem;
}

.data-header i {
    font-size: 1.1rem;
    animation: iconPulse 2s infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.data-grid {
    display: grid;
    gap: 10px;
}

.data-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 10px;
    border: 1px solid rgba(255, 107, 53, 0.1);
    transition: all 0.3s ease;
}

.data-item:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(255, 107, 53, 0.15);
}

.data-key {
    font-weight: 600;
    color: #475569;
    font-size: 0.9rem;
}

.data-value {
    color: #1e293b;
    font-weight: 500;
    text-align: right;
}

.card-actions-section {
    padding-top: 20px;
    border-top: 2px solid rgba(255, 107, 53, 0.1);
    display: flex;
    justify-content: center;
    gap: 10px;
}

.modern-alert {
    padding: 20px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    margin: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: alertSlideIn 0.5s ease-out;
}

@keyframes alertSlideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-alert-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.modern-alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
    border: 1px solid #ef4444;
}

.modern-alert i {
    font-size: 1.2rem;
    animation: iconPulse 2s infinite;
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    margin-left: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* تحسينات للشاشات الصغيرة */
@media (max-width: 768px) {
    .modern-requests-table-container {
        border-radius: 15px;
        margin: 10px 0;
    }

    .modern-requests-th {
        padding: 15px 8px;
        font-size: 12px;
    }

    .modern-requests-body td {
        padding: 12px 8px;
        font-size: 12px;
    }

    .modern-requests-body td img {
        max-width: 60px !important;
        max-height: 45px !important;
    }

    .modern-requests-body .btn {
        padding: 6px 12px;
        font-size: 10px;
    }

    .modern-cards-container {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
    }

    .modern-card-header {
        padding: 15px;
    }

    .modern-card-body {
        padding: 20px;
    }

    .card-header-content {
        gap: 10px;
    }

    .card-id-badge,
    .card-type-badge {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .card-actions-section {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .modern-cards-container {
        padding: 10px;
        gap: 15px;
    }

    .modern-rent-card {
        border-radius: 15px;
    }

    .modern-card-header {
        padding: 12px;
    }

    .modern-card-body {
        padding: 15px;
    }

    .card-header-content {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }

    .info-item {
        padding: 12px;
    }

    .info-icon {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }

    .card-data-section {
        padding: 15px;
    }
}

/* تأثيرات إضافية للتفاعل */
.modern-requests-table-container:hover {
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.table-responsive {
    border-radius: 20px;
}

/* تحسين شكل التبويبات */
.nav-tabs {
    border: none;
    margin-bottom: 30px;
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
    margin: 0 5px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #6c757d;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.nav-tabs .nav-link:hover {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,107,53,0.3);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(255,107,53,0.3);
}

/* تحسين العنوان الرئيسي */
h2.mb-4 {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* تحسينات إضافية */
.btn-processing {
    position: relative;
    pointer-events: none;
}

.btn-processing::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.status-badge-pending { background: linear-gradient(135deg, #6c757d, #495057) !important; }
.status-badge-approved { background: linear-gradient(135deg, #28a745, #20c997) !important; }
.status-badge-rejected { background: linear-gradient(135deg, #dc3545, #e74c3c) !important; }
.status-badge-processing { background: linear-gradient(135deg, #007bff, #0056b3) !important; }
</style>

<h2 class="mb-4">إدارة الطلبات</h2>
<div class="mb-3">
    <ul class="nav nav-tabs" id="ordersTabs">
        <li class="nav-item">
            <button class="nav-link active" id="tab-appointments" onclick="switchTab('appointments')">
                مواعيد المعاينة 
                <span class="badge bg-light text-dark ms-2" id="appointments-count">0</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-rents" onclick="switchTab('rents')">
                طلبات التأجير
                <span class="badge bg-light text-dark ms-2" id="rents-count">0</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-other" onclick="switchTab('other')">
                طلبات أخرى
                <span class="badge bg-light text-dark ms-2" id="other-count">0</span>
            </button>
        </li>
    </ul>
</div>

<div id="ordersTabContent" class="mt-4"></div>

<!-- Modal للعرض أو الرد -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderModalContent"></div>
            <div class="modal-footer" id="orderModalFooter"></div>
        </div>
    </div>
</div>

<!-- Toast للإشعارات -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-bell text-primary me-2"></i>
            <strong class="me-auto">إشعار</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toast-body"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentTab = 'appointments';
let appointments = [];
let rents = [];
let loading = false;
let pollingInterval = null;
let processingRequests = new Set(); // تتبع الطلبات قيد المعالجة
const baseUrl = 'https://msar.app';

// دالة إظهار التوست
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('toast');
    const toastBody = document.getElementById('toast-body');
    const toastHeader = toastEl.querySelector('.toast-header i');
    
    toastBody.textContent = message;
    
    // تغيير أيقونة ولون التوست حسب النوع
    toastHeader.className = `fas me-2 ${type === 'success' ? 'fa-check-circle text-success' : 
                                     type === 'error' ? 'fa-exclamation-triangle text-danger' : 
                                     'fa-info-circle text-info'}`;
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

function switchTab(tab) {
    if (loading) return; // منع التبديل أثناء التحميل
    
    currentTab = tab;
    document.querySelectorAll('#ordersTabs .nav-link').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    
    // مسح المحتوى السابق
    document.getElementById('ordersTabContent').innerHTML = '';
    renderCurrentTab();
}

function renderCurrentTab(auto = false) {
    if (currentTab === 'appointments') {
        fetchAppointments(auto);
    } else if (currentTab === 'rents') {
        fetchRents(auto);
    } else {
        document.getElementById('ordersTabContent').innerHTML = 
            "<div class='modern-alert modern-alert-warning'><i class='fas fa-info-circle'></i> لا يوجد محتوى بعد.</div>";
        updateTabCount('other', 0);
    }
}

// ----------- Tab 1: مواعيد المعاينة -----------
async function fetchAppointments(auto = false) {
    if (loading) return;
    loading = true;
    
    const token = localStorage.getItem('token');
    const container = document.getElementById('ordersTabContent');
    
    // إظهار مؤشر التحميل فقط في المرة الأولى
    if (!auto && !container.innerHTML) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-3 text-muted">جاري تحميل المواعيد...</p>
            </div>
        `;
    }

    try {
        const res = await fetch(`${baseUrl}/api/appointments`, {
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            }
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        let newAppointments = Array.isArray(data.appointments) ? data.appointments : [];
        
        // ترتيب المواعيد من الأحدث للأقدم
        newAppointments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        renderAppointments(newAppointments);
        appointments = newAppointments;
        updateTabCount('appointments', newAppointments.length);
        
    } catch (e) {
        console.error('خطأ في تحميل المواعيد:', e);
        if (!auto) {
            container.innerHTML = `
                <div class="modern-alert modern-alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    تعذر تحميل المواعيد! الرجاء المحاولة مرة أخرى.
                </div>
            `;
        }
        updateTabCount('appointments', 0);
    }
    loading = false;
}

function renderAppointments(newAppointments) {
    const container = document.getElementById('ordersTabContent');
    
    if (!newAppointments.length) {
        container.innerHTML = `
            <div class="modern-alert modern-alert-warning">
                <i class="fas fa-calendar-times"></i> 
                لا توجد مواعيد معاينة حالياً.
            </div>
        `;
        return;
    }

    let tableContainer = container.querySelector('.modern-requests-table-container');
    
    // إنشاء الجدول إذا لم يكن موجوداً
    if (!tableContainer) {
        let html = `
            <div class="table-responsive modern-requests-table-container">
                <table class="table table-bordered align-middle modern-requests-table">
                    <thead class="modern-requests-header">
                        <tr>
                            <th class="modern-requests-th"><i class="bi bi-hash me-2"></i>#</th>
                            <th class="modern-requests-th"><i class="bi bi-house me-2"></i>العقار</th>
                            <th class="modern-requests-th"><i class="bi bi-person me-2"></i>العميل</th>
                            <th class="modern-requests-th"><i class="bi bi-tools me-2"></i>مقدم الخدمة</th>
                            <th class="modern-requests-th"><i class="bi bi-calendar-event me-2"></i>الموعد</th>
                            <th class="modern-requests-th" data-cell="status"><i class="bi bi-flag me-2"></i>الحالة</th>
                            <th class="modern-requests-th"><i class="bi bi-chat-text me-2"></i>ملاحظات</th>
                            <th class="modern-requests-th" data-cell="actions"><i class="bi bi-gear me-2"></i>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="modern-requests-body"></tbody>
                </table>
            </div>
        `;
        container.innerHTML = html;
        tableContainer = container.querySelector('.modern-requests-table-container');
    }

    const tableBody = container.querySelector('.modern-requests-body');
    const newIds = new Set(newAppointments.map(app => app.id));

    // إزالة الصفوف القديمة التي لم تعد موجودة
    tableBody.querySelectorAll('tr[data-id]').forEach(row => {
        const rowId = parseInt(row.dataset.id, 10);
        if (!newIds.has(rowId)) {
            row.remove();
        }
    });

    // تحديث وإضافة الصفوف الجديدة
    newAppointments.forEach((app, index) => {
        let row = tableBody.querySelector(`tr[data-id='${app.id}']`);
        const existingApp = appointments.find(a => a.id === app.id);
        const hasChanged = !existingApp || existingApp.status !== app.status;

        const rowHtml = `
            <td>${app.id}</td>
            <td>
                <div class="fw-bold text-primary">${app.property?.type || 'غير محدد'}</div>
                <div class="small text-muted">العنوان: ${app.property?.address || 'غير محدد'}</div>
                ${app.property?.image_url ? 
                    `<img src="${app.property.image_url}" style="max-width:80px;max-height:60px;margin-top:5px;" 
                     class="rounded shadow-sm" onerror="this.style.display='none'" alt="صورة العقار">` : 
                    '<div class="text-muted small">لا توجد صورة</div>'
                }
            </td>
            <td>
                <div class="fw-semibold">${app.customer?.name || 'غير محدد'}</div>
                <div class="small text-muted">${app.customer?.phone || 'غير محدد'}</div>
            </td>
            <td>
                <div class="fw-semibold">${app.provider?.name || 'غير محدد'}</div>
                <div class="small text-muted">${app.provider?.phone || 'غير محدد'}</div>
            </td>
            <td>
                <div class="fw-semibold">${formatDateTime(app.appointment_datetime)}</div>
            </td>
            <td data-cell="status">${getStatusLabel(app.status)}</td>
            <td>
                <div class="small">${app.note || 'لا توجد ملاحظات'}</div>
                ${app.admin_note ? `<div class="small text-success mt-1">${app.admin_note}</div>` : ''}
            </td>
            <td data-cell="actions">${renderAppointmentActions(app)}</td>
        `;

        if (!row) { // صف جديد
            const newRow = document.createElement('tr');
            newRow.dataset.id = app.id;
            newRow.innerHTML = rowHtml;
            
            // إدراج الصف في الموضع الصحيح (مرتب حسب التاريخ)
            if (index === 0) {
                tableBody.insertBefore(newRow, tableBody.firstChild);
            } else {
                const prevRow = tableBody.querySelector(`tr[data-id='${newAppointments[index-1].id}']`);
                if (prevRow && prevRow.nextSibling) {
                    tableBody.insertBefore(newRow, prevRow.nextSibling);
                } else {
                    tableBody.appendChild(newRow);
                }
            }
            
            // تأثير الظهور
            newRow.style.opacity = '0';
            setTimeout(() => {
                newRow.style.opacity = '1';
            }, 100);
            
        } else if (hasChanged) { // تحديث صف موجود
            row.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(app.status);
            row.querySelector('[data-cell="actions"]').innerHTML = renderAppointmentActions(app);
            
            // تأثير التحديث
            row.classList.add('status-updated');
            setTimeout(() => row.classList.remove('status-updated'), 1000);
        }
    });
}

// ----------- Tab 2: طلبات التأجير -----------
async function fetchRents(auto = false) {
    if (loading) return;
    loading = true;
    
    const token = localStorage.getItem('token');
    const container = document.getElementById('ordersTabContent');
    
    if (!auto && !container.innerHTML) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-3 text-muted">جاري تحميل طلبات التأجير...</p>
            </div>
        `;
    }

    try {
        const res = await fetch(`${baseUrl}/api/admin/service-requests/all`, {
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            }
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        const allRequests = Array.isArray(data.all_requests) ? data.all_requests : [];
        let newRents = allRequests.filter(r => r.type === 'rent');
        
        // ترتيب الطلبات من الأحدث للأقدم
        newRents.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        renderRents(newRents);
        rents = newRents;
        updateTabCount('rents', newRents.length);
        
    } catch (e) {
        console.error('خطأ في تحميل طلبات التأجير:', e);
        if (!auto) {
            container.innerHTML = `
                <div class="modern-alert modern-alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    تعذر تحميل طلبات التأجير! الرجاء المحاولة مرة أخرى.
                </div>
            `;
        }
        updateTabCount('rents', 0);
    }
    loading = false;
}

function renderRents(newRents) {
    const container = document.getElementById('ordersTabContent');
    
    if (!newRents.length) {
        container.innerHTML = `
            <div class="modern-alert modern-alert-warning">
                <i class="fas fa-home"></i> 
                لا توجد طلبات تأجير حالياً.
            </div>
        `;
        return;
    }

    let cardsContainer = container.querySelector('.modern-cards-container');
    
    // إنشاء حاوية الكروت إذا لم تكن موجودة
    if (!cardsContainer) {
        container.innerHTML = `<div class="modern-cards-container"></div>`;
        cardsContainer = container.querySelector('.modern-cards-container');
    }

    const newIds = new Set(newRents.map(r => r.id));

    // إزالة الكروت القديمة
    cardsContainer.querySelectorAll('.modern-rent-card[data-id]').forEach(card => {
        const cardId = parseInt(card.dataset.id, 10);
        if (!newIds.has(cardId)) {
            card.remove();
        }
    });

    // تحديث وإضافة الكروت الجديدة
    newRents.forEach((rent, index) => {
        let card = cardsContainer.querySelector(`.modern-rent-card[data-id='${rent.id}']`);
        const existingRent = rents.find(r => r.id === rent.id);
        const hasChanged = !existingRent || existingRent.status !== rent.status;

        const cardHtml = `
            <div class="modern-card-header">
                <div class="card-header-content">
                    <div class="card-id-badge">
                        <i class="fas fa-hashtag"></i>
                        <span>${rent.id}</span>
                    </div>
                    <div class="card-type-badge">
                        <i class="fas fa-tag"></i>
                        <span>${rent.type === 'rent' ? 'تأجير' : rent.type}</span>
                    </div>
                </div>
                <div class="card-status" data-cell="status">${getStatusLabel(rent.status)}</div>
            </div>
            <div class="modern-card-body">
                <div class="card-info-section">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-user"></i></div>
                        <div class="info-content">
                            <span class="info-label">المستخدم</span>
                            <span class="info-value">${rent.user?.name || 'غير محدد'}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-content">
                            <span class="info-label">رقم الهاتف</span>
                            <span class="info-value">${rent.user?.phone || 'غير محدد'}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <span class="info-label">المحافظة</span>
                            <span class="info-value">${rent.governorate || 'غير محدد'}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-calendar"></i></div>
                        <div class="info-content">
                            <span class="info-label">تاريخ الطلب</span>
                            <span class="info-value">${formatDateTime(rent.created_at)}</span>
                        </div>
                    </div>
                </div>
                ${Object.keys(rent.request_data || {}).length > 0 ? `
                <div class="card-data-section">
                    <div class="data-header">
                        <i class="fas fa-info-circle"></i>
                        <span>تفاصيل الطلب</span>
                    </div>
                    <div class="data-grid">
                        ${Object.entries(rent.request_data || {}).map(([key, value]) => `
                            <div class="data-item">
                                <span class="data-key">${translateDataKey(key)}</span>
                                <span class="data-value">${value}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>` : ''}
                <div class="card-actions-section" data-cell="actions">
                    ${renderRentActions(rent)}
                </div>
            </div>
        `;

        if (!card) { // كارت جديد
            const newCard = document.createElement('div');
            newCard.className = 'modern-rent-card';
            newCard.dataset.id = rent.id;
            newCard.innerHTML = cardHtml;
            
            // إدراج الكارت في الموضع الصحيح
            if (index === 0) {
                cardsContainer.insertBefore(newCard, cardsContainer.firstChild);
            } else {
                const prevCard = cardsContainer.querySelector(`[data-id='${newRents[index-1].id}']`);
                if (prevCard && prevCard.nextSibling) {
                    cardsContainer.insertBefore(newCard, prevCard.nextSibling);
                } else {
                    cardsContainer.appendChild(newCard);
                }
            }
            
            // تأثير الظهور
            newCard.style.opacity = '0';
            newCard.style.transform = 'translateY(20px)';
            setTimeout(() => {
                newCard.style.opacity = '1';
                newCard.style.transform = 'translateY(0)';
            }, 100);
            
        } else if (hasChanged) { // تحديث كارت موجود
            card.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(rent.status);
            card.querySelector('[data-cell="actions"]').innerHTML = renderRentActions(rent);
            
            // تأثير التحديث
            card.classList.add('status-updated');
            setTimeout(() => card.classList.remove('status-updated'), 1000);
        }
    });
}

// --- دوال المساعدة ---
function updateTabCount(tabName, count) {
    const countElement = document.getElementById(`${tabName}-count`);
    if (countElement) {
        countElement.textContent = count;
        countElement.className = count > 0 ? 'badge bg-warning text-dark ms-2' : 'badge bg-light text-muted ms-2';
    }
}

function formatDateTime(dateTime) {
    if (!dateTime) return 'غير محدد';
    try {
        const date = new Date(dateTime);
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateTime;
    }
}

function translateDataKey(key) {
    const translations = {
        'property_type': 'نوع العقار',
        'budget': 'الميزانية',
        'location': 'الموقع',
        'rooms': 'عدد الغرف',
        'area': 'المساحة',
        'floor': 'الدور',
        'furnished': 'مفروش',
        'parking': 'موقف سيارة',
        'balcony': 'بلكونة',
        'elevator': 'أسانسير',
        'notes': 'ملاحظات'
    };
    return translations[key] || key;
}

function getStatusLabel(status) {
    const statusMap = {
        'pending': { 
            text: 'قيد المراجعة', 
            class: 'bg-secondary',
            icon: 'fas fa-clock'
        },
        'admin_approved': { 
            text: 'تمت الموافقة', 
            class: 'bg-success',
            icon: 'fas fa-check-circle'
        },
        'approved': { 
            text: 'تمت الموافقة', 
            class: 'bg-success',
            icon: 'fas fa-check-circle'
        },
        'admin_rejected': { 
            text: 'مرفوض', 
            class: 'bg-danger',
            icon: 'fas fa-times-circle'
        },
        'rejected': { 
            text: 'مرفوض', 
            class: 'bg-danger',
            icon: 'fas fa-times-circle'
        }
    };
    
    const statusInfo = statusMap[status] || { 
        text: status, 
        class: 'bg-light text-dark',
        icon: 'fas fa-question-circle'
    };
    
    return `<span class="badge ${statusInfo.class}">
        <i class="${statusInfo.icon} me-1"></i>
        ${statusInfo.text}
    </span>`;
}

function renderAppointmentActions(app) {
    const isProcessing = processingRequests.has(`appointment-${app.id}`);
    
    if (app.status === 'pending' || isProcessing) { // Show buttons if pending or processing
        return `
            <button type="button" 
                    class="btn btn-success btn-sm ${isProcessing ? 'btn-processing' : ''}" 
                    onclick="updateAppointmentStatus(${app.id}, 'admin_approved')"
                    ${isProcessing ? 'disabled' : ''}>
                ${isProcessing ? '' : '<i class="fas fa-check me-1"></i>قبول'}
            </button>
            <button type="button" 
                    class="btn btn-danger btn-sm ${isProcessing ? 'btn-processing' : ''}" 
                    onclick="updateAppointmentStatus(${app.id}, 'admin_rejected')"
                    ${isProcessing ? 'disabled' : ''}>
                ${isProcessing ? '' : '<i class="fas fa-times me-1"></i>رفض'}
            </button>
        `;
    }
    
    const statusInfo = {
        'admin_approved': { text: 'تمت الموافقة', icon: 'fas fa-check-circle', class: 'text-success' },
        'admin_rejected': { text: 'مرفوض', icon: 'fas fa-times-circle', class: 'text-danger' }
    }[app.status] || { text: 'تم التعامل معه', icon: 'fas fa-info-circle', class: 'text-muted' };
    
    return `<span class="${statusInfo.class}">
        <i class="${statusInfo.icon} me-1"></i>
        ${statusInfo.text}
    </span>`;
}

function renderRentActions(rent) {
    const isProcessing = processingRequests.has(`rent-${rent.id}`);
    
    if (rent.status === 'pending' || isProcessing) { // Show buttons if pending or processing
        return `
            <button type="button" 
                    class="btn btn-success btn-sm ${isProcessing ? 'btn-processing' : ''}" 
                    onclick="approveRent(${rent.id})"
                    ${isProcessing ? 'disabled' : ''}>
                ${isProcessing ? '' : '<i class="fas fa-check me-1"></i>قبول'}
            </button>
            <button type="button" 
                    class="btn btn-danger btn-sm ${isProcessing ? 'btn-processing' : ''}" 
                    onclick="rejectRent(${rent.id})"
                    ${isProcessing ? 'disabled' : ''}>
                ${isProcessing ? '' : '<i class="fas fa-times me-1"></i>رفض'}
            </button>
        `;
    }
    
    const statusInfo = {
        'approved': { text: 'تمت الموافقة', icon: 'fas fa-check-circle', class: 'text-success' },
        'rejected': { text: 'مرفوض', icon: 'fas fa-times-circle', class: 'text-danger' }
    }[rent.status] || { text: 'تم التعامل معه', icon: 'fas fa-info-circle', class: 'text-muted' };
    
    return `<span class="${statusInfo.class}">
        <i class="${statusInfo.icon} me-1"></i>
        ${statusInfo.text}
    </span>`;
}

// --- دوال معالجة الطلبات ---
async function updateAppointmentStatus(id, status) {
    const actionKey = `appointment-${id}`;
    if (processingRequests.has(actionKey)) return;
    
    processingRequests.add(actionKey);
    
    // 1. Optimistic UI: Disable buttons and show spinner
    const row = document.querySelector(`tr[data-id='${id}']`);
    if (row) {
        const acceptButton = row.querySelector('.btn-success');
        const rejectButton = row.querySelector('.btn-danger');
        if (acceptButton) {
            acceptButton.classList.add('btn-processing');
            acceptButton.setAttribute('disabled', 'true');
            acceptButton.innerHTML = ''; // Remove text, leave spinner
        }
        if (rejectButton) {
            rejectButton.classList.add('btn-processing');
            rejectButton.setAttribute('disabled', 'true');
            rejectButton.innerHTML = ''; // Remove text, leave spinner
        }
    }

    const token = localStorage.getItem('token');
    const admin_note = status === 'admin_approved' ? 
        'تمت الموافقة على الموعد من الإدارة' : 
        'تم رفض الموعد من الإدارة';
    
    try {
        const res = await fetch(`${baseUrl}/api/appointments/${id}/status`, {
            method: 'PUT',
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json', 
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({ status, admin_note })
        });
        
        if (res.ok) {
            // 3. Update the local data model
            const updatedApp = appointments.find(a => a.id === id);
            if (updatedApp) {
                updatedApp.status = status;
                updatedApp.admin_note = admin_note;
            }
            
            // 2. Explicitly update the DOM for the *final* state after successful API call
            if (row) {
                row.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(status);
                // Pass a temporary object to renderAppointmentActions with the new status
                row.querySelector('[data-cell="actions"]').innerHTML = renderAppointmentActions({ id: id, status: status }); 
                row.classList.add('status-updated');
                setTimeout(() => row.classList.remove('status-updated'), 1000);
            }
            
            showToast(
                status === 'admin_approved' ? 
                'تم قبول الموعد بنجاح' : 
                'تم رفض الموعد بنجاح', 
                'success'
            );
        } else {
            // Revert UI if API call failed
            if (row) {
                // Find the original status from the local array before the optimistic update
                const originalApp = appointments.find(a => a.id === id);
                const originalStatus = originalApp ? originalApp.status : 'pending'; 
                row.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(originalStatus);
                row.querySelector('[data-cell="actions"]').innerHTML = renderAppointmentActions({ id: id, status: originalStatus });
                row.classList.remove('status-updated'); // Remove animation class if error
            }
            throw new Error('فشل في تحديث الحالة');
        }
    } catch (e) {
        console.error('خطأ في تحديث حالة الموعد:', e);
        showToast('حدث خطأ أثناء تحديث الحالة! الرجاء المحاولة مرة أخرى.', 'error');
    } finally {
        processingRequests.delete(actionKey);
        // 4. No need to call renderAppointments(appointments) here anymore.
        // The polling mechanism will ensure data consistency.
    }
}

async function approveRent(id) {
    const actionKey = `rent-${id}`;
    if (processingRequests.has(actionKey)) return;
    
    processingRequests.add(actionKey);

    // 1. Optimistic UI: Disable buttons and show spinner
    const card = document.querySelector(`.modern-rent-card[data-id='${id}']`);
    if (card) {
        const approveButton = card.querySelector('.btn-success');
        const rejectButton = card.querySelector('.btn-danger');
        if (approveButton) {
            approveButton.classList.add('btn-processing');
            approveButton.setAttribute('disabled', 'true');
            approveButton.innerHTML = '';
        }
        if (rejectButton) {
            rejectButton.classList.add('btn-processing');
            rejectButton.setAttribute('disabled', 'true');
            rejectButton.innerHTML = '';
        }
    }
    
    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${baseUrl}/api/admin/service-requests/${id}/approve`, {
            method: 'POST',
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json', 
                'Content-Type': 'application/json' 
            }
        });
        
        if (res.ok) {
            // 3. Update the local data model
            const updatedRent = rents.find(r => r.id === id);
            if (updatedRent) {
                updatedRent.status = 'approved';
            }
            
            // 2. Explicitly update the DOM for the final state
            if (card) {
                card.querySelector('[data-cell="status"]').innerHTML = getStatusLabel('approved');
                card.querySelector('[data-cell="actions"]').innerHTML = renderRentActions({ id: id, status: 'approved' });
                card.classList.add('status-updated');
                setTimeout(() => card.classList.remove('status-updated'), 1000);
            }
            
            showToast('تم قبول طلب التأجير بنجاح', 'success');
        } else {
            // Revert UI if API call failed
            if (card) {
                const originalRent = rents.find(r => r.id === id);
                const originalStatus = originalRent ? originalRent.status : 'pending';
                card.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(originalStatus);
                card.querySelector('[data-cell="actions"]').innerHTML = renderRentActions({ id: id, status: originalStatus });
                card.classList.remove('status-updated');
            }
            throw new Error('فشل في قبول الطلب');
        }
    } catch (e) {
        console.error('خطأ في قبول طلب التأجير:', e);
        showToast('خطأ أثناء قبول الطلب! الرجاء المحاولة مرة أخرى.', 'error');
    } finally {
        processingRequests.delete(actionKey);
        // 4. No need to call renderRents(rents) here.
    }
}

async function rejectRent(id) {
    const actionKey = `rent-${id}`;
    if (processingRequests.has(actionKey)) return;
    
    if (!confirm('هل أنت متأكد من رفض هذا الطلب؟ لن يتمكن من استعادته.')) {
        return;
    }
    
    processingRequests.add(actionKey);

    // 1. Optimistic UI: Disable buttons and show spinner
    const card = document.querySelector(`.modern-rent-card[data-id='${id}']`);
    if (card) {
        const approveButton = card.querySelector('.btn-success');
        const rejectButton = card.querySelector('.btn-danger');
        if (approveButton) {
            approveButton.classList.add('btn-processing');
            approveButton.setAttribute('disabled', 'true');
            approveButton.innerHTML = '';
        }
        if (rejectButton) {
            rejectButton.classList.add('btn-processing');
            rejectButton.setAttribute('disabled', 'true');
            rejectButton.innerHTML = '';
        }
    }
    
    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${baseUrl}/api/admin/service-requests/${id}/reject`, {
            method: 'POST',
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json', 
                'Content-Type': 'application/json' 
            }
        });
        
        if (res.ok) {
            // 3. Update the local data model (remove the item)
            const rentIndex = rents.findIndex(r => r.id === id);
            if (rentIndex !== -1) {
                rents.splice(rentIndex, 1);
            }
            
            // 2. Explicitly remove/hide the card from the DOM
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 500); // Animate out and then remove
            }
            
            updateTabCount('rents', rents.length);
            showToast('تم رفض طلب التأجير بنجاح', 'success');
        } else {
            // Revert UI if API call failed
            if (card) {
                const originalRent = rents.find(r => r.id === id);
                const originalStatus = originalRent ? originalRent.status : 'pending';
                card.querySelector('[data-cell="status"]').innerHTML = getStatusLabel(originalStatus);
                card.querySelector('[data-cell="actions"]').innerHTML = renderRentActions({ id: id, status: originalStatus });
                card.classList.remove('status-updated');
            }
            throw new Error('فشل في رفض الطلب');
        }
    } catch (e) {
        console.error('خطأ في رفض طلب التأجير:', e);
        showToast('خطأ أثناء رفض الطلب! الرجاء المحاولة مرة أخرى.', 'error');
    } finally {
        processingRequests.delete(actionKey);
        // 4. No need to call renderRents(rents) here.
    }
}

// ---------- دوال التحديث التلقائي ----------
function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);
    
    pollingInterval = setInterval(() => {
        if (!loading && processingRequests.size === 0) {
            renderCurrentTab(true); // true = تحديث تلقائي صامت
        }
    }, 10000); // كل 10 ثوانِ
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// ---------- معالجة الأحداث ----------
document.addEventListener('DOMContentLoaded', function() {
    // التحقق من وجود التوكن
    const token = localStorage.getItem('token');
    if (!token) {
        showToast('لم يتم العثور على رمز التفويض، الرجاء تسجيل الدخول مرة أخرى.', 'error');
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
        return;
    }
    
    // بدء التشغيل
    switchTab('appointments');
    startPolling();
});

// إيقاف التحديث التلقائي عند إغلاق النافذة
window.addEventListener('beforeunload', function() {
    stopPolling();
});

// إيقاف التحديث التلقائي عند فقدان التركيز
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPolling();
    } else {
        startPolling();
        // تحديث فوري عند العودة للصفحة
        if (!loading) {
            renderCurrentTab(true);
        }
    }
});

// معالجة أخطاء الشبكة
window.addEventListener('online', function() {
    showToast('تم استعادة الاتصال بالإنترنت', 'success');
    if (!loading) {
        renderCurrentTab(true);
    }
});

window.addEventListener('offline', function() {
    showToast('تم فقدان الاتصال بالإنترنت', 'error');
    stopPolling();
});

// دالة لإعادة تحميل البيانات يدوياً
function refreshData() {
    if (loading) {
        showToast('جاري تحميل البيانات بالفعل...', 'info');
        return;
    }
    
    showToast('جاري تحديث البيانات...', 'info');
    renderCurrentTab(false);
}

// إضافة زر التحديث لكل تبويب
function addRefreshButton() {
    const tabsContainer = document.getElementById('ordersTabs');
    if (tabsContainer && !document.getElementById('refresh-btn')) {
        const refreshBtn = document.createElement('li');
        refreshBtn.className = 'nav-item ms-auto';
        refreshBtn.innerHTML = `
            <button class="nav-link border-0 bg-transparent" id="refresh-btn" onclick="refreshData()" title="تحديث البيانات">
                <i class="fas fa-sync-alt"></i>
                تحديث
            </button>
        `;
        tabsContainer.appendChild(refreshBtn);
    }
}

// تشغيل دالة إضافة زر التحديث عند تحميل الصفحة
setTimeout(addRefreshButton, 100);

</script>
@endsection