@extends('layouts.dashboard')

@section('title', 'معلومات التطبيق')

@section('content')
<style>
/* Modern App Settings Styles */
.modern-settings-container {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.modern-settings-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff6b35, #f7931e, #ff6b35);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
}

.modern-title {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 2.2rem;
    text-align: center;
    margin-bottom: 30px;
    text-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    position: relative;
}

.modern-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #ff6b35, #f7931e);
    border-radius: 2px;
    box-shadow: 0 2px 10px rgba(255, 107, 53, 0.4);
}

/* Modern Tabs */
.modern-tabs {
    display: flex;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 8px;
    margin-bottom: 30px;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    flex-wrap: wrap;
    gap: 5px;
}

.modern-tab {
    flex: 1;
    min-width: 120px;
    padding: 12px 20px;
    background: transparent;
    border: none;
    border-radius: 10px;
    color: #666;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modern-tab::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.modern-tab:hover::before {
    left: 100%;
}

.modern-tab:hover {
    color: #ff6b35;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.2);
}

.modern-tab.active {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

/* Content Container */
.modern-content {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    animation: fadeInUp 0.6s ease-out;
}

/* Form Styles */
.modern-form h4 {
    color: #ff6b35;
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modern-form h4::before {
    content: '⚙️';
    font-size: 1.2rem;
}

.modern-input-group {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    animation: slideInLeft 0.5s ease-out;
}

.modern-input-group:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: rgba(255, 107, 53, 0.3);
}

.modern-input {
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 12px 15px;
    margin: 5px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    color: #333;
}

.modern-input:focus {
    outline: none;
    border-color: #ff6b35;
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    background: white;
}

.modern-input::placeholder {
    color: #999;
    font-style: italic;
}

/* Buttons */
.modern-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin: 3px;
}

.modern-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.modern-btn:hover::before {
    width: 300px;
    height: 300px;
}

.modern-btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.modern-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
}

.modern-btn-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.modern-btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.modern-btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.modern-btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
}

.modern-btn-outline {
    background: transparent;
    border: 2px solid #6c757d;
    color: #6c757d;
}

.modern-btn-outline:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.modern-btn-full {
    width: 100%;
    margin-top: 20px;
    padding: 15px;
    font-size: 1.1rem;
}

