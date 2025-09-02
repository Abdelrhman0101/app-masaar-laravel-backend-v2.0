@extends('layouts.dashboard')

@section('title', 'التحكم في التطبيق')

@section('content')
<style>
/* Modern App Controller Styles */
.modern-app-title {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 800;
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(255, 107, 53, 0.3);
    margin-bottom: 2rem;
    position: relative;
}

.modern-app-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #ff6b35, #f7931e);
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.4);
}

/* Modern Tabs */
.modern-tabs {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 2rem;
}

.modern-tab-button {
    background: transparent;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    color: #666;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modern-tab-button:hover {
    background: rgba(255, 107, 53, 0.1);
    color: #ff6b35;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.2);
}

.modern-tab-button.active {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.modern-tab-button.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

/* Modern Cards Container */
.modern-cards-container {
    padding: 20px;
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.05), rgba(247, 147, 30, 0.05));
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Modern Cards */
.modern-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    height: 100%;
}

.modern-card::before {
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

.modern-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(255, 107, 53, 0.2);
    border-color: rgba(255, 107, 53, 0.3);
}

.modern-card-header {
    padding: 20px 20px 15px;
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(247, 147, 30, 0.1));
    border-bottom: 1px solid rgba(255, 107, 53, 0.2);
    position: relative;
}

.modern-card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 20px;
    right: 20px;
    height: 2px;
    background: linear-gradient(90deg, #ff6b35, #f7931e);
    border-radius: 1px;
}

.modern-card-body {
    padding: 20px;
}
  
  /* تصميم حديث ومبدع لنافذة التفاصيل */
.modern-details-modal .modal-dialog {
    max-width: 95vw;
}

.modern-modal-content {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
    border-radius: 25px;
    box-shadow: 
        0 25px 80px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(252, 135, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    border: none;
    overflow: hidden;
    backdrop-filter: blur(10px);
    position: relative;
}

.modern-modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #FC8700, #ff9500, #FC8700);
    background-size: 200% 100%;
    animation: gradientShift 3s ease-in-out infinite;
}

.modern-modal-header {
    background: linear-gradient(135deg, #FC8700 0%, #ff9500 30%, #FC8700 70%, #e67600 100%);
    color: white;
    border: none;
    padding: 25px 35px;
    position: relative;
    overflow: hidden;
}

.modern-modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmerFlow 4s infinite;
}

.modern-modal-title {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 2;
}

.title-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.title-icon {
    font-size: 2rem;
    animation: iconPulse 2s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

.icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
    border-radius: 50%;
    animation: glowPulse 2s ease-in-out infinite;
}

.title-text {
    background: linear-gradient(45deg, #ffffff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.title-decoration {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    border-radius: 1px;
}

.modern-close-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 3;
}

