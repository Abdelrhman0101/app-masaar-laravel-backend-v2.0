@extends('layouts.dashboard')

@section('title', 'إدارة التصاريح الأمنية')

@section('content')
<h2 class="mb-4">إدارة التصاريح الأمنية</h2>
<div id="permitsContent"></div>

<!-- Modal لعرض الصور -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <img id="imgModalSrc" src="" style="max-width:98vw;max-height:85vh;display:block;margin:auto;">
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const baseUrl = window.location.origin; // تم التعديل: استخدام أصل الموقع لتجنب Mixed Content
let permits = [];
let pollingInterval = null;

function openImageModal(src, title = 'عرض الصورة') {
    // إزالة أي نافذة سابقة
    const existingModal = document.getElementById('dynamicImageModal');
    if (existingModal) {
        existingModal.remove();
    }

    // إنشاء النافذة المنبثقة
    const modalHTML = `
        <div class="modal fade" id="dynamicImageModal" tabindex="-1" aria-labelledby="dynamicImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamicImageModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${src}" class="img-fluid" alt="${title}" style="max-height: 70vh; border-radius: 8px;">
                    </div>
                </div>
            </div>
        </div>
    `;

    // إضافة النافذة إلى الصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // عرض النافذة
    const modal = new bootstrap.Modal(document.getElementById('dynamicImageModal'));
    modal.show();

    // إزالة النافذة عند الإغلاق
    document.getElementById('dynamicImageModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

function openImgFull(src) {
    openImageModal(src, 'صورة المستند');
}

async function fetchPermits(auto = false) {
    const token = localStorage.getItem('token');
    if (!auto) {
        document.getElementById('permitsContent').innerHTML = `<div class="text-center py-5"><div class="spinner-border"></div></div>`;
    }
    try {
        const res = await fetch(`${baseUrl}/api/all-security-permits`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        permits = Array.isArray(data.permits) ? data.permits : [];
        renderPermits();
    } catch (e) {
        if (!auto) {
            document.getElementById('permitsContent').innerHTML = `<div class="alert alert-danger">تعذر تحميل التصاريح!</div>`;
        }
    }
}

function renderPermits() {
    if (!permits.length) {
        document.getElementById('permitsContent').innerHTML = `<div class="alert alert-warning">لا توجد تصاريح حتى الآن.</div>`;
        return;
    }
    let html = `<div class="row g-4">`;
    permits.forEach(permit => {
        html += `<div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden" style="border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)'">
                <div class="position-absolute top-0 end-0 m-3">
                    <div class="badge bg-gradient" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); font-size: 0.9rem; padding: 8px 12px; border-radius: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">#${permit.id}</div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3 mt-2">
                        <div class="me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-passport text-primary" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1 text-dark fw-bold text-center">مصري</h5>
                        </div>
                    </div>
                    
                    <!-- تاريخ السفر في وسط البطاقة -->
                    <div class="text-center mb-3 p-3 bg-gradient rounded-3" style="background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%); border: 2px solid #dee2e6;">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-calendar-alt text-primary me-2" style="font-size: 1.2rem;"></i>
                            <span class="fw-bold text-dark" style="font-size: 1.1rem;">تاريخ السفر</span>
                        </div>
                        <div class="text-primary fw-bold" style="font-size: 1.3rem;">${permit.travel_date}</div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <div class="text-primary fw-bold" style="font-size: 1.5rem;">${permit.people_count}</div>
                                <div class="text-muted small">عدد الأشخاص</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <div class="text-success fw-bold" style="font-size: 0.9rem;">${getPermitStatusLabel(permit.status)}</div>
                                <div class="text-muted small">الحالة</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-warning me-2"></i>
                            <span class="fw-semibold text-dark">قادمين من:</span>
                        </div>
                        <div class="ps-3 text-muted">${permit.coming_from}</div>
                    </div>
                    
                    ${permit.notes ? `<div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-sticky-note text-info me-2"></i>
                            <span class="fw-semibold text-dark">ملاحظات:</span>
                        </div>
                        <div class="ps-3 text-muted small bg-light rounded-3 p-2">${permit.notes}</div>
                    </div>` : ''}
                    
                    <div class="border-top pt-3 mb-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-images text-primary me-2"></i>المستندات المرفقة</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="small text-muted mb-2">جواز السفر</div>
                                    ${permit.passport_image ? `<div class="position-relative d-inline-block">
                                        <img src="${permit.passport_image}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover; cursor: pointer; border-radius: 8px; transition: transform 0.2s ease;" onclick="openImgFull('${permit.passport_image}')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        <div class="position-absolute top-0 end-0 bg-success rounded-circle" style="width: 15px; height: 15px; margin: -5px;"></div>
                                    </div>` : '<div class="text-muted small bg-light rounded-3 p-2">لا يوجد</div>'}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="small text-muted mb-2">مستند آخر</div>
                                    ${permit.other_document_image ? `<div class="position-relative d-inline-block">
                                        <img src="${permit.other_document_image}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover; cursor: pointer; border-radius: 8px; transition: transform 0.2s ease;" onclick="openImgFull('${permit.other_document_image}')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        <div class="position-absolute top-0 end-0 bg-success rounded-circle" style="width: 15px; height: 15px; margin: -5px;"></div>
                                    </div>` : '<div class="text-muted small bg-light rounded-3 p-2">لا يوجد</div>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-user text-success me-2"></i>بيانات صاحب التصريح</h6>
                        <div class="bg-light rounded-3 p-3">
                            <div class="row g-2 small">
                                <div class="col-12"><i class="fas fa-user me-2 text-primary"></i><span class="fw-semibold">${permit.user?.name ?? '-'}</span></div>
                                ${permit.user?.phone ? `<div class="col-12"><i class="fas fa-phone me-2 text-success"></i>${permit.user.phone}</div>` : ''}
                                ${permit.user?.email ? `<div class="col-12"><i class="fas fa-envelope me-2 text-info"></i>${permit.user.email}</div>` : ''}
                                ${permit.user?.governorate ? `<div class="col-12"><i class="fas fa-map-marker-alt me-2 text-warning"></i>${permit.user.governorate}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-2 border-top">
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                <i class="fas fa-plus-circle me-1"></i>
                                <span>أنشئ في:</span><br>
                                <span class="fw-semibold">${formatDateTime(permit.created_at)}</span>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-edit me-1"></i>
                                <span>آخر تحديث:</span><br>
                                <span class="fw-semibold">${formatDateTime(permit.updated_at)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    });
    html += `</div>`;
    document.getElementById('permitsContent').innerHTML = html;
}

function getPermitStatusLabel(status) {
    switch (status) {
        case 'new': return `<span class="badge bg-secondary">جديد</span>`;
        case 'approved': return `<span class="badge bg-success">مقبول</span>`;
        case 'rejected': return `<span class="badge bg-danger">مرفوض</span>`;
        default: return `<span class="badge bg-light">${status}</span>`;
    }
}

function formatDateTime(dt) {
    if (!dt) return '-';
    return new Date(dt).toLocaleString('ar-EG', { dateStyle: 'short', timeStyle: 'short' });
}

function startPolling() {
    // تحديث البيانات كل ثانية (1000 مللي ثانية)
    pollingInterval = setInterval(() => {
        fetchPermits(true); // auto = true لتجنب رسائل التحميل
    }, 1000);
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// تشغيل عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    fetchPermits(); // تحميل أولي
    startPolling(); // بدء التحديث التلقائي
});

// إيقاف التحديث عند مغادرة الصفحة
window.addEventListener('beforeunload', function() {
    stopPolling();
});
</script>
@endsection