/* Banner Preview */
.modern-banner-preview {
    width: 80px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.modern-banner-preview:hover {
    transform: scale(1.1);
    border-color: #ff6b35;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Loading */
.modern-loading {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    border-radius: 10px;
    padding: 15px;
    margin-top: 15px;
    text-align: center;
    animation: pulse 2s infinite;
}

/* Animations */
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive */
@media (max-width: 768px) {
    .modern-settings-container {
        padding: 20px;
        margin: 10px;
    }
    
    .modern-title {
        font-size: 1.8rem;
    }
    
    .modern-tabs {
        flex-direction: column;
    }
    
    .modern-tab {
        min-width: auto;
        margin-bottom: 5px;
    }
    
    .modern-input-group {
        padding: 10px;
    }
    
    .modern-input {
        margin: 2px;
        padding: 10px;
    }
}
</style>

    <div class="modern-settings-container">
        <h2 class="modern-title">معلومات التطبيق - App Settings</h2>
        <div id="settingsApp">
            <ul class="modern-tabs" id="settingsTabs" dir="rtl">
                <li><button class="modern-tab active" onclick="changeSettingsTab('banners')">✨ Banners</button></li>
                <!-- ====== New Tab Added ====== -->
                <li><button class="modern-tab" onclick="changeSettingsTab('restaurantBanners')">🍔 بنرات المطاعم</button></li>
                <li><button class="modern-tab" onclick="changeSettingsTab('aboutUs')">📱 عن التطبيق</button></li>
                <li><button class="modern-tab" onclick="changeSettingsTab('termsAndConditions')">📋 الشروط والأحكام</button></li>
                <li><button class="modern-tab" onclick="changeSettingsTab('faqs')">❓ الأسئلة الشائعة</button></li>
                <li><button class="modern-tab" onclick="changeSettingsTab('socialMedia')">🌐 روابط السوشيال</button></li>
            </ul>
            <div id="settingsContent" class="modern-content"></div>
        </div>
    </div>
@endsection

@section('scripts')
<!-- Bootstrap & Icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script>
let settingsData = {};
// ====== New variable to store restaurant banners ======
let restaurantBannersData = [];
let currentSettingsTab = 'banners';

function changeSettingsTab(tab) {
    currentSettingsTab = tab;
    document.querySelectorAll('#settingsTabs .modern-tab').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    renderSettingsContent();
}

// ====== Updated to fetch both general settings and restaurant banners ======
async function fetchSettings() {
    const token = localStorage.getItem('token');
    const headers = { 
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token
    };

    // Fetch general settings
    const settingsPromise = fetch('/api/settings', { headers }).then(res => res.json());
    
    // Fetch restaurant banners
    const restaurantBannersPromise = fetch('/api/restaurant-banners', { headers }).then(res => res.json());

    const [settingsJson, bannersJson] = await Promise.all([settingsPromise, restaurantBannersPromise]);
    
    settingsData = settingsJson.settings || {};
    // Assuming the API returns an object with a "ResturantBanners" key which is an array of objects {id, image_url}
    restaurantBannersData = bannersJson.ResturantBanners || []; 
    
    renderSettingsContent();
}

// ====== Updated to render the new tab content ======
function renderSettingsContent() {
    let html = '';
    if (currentSettingsTab === 'banners') {
        html = renderBanners();
    } else if (currentSettingsTab === 'restaurantBanners') {
        html = renderRestaurantBanners();
    } else if (currentSettingsTab === 'aboutUs') {
        html = renderAboutUs();
    } else if (currentSettingsTab === 'termsAndConditions') {
        html = renderTerms();
    } else if (currentSettingsTab === 'faqs') {
        html = renderFaqs();
    } else if (currentSettingsTab === 'socialMedia') {
        html = renderSocialMedia();
    }
    document.getElementById('settingsContent').innerHTML = html;
}

// ----------- Banners Tab: رفع صور ----------- //
function renderBanners() {
    const banners = settingsData.userHomeBanners || [];
    let links = Array.isArray(banners) ? banners : Object.values(banners);
    let html = `<div class="modern-form"><h4>صور البنرات الرئيسية</h4>
        <form id="bannersForm" onsubmit="return saveBanners()">
        <div id="bannersList">`;

    links.forEach((link, i) => {
        html += `
        <div class="modern-input-group banner-row">
        <img src="${link}" alt="banner" class="modern-banner-preview">
            <input type="text" class="modern-input" name="banners[]" value="${link}" readonly>
            <a href="${link}" target="_blank" class="modern-btn modern-btn-outline" title="معاينة"><i class="bi bi-image"></i></a>
            <button type="button" class="modern-btn modern-btn-danger" onclick="removeBanner(${i})"> حذف</button>
        </div>`;
    });

    html += `
        </div>
        <div class="mb-2">
            <input type="file" id="bannerFileInput" accept="image/*" style="display:none" onchange="uploadGeneralBanner(event)">
            <button type="button" class="modern-btn modern-btn-primary" onclick="document.getElementById('bannerFileInput').click()">➕ إضافة صورة</button>
        </div>
        <button type="submit" class="modern-btn modern-btn-success modern-btn-full">💾 حفظ البنرات</button>
        </form>
        <div id="generalBannerUploadLoading" style="display:none;" class="modern-loading">جاري رفع الصورة...</div>
        </div>`;
    return html;
}

function removeBanner(i) {
    document.querySelectorAll('#bannersList .banner-row')[i].remove();
}

function saveBanners() {
    event.preventDefault();
    const token = localStorage.getItem('token');
    let banners = Array.from(document.querySelectorAll('input[name="banners[]"]')).map(i => i.value).filter(Boolean);
    fetch('/api/settings/userHomeBanners', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({ value: banners })
    }).then(res => {
        if (res.ok) {
            alert('تم حفظ البنرات بنجاح');
            fetchSettings();
        } else {
            alert('حدث خطأ أثناء الحفظ!');
        }
    });
    return false;
}

async function uploadGeneralBanner(event) {
    const fileInput = event.target;
    if (!fileInput.files || !fileInput.files.length) return;

    document.getElementById('generalBannerUploadLoading').style.display = 'block';

    const imageUrl = await uploadFile(fileInput.files[0]);

    document.getElementById('generalBannerUploadLoading').style.display = 'none';

    if (imageUrl) {
        addBannerField(imageUrl);
    } else {
        alert('حدث خطأ أثناء رفع الصورة.');
    }
    fileInput.value = '';
}

function addBannerField(link) {
    const list = document.getElementById('bannersList');
    if (list) {
        const div = document.createElement('div');
        div.className = 'modern-input-group banner-row';
        div.innerHTML = `
            <img src="${link}" alt="banner" class="modern-banner-preview">
            <input type="text" class="modern-input" name="banners[]" value="${link}" readonly>
            <a href="${link}" target="_blank" class="modern-btn modern-btn-outline" title="معاينة"><i class="bi bi-image"></i></a>
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        `;
        list.appendChild(div);
    }
}
// ----------- /Banners ----------- //


// ====== New Section for Restaurant Banners ====== //
function renderRestaurantBanners() {
    let html = `<div class="modern-form"><h4>بنرات المطاعم</h4>
        <div id="restaurantBannersList">`;

    restaurantBannersData.forEach((banner, index) => {
        // Handle both object format {id, image_url} and string format
        const bannerId = banner.id || index;
        const imageUrl = banner.image_url || banner;
        
        html += `
        <div class="modern-input-group banner-row" data-id="${bannerId}">
            <img src="${imageUrl}" alt="restaurant banner" class="modern-banner-preview">
            <input type="text" class="modern-input" value="${imageUrl}" readonly>
            <a href="${imageUrl}" target="_blank" class="modern-btn modern-btn-outline" title="معاينة"><i class="bi bi-image"></i></a>
            <button type="button" class="modern-btn modern-btn-danger" onclick="deleteRestaurantBanner(${bannerId})">🗑️ حذف</button>
        </div>`;
    });

    html += `
        </div>
        <div class="mb-2">
            <input type="file" id="restaurantBannerFileInput" accept="image/*" style="display:none" onchange="addRestaurantBanner(event)">
            <button type="button" class="modern-btn modern-btn-primary" onclick="document.getElementById('restaurantBannerFileInput').click()">➕ إضافة صورة</button>
        </div>
        <div id="restaurantBannerUploadLoading" style="display:none;" class="modern-loading">جاري رفع ومعالجة الصورة...</div>
        </div>`;
    return html;
}

async function addRestaurantBanner(event) {
    const fileInput = event.target;
    if (!fileInput.files || !fileInput.files.length) return;

    const loadingDiv = document.getElementById('restaurantBannerUploadLoading');
    loadingDiv.style.display = 'block';

    // 1. Upload the file
    const imageUrl = await uploadFile(fileInput.files[0]);

    if (imageUrl) {
        // 2. Save the banner URL via API
        const token = localStorage.getItem('token');
        const res = await fetch('/api/restaurant-banners', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            // Note: Adjust the position logic if needed. Here it's hardcoded to 1.
            body: JSON.stringify({ image_url: imageUrl, position: 1 }) 
        });

        if (res.ok) {
            alert('تمت إضافة بنر المطعم بنجاح.');
            fetchSettings(); // Refresh the list
        } else {
            alert('حدث خطأ أثناء حفظ البنر!');
        }
    } else {
        alert('حدث خطأ أثناء رفع الصورة.');
    }
    
    loadingDiv.style.display = 'none';
    fileInput.value = ''; // Reset file input
}