.modern-close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: rotate(90deg) scale(1.15);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.modern-close-btn i {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.modern-modal-body {
    padding: 35px;
    background: linear-gradient(135deg, #fafbfc 0%, #ffffff 50%, #f8f9fa 100%);
    position: relative;
}

/* أنماط التفاصيل الحديثة */
.details-container {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.details-item {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(252, 135, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.details-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #FC8700, #ff9500);
    border-radius: 0 15px 15px 0;
}

.details-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(252, 135, 0, 0.15);
}

.details-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.details-label i {
    color: #FC8700;
    font-size: 1.1rem;
}

.details-content {
    margin-right: 25px;
}

.details-value {
    color: #555;
    font-weight: 500;
    line-height: 1.5;
}

.details-empty {
    color: #999;
    font-style: italic;
    opacity: 0.7;
}

.details-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.badge-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.badge-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.badge-info {
    background: rgba(252, 135, 0, 0.1);
    color: #FC8700;
    border: 1px solid rgba(252, 135, 0, 0.2);
}

.details-image-container {
    position: relative;
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.details-image-container:hover {
    transform: scale(1.08);
    box-shadow: 0 15px 35px rgba(252, 135, 0, 0.3);
}

.details-image:hover {
    border-color: #FC8700;
    transform: scale(1.02);
}

.details-image {
    max-width: 150px;
    max-height: 100px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    object-fit: cover;
    border: 2px solid rgba(252, 135, 0, 0.2);
}

.details-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 12px;
}

.details-image-container:hover .details-image-overlay {
    opacity: 1;
}

.details-image-overlay i {
    color: white;
    font-size: 1.8rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

/* تحسين نافذة عرض الصورة الكاملة */
.img-modal .modal-dialog {
    max-width: 90vw;
    max-height: 90vh;
}

.img-modal .modal-content {
    background: transparent;
    border: none;
    box-shadow: none;
}

.img-modal .modal-body {
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.img-modal img {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* الرسوم المتحركة الجديدة المطلوبة */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes shimmerFlow {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes glowPulse {
    0%, 100% { opacity: 0.4; transform: translate(-50%, -50%) scale(1); }
    50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.2); }
}

/* أنماط الأقسام المتخصصة الجديدة */
.details-section {
    margin-bottom: 25px;
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.details-section:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.details-section.specialized-section {
    border-left: 4px solid #FC8700;
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
}

.section-title {
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #FC8700;
    font-size: 18px;
    animation: iconPulse 2s infinite;
}

/* شبكة البيانات النصية */
.text-data-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

/* قسم الصور المحسن */
.images-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.images-title {
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.images-title i {
    color: #28a745;
    animation: iconPulse 2s infinite;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.image-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    text-align: center;
}

.image-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(252, 135, 0, 0.15);
    border-color: #FC8700;
}

.image-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 10px;
    text-align: center;
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 6px;
    display: inline-block;
}

/* تحسينات للقيم النقدية والمنطقية داخل التفاصيل */
.details-value.money {
    color: #28a745;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    background: #f8fff9;
    padding: 5px 10px;
    border-radius: 6px;
    border: 1px solid #d4edda;
}
.details-value.money i {
    color: #ffc107;
    animation: iconPulse 2s infinite;
}
.details-value .badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.details-value .badge.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}
.details-value .badge.bg-danger {
    background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

/* تحسينات متجاوبة للأقسام الجديدة */
@media (max-width: 768px) {
    .modern-details-modal .modal-dialog {
        max-width: 98vw;
        margin: 10px;
    }
    .modern-modal-header {
        padding: 20px 25px;
    }
    .modern-modal-title {
        font-size: 1.3rem;
        gap: 15px;
    }
    .title-icon {
        font-size: 1.6rem;
    }
    .modern-modal-body {
        padding: 25px 20px;
    }
    .details-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .details-item {
        padding: 15px;
    }
    .details-label {
        font-size: 0.9rem;
    }
    .details-image {
        max-width: 100px;
        max-height: 70px;
    }
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .modern-modal-header {
        padding: 15px 20px;
    }
    .modern-modal-title {
        font-size: 1.1rem;
        gap: 10px;
    }
    .title-icon {
        font-size: 1.4rem;
    }
    .modern-close-btn {
        width: 35px;
        height: 35px;
    }
    .modern-close-btn i {
        font-size: 1rem;
    }
    .text-data-grid {
        gap: 10px;
    }
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
    .details-section {
        padding: 12px;
        margin-bottom: 15px;
    }
    .section-title {
        font-size: 13px;
        gap: 6px;
    }
    .image-card {
        padding: 10px;
    }
    .image-label {
        font-size: 11px;
        padding: 4px 8px;
    }
}


/* Logo/Image Styles */
.modern-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 107, 53, 0.3);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
}

.modern-logo:hover {
    transform: scale(1.1) rotate(5deg);
    border-color: #ff6b35;
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.modern-property-image {
    width: 100%;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid rgba(255, 107, 53, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    margin-bottom: 15px;
}

.modern-property-image:hover {
    transform: scale(1.05);
    border-color: #ff6b35;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
}

/* Info Section */
.modern-info-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.modern-info-text {
    flex: 1;
}

.modern-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.modern-subtitle {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Details Section */
.modern-details {
    margin: 15px 0;
}

.modern-detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 107, 53, 0.1);
    transition: all 0.3s ease;
}

.modern-detail-item:hover {
    background: rgba(255, 107, 53, 0.05);
    padding-left: 10px;
    border-radius: 8px;
}

.modern-detail-icon {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
    box-shadow: 0 2px 6px rgba(255, 107, 53, 0.3);
}

.modern-detail-label {
    font-weight: 600;
    color: #333;
    min-width: 80px;
}

.modern-detail-value {
    color: #666;
    flex: 1;
}

/* Action Section */
.modern-actions {
    padding-top: 15px;
    border-top: 2px solid rgba(255, 107, 53, 0.1);
    margin-top: 15px;
}

.modern-favorite-btn {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    width: 100%;
}

.modern-favorite-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.modern-favorite-btn.active {
    background: linear-gradient(135deg, #28a745, #20c997);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.modern-favorite-btn.active:hover {
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.modern-favorite-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.modern-favorite-btn:hover::before {
    left: 100%;
}

/* Animations */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
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

.modern-card.new-card-animation {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-app-title {
        font-size: 2rem;
    }
    
    .modern-card-header,
    .modern-card-body {
        padding: 15px;
    }
    
    .modern-logo {
        width: 50px;
        height: 50px;
    }
    
    .modern-property-image {
        height: 100px;
    }
    
    .modern-title {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .modern-app-title {
        font-size: 1.8rem;
    }
    
    .modern-tab-button {
        padding: 10px 16px;
        font-size: 0.9rem;
    }
    
    .modern-card-header,
    .modern-card-body {
        padding: 12px;
    }
    
    .modern-logo {
        width: 45px;
        height: 45px;
    }
    
    .modern-property-image {
        height: 80px;
    }
}
</style>

<h2 class="modern-app-title">التحكم في التطبيق</h2>
<div class="modern-tabs">
    <div class="d-flex gap-2" id="controlTabs">
        <button class="modern-tab-button active" id="tab-restaurants" onclick="switchTab('restaurants')">المطاعم</button>
        <button class="modern-tab-button" id="tab-properties" onclick="switchTab('properties')">العقارات</button>
    </div>
</div>
<div id="controlTabContent" class="mt-4"></div>

<!-- Modal لعرض الصورة -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <img id="imgModalSrc" src="" style="max-width:98vw;max-height:85vh;display:block;margin:auto;">
    </div>
</div>
<!-- Modal تفاصيل (نفس ستايل إدارة الحسابات) -->
<div class="modal fade" id="entityDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modern-details-modal">
    <div class="modal-content modern-modal-content">
      <div class="modal-header modern-modal-header">
        <h5 class="modal-title modern-modal-title">
          <div class="title-icon-wrapper">
            <i class="bi bi-person-badge-fill title-icon" id="entityDetailsIcon"></i>
            <div class="icon-glow"></div>
          </div>
          <span class="title-text" id="entityDetailsTitle">تفاصيل</span>
          <div class="title-decoration"></div>
        </h5>
        <button type="button" class="btn-close modern-close-btn" data-bs-dismiss="modal" aria-label="إغلاق">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="modal-body modern-modal-body" id="entityDetailsContent"></div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/** ====== إعدادات عامة ====== **/
const baseUrl = "{{ url('') }}"; // أكثر أمانًا من ثابت domain
let currentTab = 'restaurants';
let restaurants = [];
let properties = [];

let pollTimer = null;
const POLL_MS = 8000;

// لإلغاء الطلبات عند التبديل أو التحديث
let inflight = {
  restaurants: null,
  properties: null,
};

// تأكيد وجود توكن
function getTokenOrThrow() {
  const token = localStorage.getItem('token');
  if (!token) throw new Error('Missing token in localStorage.');
  return token;
}

/** ====== تبويبات ====== **/
function switchTab(tab) {
  // تحديث حالة الأزرار
  document.querySelectorAll('#controlTabs button').forEach(b => b.classList.remove('active'));
  const btn = document.getElementById(`tab-${tab}`);
  if (btn) btn.classList.add('active');

  // إلغاء أي طلب جاري للتبويب السابق
  abortIfInflight(currentTab);

  currentTab = tab;
  // تفريغ المحتوى وبدء التحميل الأول
  document.getElementById('controlTabContent').innerHTML =
    `<div class="text-center py-5"><div class="spinner-border"></div></div>`;

  renderCurrentTab(false);
}

function renderCurrentTab(auto = false) {
  if (currentTab === 'restaurants') {
    fetchRestaurants(auto);
  } else {
    fetchProperties(auto);
  }
}

function abortIfInflight(tab) {
  const c = inflight[tab];
  if (c && typeof c.abort === 'function') c.abort();
  inflight[tab] = null;
}

/** ====== طلبات الشبكة (مطاعم) ====== **/
async function fetchRestaurants(auto = false) {
  // إلغاء أي طلب سابق للمطاعم
  abortIfInflight('restaurants');

  const controller = new AbortController();
  inflight.restaurants = controller;

  try {
    const token = getTokenOrThrow();
    if (!auto && !document.getElementById('restaurants-container')) {
      document.getElementById('controlTabContent').innerHTML =
        `<div class="text-center py-5"><div class="spinner-border"></div></div>`;
    }

    const res = await fetch(`${baseUrl}/api/users`, {
      headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
      signal: controller.signal
    });

    if (!res.ok) {
      const t = await res.text();
      throw new Error(`Users fetch failed: ${res.status} ${t}`);
    }

    const data = await res.json();

    // تجاهل الرد لو المستخدم غيّر التبويب أثناء الانتظار
    if (currentTab !== 'restaurants') return;

    const list = Array.isArray(data?.users) ? data.users : [];
    restaurants = list.filter(u => u?.user_type === 'restaurant');
    renderRestaurants(!document.getElementById('restaurants-container')); // أول مرة فقط ننشئ الحاوية
  } catch (err) {
    if (err.name === 'AbortError') return;
    if (!auto) {
      console.error('[CONTROL] Restaurants error:', err);
      document.getElementById('controlTabContent').innerHTML =
        `<div class="alert alert-danger">تعذر تحميل المطاعم! ${escapeHTML(err.message)}</div>`;
    }
  } finally {
    inflight.restaurants = null;
  }
}

function renderRestaurants(isFirstRender = false) {
  const container = document.getElementById('controlTabContent');

  if (isFirstRender) {
    if (!restaurants.length) {
      container.innerHTML = `<div class="alert alert-warning">لا توجد مطاعم حالياً.</div>`;
      return;
    }
    container.innerHTML = `
      <div class="modern-cards-container">
        <div class="row g-4" id="restaurants-container"></div>
      </div>`;
  }

  const cards = document.getElementById('restaurants-container');
  if (!cards) return;

  const existing = new Set([...cards.children].map(c => c.id));
  const incoming = new Set(restaurants.map(r => `restaurant-card-${r.id}`));

  // إضافة/تحديث
  restaurants.forEach(r => {
    const cardId = `restaurant-card-${r.id}`;
    const isBest = Number(r?.the_best) === 1;
    const logo = r?.restaurant_detail?.logo_image || r?.restaurant_detail?.profile_image || '';

    let col = document.getElementById(cardId);
    if (!col) {
      col = document.createElement('div');
      col.className = 'col-md-6 col-lg-4';
      col.id = cardId;
      col.innerHTML = createRestaurantCardHTML(r, isBest, logo);
      cards.appendChild(col);
    } else {
      updateRestaurantCard(col, r, isBest, logo);
    }
  });

  // إزالة المفقود
  existing.forEach(id => {
    if (!incoming.has(id)) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }
  });
}

function createRestaurantCardHTML(r, isBest, logo) {
  const name = r?.restaurant_detail?.restaurant_name || r?.name || 'مطعم';
  const email = r?.email || '-';
  const governorate = r?.governorate ?? '-';
  const phone = r?.phone ?? '-';

  return `
     <div class="modern-card new-card-animation js-open-details" data-kind="restaurant" data-id="${r.id}" tabindex="0" role="button">
      <div class="modern-card-header">
        <div class="modern-info-section">
          ${logo ? `<img src="${escapeAttr(logo)}" class="modern-logo" onclick="openImgFull('${escapeAttr(logo)}')">` : ''}
          <div class="modern-info-text">
            <div class="modern-title">${escapeHTML(name)}</div>
            <div class="modern-subtitle">${escapeHTML(email)}</div>
          </div>
        </div>
      </div>
      <div class="modern-card-body">
        <div class="modern-details">
          <div class="modern-detail-item">
            <div class="modern-detail-icon">📍</div>
            <div class="modern-detail-label">المحافظة:</div>
            <div class="modern-detail-value">${escapeHTML(String(governorate))}</div>
          </div>
          <div class="modern-detail-item">
            <div class="modern-detail-icon">📞</div>
            <div class="modern-detail-label">الهاتف:</div>
            <div class="modern-detail-value">${escapeHTML(String(phone))}</div>
          </div>
        </div>
        <div class="modern-actions">
          <button type="button"
            class="modern-favorite-btn ${isBest ? 'active' : ''}"
            data-id="${r.id}" data-kind="restaurant">
            <i class="fas fa-star"></i> ${isBest ? 'مُفضّل' : 'إضافة للمفضلة'}
          </button>
        </div>
      </div>
    </div>`;
}

function updateRestaurantCard(col, r, isBest, logo) {
  // حدّث الاسم/الإيميل السريع
  const title = col.querySelector('.modern-title');
  const sub = col.querySelector('.modern-subtitle');
  if (title) title.textContent = r?.restaurant_detail?.restaurant_name || r?.name || 'مطعم';
  if (sub) sub.textContent = r?.email || '-';

  // حدّث اللوجو إن تغيّر
  const img = col.querySelector('.modern-logo');
  if (logo) {
    if (img) img.src = logo;
  } else if (img) {
    img.remove();
  }

  // زر المفضلة
  const btn = col.querySelector('.modern-favorite-btn');
  if (btn) {
    btn.classList.toggle('active', isBest);
    btn.innerHTML = `<i class="fas fa-star"></i> ${isBest ? 'مُفضّل' : 'إضافة للمفضلة'}`;
    btn.dataset.id = r.id;
    btn.dataset.kind = 'restaurant';
  }
}

/** ====== طلبات الشبكة (عقارات) ====== **/
async function fetchProperties(auto = false) {
  abortIfInflight('properties');

  const controller = new AbortController();
  inflight.properties = controller;

  try {
    const token = getTokenOrThrow();
    if (!auto && !document.getElementById('properties-container')) {
      document.getElementById('controlTabContent').innerHTML =
        `<div class="text-center py-5"><div class="spinner-border"></div></div>`;
    }

    const res = await fetch(`${baseUrl}/api/all-properties`, {
      headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
      signal: controller.signal
    });

    if (!res.ok) {
      const t = await res.text();
      throw new Error(`Properties fetch failed: ${res.status} ${t}`);
    }

    const data = await res.json();

    if (currentTab !== 'properties') return;

    properties = Array.isArray(data) ? data : (data?.properties ?? []);
    renderProperties(!document.getElementById('properties-container'));
  } catch (err) {
    if (err.name === 'AbortError') return;
    if (!auto) {
      console.error('[CONTROL] Properties error:', err);
      document.getElementById('controlTabContent').innerHTML =
        `<div class="alert alert-danger">تعذر تحميل العقارات! ${escapeHTML(err.message)}</div>`;
    }
  } finally {
    inflight.properties = null;
  }
}

function renderProperties(isFirstRender = false) {
  const container = document.getElementById('controlTabContent');

  if (isFirstRender) {
    if (!properties.length) {
      container.innerHTML = `<div class="alert alert-warning">لا توجد عقارات حالياً.</div>`;
      return;
    }
    container.innerHTML = `
      <div class="modern-cards-container">
        <div class="row g-4" id="properties-container"></div>
      </div>`;
  }

  const cards = document.getElementById('properties-container');
  if (!cards) return;

  const existing = new Set([...cards.children].map(c => c.id));
  const incoming = new Set(properties.map(p => `property-card-${p.id}`));

  properties.forEach(p => {
    const cardId = `property-card-${p.id}`;
    const isBest = Number(p?.the_best) === 1;

    let col = document.getElementById(cardId);
    if (!col) {
      col = document.createElement('div');
      col.className = 'col-md-6 col-lg-4';
      col.id = cardId;
      col.innerHTML = createPropertyCardHTML(p, isBest);
      cards.appendChild(col);
    } else {
      updatePropertyCard(col, p, isBest);
    }
  });

  existing.forEach(id => {
    if (!incoming.has(id)) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }
  });
}

function createPropertyCardHTML(p, isBest) {
  const img = p?.image_url || '';
  const type = p?.type || 'عقار';
  const address = p?.address || '-';
  const price = p?.price != null ? p.price : '-';
  const area = p?.area ?? '-';

  return `
    <div class="modern-card new-card-animation js-open-details" data-kind="property" data-id="${p.id}" tabindex="0" role="button">
      <div class="modern-card-header">
        ${img ? `<img src="${escapeAttr(img)}" class="modern-property-image" onclick="openImgFull('${escapeAttr(img)}')">` : ''}
        <div class="modern-info-text">
          <div class="modern-title">${escapeHTML(String(type))}</div>
          <div class="modern-subtitle">${escapeHTML(String(address))}</div>
        </div>
      </div>
      <div class="modern-card-body">
        <div class="modern-details">
          <div class="modern-detail-item">
            <div class="modern-detail-icon">💰</div>
            <div class="modern-detail-label">السعر:</div>
            <div class="modern-detail-value">${escapeHTML(String(price))} جنيه</div>
          </div>
          <div class="modern-detail-item">
            <div class="modern-detail-icon">📐</div>
            <div class="modern-detail-label">المساحة:</div>
            <div class="modern-detail-value">${escapeHTML(String(area))}</div>
          </div>
        </div>
        <div class="modern-actions">
          <button type="button"
            class="modern-favorite-btn ${isBest ? 'active' : ''}"
            data-id="${p.id}" data-kind="property">
            <i class="fas fa-star"></i> ${isBest ? 'مُفضّل' : 'إضافة للمفضلة'}
          </button>
        </div>
      </div>
    </div>`;
}

function updatePropertyCard(col, p, isBest) {
  const title = col.querySelector('.modern-title');
  const sub = col.querySelector('.modern-subtitle');
  if (title) title.textContent = p?.type || 'عقار';
  if (sub) sub.textContent = p?.address || '-';

  const imgEl = col.querySelector('.modern-property-image');
  if (p?.image_url) {
    if (imgEl) imgEl.src = p.image_url;
  } else if (imgEl) {
    imgEl.remove();
  }

  const btn = col.querySelector('.modern-favorite-btn');
  if (btn) {
    btn.classList.toggle('active', isBest);
    btn.innerHTML = `<i class="fas fa-star"></i> ${isBest ? 'مُفضّل' : 'إضافة للمفضلة'}`;
    btn.dataset.id = p.id;
    btn.dataset.kind = 'property';
  }
}

/** ====== تفعيل/تعطيل “المفضل” (تفويض أحداث) ====== **/
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.modern-favorite-btn');
  if (!btn) return;

  const id = btn.dataset.id;
  const kind = btn.dataset.kind; // 'restaurant' | 'property'
  if (!id || !kind) return;

  try {
    const token = getTokenOrThrow();
    const willSet = btn.classList.contains('active') ? 0 : 1;

    const url = kind === 'restaurant'
      ? `${baseUrl}/api/users/${id}`
      : `${baseUrl}/api/properties/${id}`;

    const res = await fetch(url, {
      method: 'PUT',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ the_best: willSet })
    });

    if (!res.ok) {
      const text = await res.text();
      throw new Error(`Update failed: ${res.status} ${text}`);
    }

    // تحديث بصمة واحدة محليًا لسرعة الاستجابة (optimistic)
    if (kind === 'restaurant') {
      const idx = restaurants.findIndex(r => String(r.id) === String(id));
      if (idx > -1) restaurants[idx].the_best = willSet;
      updateRestaurantCard(document.getElementById(`restaurant-card-${id}`), restaurants[idx], willSet,
        restaurants[idx]?.restaurant_detail?.logo_image || restaurants[idx]?.restaurant_detail?.profile_image || '');
    } else {
      const idx = properties.findIndex(p => String(p.id) === String(id));
      if (idx > -1) properties[idx].the_best = willSet;
      updatePropertyCard(document.getElementById(`property-card-${id}`), properties[idx], willSet);
    }
  } catch (err) {
    console.error('[CONTROL] Toggle favorite error:', err);
    alert('خطأ أثناء تغيير حالة المفضلة!');
  }
});

/** ====== معاينة الصور ====== **/
function openImageModal(src, title = 'عرض الصورة') {
  const existing = document.getElementById('dynamicImageModal');
  if (existing) existing.remove();

  const html = `
    <div class="modal fade" id="dynamicImageModal" tabindex="-1" aria-labelledby="dynamicImageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="dynamicImageModalLabel">${escapeHTML(title)}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
          </div>
          <div class="modal-body text-center">
            <img src="${escapeAttr(src)}" class="img-fluid" alt="${escapeAttr(title)}" style="max-height: 70vh; border-radius: 8px;">
          </div>
        </div>
      </div>
    </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  const modal = new bootstrap.Modal(document.getElementById('dynamicImageModal'));
  modal.show();
  document.getElementById('dynamicImageModal').addEventListener('hidden.bs.modal', function () { this.remove(); });
}
function openImgFull(src) { openImageModal(src, 'صورة التطبيق'); }

/** ====== Polling متحكَّم فيه ====== **/
function startPolling() {
  stopPolling();
  pollTimer = setInterval(() => renderCurrentTab(true), POLL_MS);
  console.log(`[CONTROL] Auto-refresh every ${POLL_MS/1000}s`);
}
function stopPolling() {
  if (pollTimer) clearInterval(pollTimer);
  pollTimer = null;
}

/** ====== Utilities صغيرة ====== **/
function escapeHTML(s) {
  return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}
function escapeAttr(s) {
  // آمنة للاستخدام داخل صفات HTML
  return escapeHTML(s).replace(/"/g, '&quot;');
}
/** ====== تفاصيل الكروت (مطعم/عقار) — ستايل “إدارة الحسابات” ====== **/
  // ===== Helpers: تسميات/أيقونات + عرض أي قيمة (نص/رقم/Boolean/صورة/مصفوفة/كائن) =====
const AR_LABELS = {
  branches: 'الفروع',
  working_hours: 'مواعيد العمل',
  workingHours: 'مواعيد العمل',
  appointments: 'المواعيد',
  cuisine_types: 'أنواع المطبخ',
  delivery_available: 'يدعم التوصيل',
  delivery_cost_per_km: 'تكلفة التوصيل لكل كم',
  deposit_required: 'مطلوب عربون',
  the_best: 'المفضلة',
  real_estate: 'العقار',
  office_detail: 'تفاصيل المكتب',
  driver_detail: 'تفاصيل السائق',
  individual_detail: 'تفاصيل السمسار',
  restaurant_detail: 'تفاصيل المطعم'
};

function arLabel(key){
  if (!key) return '';
  if (AR_LABELS[key]) return AR_LABELS[key];
  // محاولة ذكية لو كان key مثل some_key
  return String(key).replace(/[_\-]/g,' ').trim();
}

function iconFor(key){
  const map = {
    name:'bi-person-fill', email:'bi-envelope-fill', phone:'bi-telephone-fill', governorate:'bi-geo-alt-fill',
    restaurant_name:'bi-shop', logo:'bi-image', image:'bi-image', images:'bi-images',
    branch:'bi-diagram-3', working:'bi-clock-history', hours:'bi-clock-history',
    price:'bi-cash', area:'bi-aspect-ratio', address:'bi-geo-alt', type:'bi-tag'
  };
  key = String(key||'').toLowerCase();
  for (const k in map){ if (key.includes(k)) return map[k]; }
  return 'bi-dot';
}

function isImageLike(v){ return typeof v==='string' && /\.(png|jpe?g|gif|webp|bmp|svg)(\?.*)?$/i.test(v); }

function fmtTime(t){
  if (t == null || t === '') return '—';
  // قص بسيط لـ 08:00:00 => 08:00
  const m = String(t).match(/^(\d{1,2}):(\d{2})/);
  return m ? `${m[1].padStart(2,'0')}:${m[2]}` : String(t);
}

function renderArray(arr, key){
  if (!Array.isArray(arr) || !arr.length) return '<span class="details-empty">لا توجد بيانات</span>';

  // مصفوفة صور
  if (arr.every(isImageLike)){
    return `<div class="images-grid">` + arr.map((src)=>{
      const full = src.startsWith('http') ? src : (IMAGE_BASE + src);
      return `<div class="image-card">
        <div class="image-label">${arLabel(key)}</div>
        <div class="details-image-container">
          <img src="${full}" class="details-image" onclick="openImgFull('${full}')" alt="${arLabel(key)}">
          <div class="details-image-overlay" onclick="openImgFull('${full}')"><i class="bi bi-zoom-in"></i></div>
        </div>
      </div>`;
    }).join('') + `</div>`;
  }

  // مصفوفة بدائية (نصوص/أرقام/Booleans)
  if (arr.every(x => (typeof x !== 'object' || x === null))){
    return `<ul class="mb-0 ps-3">${arr.map(v=>`<li>${String(v)}</li>`).join('')}</ul>`;
  }

  // مصفوفة كائنات (فروع/مواعيد عمل... إلخ)
  const isWorking = /working|hours/i.test(key);
  const isBranches = /branch/i.test(key);

  if (isWorking){
    return `<ul class="mb-0 ps-3">` + arr.map(it=>{
      const day = it.day_ar || it.day_name || it.day || it.week_day || 'اليوم';
      const from = it.from || it.start || it.start_time || it.open || it.open_at;
      const to   = it.to   || it.end   || it.end_time   || it.close|| it.close_at;
      const note = it.note || it.notes || '';
      return `<li><strong>${day}:</strong> ${fmtTime(from)} – ${fmtTime(to)} ${note?`<span class="text-muted">(${note})</span>`:''}</li>`;
    }).join('') + `</ul>`;
  }

  if (isBranches){
    return arr.map((b,i)=>`
      <div class="details-item nested-item">
        <div class="details-label"><i class="bi ${iconFor('branch')}"></i><span>فرع ${i+1}</span></div>
        <div class="details-content">
          ${b.name || b.branch_name ? `<div class="mb-1"><strong>الاسم:</strong> ${b.name || b.branch_name}</div>`:''}
          ${b.address || b.location ? `<div class="mb-1"><strong>العنوان:</strong> ${b.address || b.location}</div>`:''}
          ${b.phone ? `<div class="mb-1"><strong>الهاتف:</strong> ${b.phone}</div>`:''}
          ${b.email ? `<div class="mb-1"><strong>البريد:</strong> ${b.email}</div>`:''}
        </div>
      </div>
    `).join('');
  }

  // كائنات عامة
  return arr.map((obj,i)=>renderObject(obj, `${arLabel(key)} ${i+1}`)).join('');
}

function renderObject(obj, titleIfAny = ''){
  if (!obj || typeof obj !== 'object') return `<span class="details-value">${String(obj)}</span>`;
  let inner = '';
  Object.keys(obj).forEach(k=>{
    const v = obj[k];
    if (v === '' || v == null) return;
    inner += `
      <div class="details-item">
        <div class="details-label"><i class="bi ${iconFor(k)}"></i><span>${arLabel(k)}</span></div>
        <div class="details-content">${renderAny(v, k)}</div>
      </div>`;
  });
  if (!inner) inner = '<div class="details-empty">لا توجد بيانات</div>';
  return `<div class="details-nested">${titleIfAny?`<div class="mb-2 fw-bold">${titleIfAny}</div>`:''}${inner}</div>`;
}

function renderAny(value, key){
  if (value === '' || value == null) return '<span class="details-empty">غير محدد</span>';
  if (typeof value === 'boolean'){
    return `<span class="details-badge ${value?'badge-success':'badge-danger'}">
      <i class="bi ${value?'bi-check-circle-fill':'bi-x-circle-fill'}"></i>${value?'نعم':'لا'}</span>`;
  }
  // أرقام منطقية 0/1 لبعض الحقول
  if (['delivery_available','vat_included','tax_enabled','is_available_for_rent','is_available_for_delivery','the_best','deposit_required']
      .some(k=>new RegExp(k,'i').test(key))){
    const ok = Number(value) === 1;
    return `<span class="details-badge ${ok?'badge-success':'badge-danger'}">
      <i class="bi ${ok?'bi-check-circle-fill':'bi-x-circle-fill'}"></i>${ok?'نعم':'لا'}</span>`;
  }
  // أموال
  if (/price|cost|fee/i.test(key)){
    return `<span class="details-value money"><i class="bi bi-currency-dollar"></i> ${value} جنيه</span>`;
  }
  // صورة مفردة
  if (typeof value === 'string' && isImageLike(value)){
    const src = value.startsWith('http') ? value : (IMAGE_BASE + value);
    return `<div class="details-image-container">
      <img src="${src}" class="details-image" onclick="openImgFull('${src}')" alt="${arLabel(key)}">
      <div class="details-image-overlay" onclick="openImgFull('${src}')"><i class="bi bi-zoom-in"></i></div>
    </div>`;
  }
  // مصفوفة
  if (Array.isArray(value)) return renderArray(value, key);
  // كائن
  if (typeof value === 'object') return renderObject(value);
  // نص/رقم عادي
  return `<span class="details-value">${String(value)}</span>`;
}


// لو ماعندكش نفس الثابت، عرّفه هنا ليشتغل تجميع روابط الصور زي صفحة الحسابات
const IMAGE_BASE = (typeof baseUrl !== 'undefined' ? (baseUrl + '/storage/uploads/images/') : '/storage/uploads/images/');

// منع فتح التفاصيل لو الضغط كان على زر مفضلة أو صورة
function isClickOnExcludedElement(target) {
  return !!(
    target.closest('.modern-favorite-btn') ||
    target.closest('.modern-logo') ||
    target.closest('.modern-property-image')
  );
}

// فتح التفاصيل بالماوس
document.addEventListener('click', (e) => {
  const card = e.target.closest('.js-open-details');
  if (!card) return;
  if (isClickOnExcludedElement(e.target)) return;
  openEntityDetails(card.dataset.kind, card.dataset.id);
});

// فتح التفاصيل بالكيبورد (Enter/Space)
document.addEventListener('keydown', (e) => {
  if (e.key !== 'Enter' && e.key !== ' ') return;
  const el = e.target.closest('.js-open-details');
  if (!el) return;
  e.preventDefault();
  openEntityDetails(el.dataset.kind, el.dataset.id);
});

// إنشاء المودال لو مش موجود (إحتياطي)
function ensureEntityDetailsModal() {
  if (document.getElementById('entityDetailsModal')) return;
  // لو مش موجود لأي سبب، ممكن تحقنه (احنا بالفعل ضفناه في HTML فوق)
}

// فتح التفاصيل وتعبئة نفس بنية “إدارة الحسابات”
function openEntityDetails(kind, id) {
  ensureEntityDetailsModal();
  const modalEl = document.getElementById('entityDetailsModal');
  const bodyEl  = document.getElementById('entityDetailsContent');
  const titleEl = document.getElementById('entityDetailsTitle');
  const iconEl  = document.getElementById('entityDetailsIcon');

  if (kind === 'restaurant') {
    const r = restaurants.find(x => String(x.id) === String(id));
    if (!r) return alert('لم يتم العثور على المطعم.');
    titleEl.textContent = 'تفاصيل المطعم';
    iconEl.className = 'bi bi-person-badge-fill title-icon';
    bodyEl.innerHTML = renderAccountStyleDetailsForRestaurant(r);
  } else {
    const p = properties.find(x => String(x.id) === String(id));
    if (!p) return alert('لم يتم العثور على العقار.');
    titleEl.textContent = 'تفاصيل العقار';
    iconEl.className = 'bi bi-building title-icon';
    bodyEl.innerHTML = renderAccountStyleDetailsForProperty(p);
  }

  new bootstrap.Modal(modalEl).show();
}

// ====== نفس تقسيم “إدارة الحسابات” لكن مُكيّف للمطاعم/العقارات ======
function renderAccountStyleDetailsForRestaurant(user) {
  const basicFields = ['name','email','phone','governorate'];
  let html = '<div class="details-container">';

  // قسم المعلومات الأساسية
  html += '<div class="details-section"><h5 class="section-title"><i class="bi bi-person-circle"></i> المعلومات الأساسية</h5>';
  basicFields.forEach(k=>{
    if (k in user){
      html += `<div class="details-item">
        <div class="details-label"><i class="bi ${iconFor(k)}"></i><span>${arLabel(k)}</span></div>
        <div class="details-content">${renderAny(user[k], k)}</div>
      </div>`;
    }
  });
  html += '</div>';

  // تفاصيل المطعم (تعالج صور/مصفوفات/كائنات بدون [object Object])
  if (user.restaurant_detail){
    html += `<div class="details-section specialized-section">
      <h5 class="section-title"><i class="bi bi-shop"></i> ${arLabel('restaurant_detail')}</h5>`;
    Object.keys(user.restaurant_detail).forEach(k=>{
      const v = user.restaurant_detail[k];
      if (v === '' || v == null) return;
      html += `<div class="details-item">
        <div class="details-label"><i class="bi ${iconFor(k)}"></i><span>${arLabel(k)}</span></div>
        <div class="details-content">${renderAny(v, k)}</div>
      </div>`;
    });
    html += `</div>`;
  }

  // معلومات النظام
  const sys = ['id','created_at','updated_at'];
  html += '<div class="details-section"><h5 class="section-title"><i class="bi bi-gear"></i> معلومات النظام</h5>';
  sys.forEach(k=>{
    if (k in user){
      html += `<div class="details-item">
        <div class="details-label"><i class="bi ${iconFor(k)}"></i><span>${arLabel(k)}</span></div>
        <div class="details-content">${renderAny(user[k], k)}</div>
      </div>`;
    }
  });
  html += '</div>';

  html += '</div>';
  return html;
}

function renderAccountStyleDetailsForProperty(p) {
  let html = '<div class="details-container">';

  // معلومات أساسية
  html += '<div class="details-section"><h5 class="section-title"><i class="bi bi-building"></i> معلومات أساسية</h5>';
  const basics = { type:'النوع', address:'العنوان', price:'السعر', area:'المساحة' };
  Object.keys(basics).forEach(k=>{
    if (p[k] == null) return;
    html += `<div class="details-item">
      <div class="details-label"><i class="bi ${iconFor(k)}"></i><span>${basics[k]}</span></div>
      <div class="details-content">${renderAny(p[k], k)}</div>
    </div>`;
  });
  html += '</div>';

  // باقي الحقول بما فيها الكائنات (مثلاً real_estate) والمصفوفات والصور
  const skip = new Set(['id','type','address','price','area','the_best']);
  const imageBucket = [];

  Object.keys(p || {}).forEach(k=>{
    if (skip.has(k)) return;
    const v = p[k];
    if (v === '' || v == null) return;

    // تجميع مصفوفة صور أو صورة مفردة في قسم صور
    if (isImageLike(v)) { imageBucket.push(v); return; }
    if (Array.isArray(v) && v.length && v.every(isImageLike)) { imageBucket.push(...v); return; }

    // أي شيء آخر يُعرض مباشرة باستخدام renderer العام
    html += `<div class="details-section">
      <h5 class="section-title"><i class="bi ${iconFor(k)}"></i> ${arLabel(k)}</h5>
      <div class="text-data-grid">
        <div class="details-item" style="grid-column: 1 / -1;">
          <div class="details-content">${renderAny(v, k)}</div>
        </div>
      </div>
    </div>`;
  });

  if (imageBucket.length){
    html += `<div class="details-section specialized-section">
      <h5 class="section-title"><i class="bi bi-images"></i> الصور</h5>
      ${renderArray(imageBucket, 'images')}
    </div>`;
  }

  html += '</div>';
  return html;
}

/** ====== تشغيل مبدئي ====== **/
document.addEventListener('DOMContentLoaded', () => {
  try {
    getTokenOrThrow();
  } catch (e) {
    console.error(e);
    document.getElementById('controlTabContent').innerHTML =
      `<div class="alert alert-warning">غير مسجل الدخول. برجاء إعادة تسجيل الدخول.</div>`;
    return;
  }
  switchTab('restaurants');
  startPolling();
});
window.addEventListener('beforeunload', () => {
  stopPolling();
  abortIfInflight('restaurants');
  abortIfInflight('properties');
});
</script>

@endsection