async function deleteRestaurantBanner(id) {
    if (!confirm(`هل أنت متأكد من حذف البنر رقم ${id}؟`)) return;

    const token = localStorage.getItem('token');
    const res = await fetch(`/api/restaurant-banners/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    });

    if (res.ok) {
        alert('تم حذف البنر بنجاح.');
        fetchSettings(); // Refresh the list
    } else {
        alert('حدث خطأ أثناء الحذف!');
    }
}
// ====== /End of Restaurant Banners Section ====== //


// ----------- About Us ----------- //
function renderAboutUs() {
    let about = settingsData.aboutUs || {};
    if (Array.isArray(about)) about = {};
    let html = `<div class="modern-form"><h4>عن التطبيق</h4>
        <form onsubmit="return saveSetting('aboutUs')">
        <div id="aboutList">`;
    Object.entries(about).forEach(([title, desc]) => {
        html += `<div class="modern-input-group aboutUs-row">
            <input type="text" class="modern-input" name="aboutTitle[]" placeholder="📝 العنوان" value="${title}">
            <input type="text" class="modern-input" name="aboutDesc[]" placeholder="📄 الوصف" value="${desc}">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        </div>`;
    });
    html += `</div>
        <button type="button" class="modern-btn modern-btn-primary mb-2" onclick="addAboutRow()">➕ إضافة</button>
        <button type="submit" class="modern-btn modern-btn-success modern-btn-full">💾 حفظ عن التطبيق</button>
        </form></div>`;
    return html;
}
function addAboutRow() {
    const list = document.getElementById('aboutList');
    if (list) {
        const div = document.createElement('div');
        div.className = 'modern-input-group aboutUs-row';
        div.innerHTML = `
            <input type="text" class="modern-input" name="aboutTitle[]" placeholder="📝 العنوان">
            <input type="text" class="modern-input" name="aboutDesc[]" placeholder="📄 الوصف">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        `;
        list.appendChild(div);
    }
}

// ----------- Terms ----------- //
function renderTerms() {
    let terms = settingsData.termsAndConditions || {};
    if (Array.isArray(terms)) terms = {};
    let html = `<div class="modern-form"><h4>الشروط والأحكام</h4>
        <form onsubmit="return saveSetting('termsAndConditions')">
        <div id="termsList">`;
    Object.entries(terms).forEach(([title, desc]) => {
        html += `<div class="modern-input-group terms-row">
            <input type="text" class="modern-input" name="termTitle[]" placeholder="📝 العنوان" value="${title}">
            <input type="text" class="modern-input" name="termDesc[]" placeholder="📄 الوصف" value="${desc}">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        </div>`;
    });
    html += `</div>
        <button type="button" class="modern-btn modern-btn-primary mb-2" onclick="addTermsRow()">➕ إضافة</button>
        <button type="submit" class="modern-btn modern-btn-success modern-btn-full">💾 حفظ الشروط</button>
        </form></div>`;
    return html;
}
function addTermsRow() {
    const list = document.getElementById('termsList');
    if (list) {
        const div = document.createElement('div');
        div.className = 'modern-input-group terms-row';
        div.innerHTML = `
            <input type="text" class="modern-input" name="termTitle[]" placeholder="📝 العنوان">
            <input type="text" class="modern-input" name="termDesc[]" placeholder="📄 الوصف">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        `;
        list.appendChild(div);
    }
}

// ----------- FAQs ----------- //
function renderFaqs() {
    let faqs = settingsData.faqs || {};
    if (Array.isArray(faqs)) faqs = {};
    let html = `<div class="modern-form"><h4>الأسئلة الشائعة</h4>
        <form onsubmit="return saveSetting('faqs')">
        <div id="faqList">`;
    Object.entries(faqs).forEach(([title, desc]) => {
        html += `<div class="modern-input-group faqs-row">
            <input type="text" class="modern-input" name="faqTitle[]" placeholder="❓ السؤال" value="${title}">
            <input type="text" class="modern-input" name="faqDesc[]" placeholder="💬 الإجابة" value="${desc}">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        </div>`;
    });
    html += `</div>
        <button type="button" class="modern-btn modern-btn-primary mb-2" onclick="addFaqRow()">➕ إضافة</button>
        <button type="submit" class="modern-btn modern-btn-success modern-btn-full">💾 حفظ الأسئلة الشائعة</button>
        </form></div>`;
    return html;
}
function addFaqRow() {
    const list = document.getElementById('faqList');
    if (list) {
        const div = document.createElement('div');
        div.className = 'modern-input-group faqs-row';
        div.innerHTML = `
            <input type="text" class="modern-input" name="faqTitle[]" placeholder="❓ السؤال">
            <input type="text" class="modern-input" name="faqDesc[]" placeholder="💬 الإجابة">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        `;
        list.appendChild(div);
    }
}

// ----------- Social Media ----------- //
function renderSocialMedia() {
    let social = settingsData.socialMedia || {};
    if (Array.isArray(social)) social = {};
    let html = `<div class="modern-form"><h4>روابط السوشيال ميديا</h4>
        <form onsubmit="return saveSetting('socialMedia')">
        <div id="socialList">`;
    Object.entries(social).forEach(([key, link]) => {
        html += `<div class="modern-input-group social-row">
            <input type="text" class="modern-input" name="socialKey[]" placeholder="🌐 النوع (facebook, twitter...)" value="${key}">
            <input type="text" class="modern-input" name="socialLink[]" placeholder="🔗 الرابط أو الرقم" value="${link}">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        </div>`;
    });
    html += `</div>
        <button type="button" class="modern-btn modern-btn-primary mb-2" onclick="addSocialRow()">➕ إضافة</button>
        <button type="submit" class="modern-btn modern-btn-success modern-btn-full">💾 حفظ الروابط</button>
        </form></div>`;
    return html;
}
function addSocialRow() {
    const list = document.getElementById('socialList');
    if (list) {
        const div = document.createElement('div');
        div.className = 'modern-input-group social-row';
        div.innerHTML = `
            <input type="text" class="modern-input" name="socialKey[]" placeholder="🌐 النوع (facebook, twitter...)">
            <input type="text" class="modern-input" name="socialLink[]" placeholder="🔗 الرابط أو الرقم">
            <button type="button" class="modern-btn modern-btn-danger" onclick="this.parentNode.remove()">🗑️ حذف</button>
        `;
        list.appendChild(div);
    }
}

// ------ General Save for AboutUs, Terms, FAQs, Social Media ------ //
async function saveSetting(key) {
    event.preventDefault();
    let value = {};
    if (key === 'aboutUs') {
        document.querySelectorAll('.aboutUs-row').forEach(row => {
            const t = row.querySelector('input[name="aboutTitle[]"]').value;
            const d = row.querySelector('input[name="aboutDesc[]"]').value;
            if (t.trim()) value[t] = d;
        });
    } else if (key === 'termsAndConditions') {
        document.querySelectorAll('.terms-row').forEach(row => {
            const t = row.querySelector('input[name="termTitle[]"]').value;
            const d = row.querySelector('input[name="termDesc[]"]').value;
            if (t.trim()) value[t] = d;
        });
    } else if (key === 'faqs') {
        document.querySelectorAll('.faqs-row').forEach(row => {
            const t = row.querySelector('input[name="faqTitle[]"]').value;
            const d = row.querySelector('input[name="faqDesc[]"]').value;
            if (t.trim()) value[t] = d;
        });
    } else if (key === 'socialMedia') {
        document.querySelectorAll('.social-row').forEach(row => {
            const s = row.querySelector('input[name="socialKey[]"]').value;
            const l = row.querySelector('input[name="socialLink[]"]').value;
            if (s.trim() && l.trim()) value[s] = l;
        });
    }

    const token = localStorage.getItem('token');
    const res = await fetch(`/api/settings/${key}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token,
        },
        body: JSON.stringify({ value })
    });
    if (res.ok) {
        alert('تم حفظ البيانات بنجاح');
        fetchSettings();
    } else {
        alert('حدث خطأ أثناء الحفظ!');
    }
}

// ====== New General File Upload Function ======
async function uploadFile(file) {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('files[]', file);

    try {
        const res = await fetch('/api/upload', {
            method: 'POST',
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            },
            body: formData
        });

        if (res.ok) {
            const data = await res.json();
            if (data.files && data.files.length > 0) {
                return data.files[0]; // Returns the URL of the uploaded file
            }
        }
        return null;
    } catch (error) {
        console.error('Upload failed:', error);
        return null;
    }
}

// ---- تحميل الإعدادات عند بداية الصفحة ----
window.addEventListener('DOMContentLoaded', fetchSettings);
</script>
@endsection