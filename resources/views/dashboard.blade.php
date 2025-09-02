@extends('layouts.dashboard')

@section('title', 'Dashboard الرئيسية')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #FC8700 0%, #6c757d 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        text-align: center;
    }
    
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    .welcome-text {
        position: relative;
        z-index: 2;
    }
    .quick-actions {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn {
        background: linear-gradient(135deg, #6c757d 0%, #FC8700 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin: 0.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .action-btn i {
        margin-left: 0.5rem;
        font-size: 1.1rem;
    }
    
    .recent-activity {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .activity-item {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid #e9ecef;
    }
    
    .activity-item:hover {
        background: #f8f9fa;
        border-left-color: #667eea;
    }
    
    .activity-time {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #2c3e50;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 170px;
        height: 3px;
        background: linear-gradient(90deg, #FC8700, #6c757d);
        border-radius: 2px;
    }
    
   
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .gradient-bg-1 { background: linear-gradient(135deg, #FF6B6B, #FF8E8E); }
    .gradient-bg-2 { background: linear-gradient(135deg, #4ECDC4, #7FDBDA); }
    .gradient-bg-3 { background: linear-gradient(135deg, #45B7D1, #73C6E6); }
    .gradient-bg-4 { background: linear-gradient(135deg, #96CEB4, #B8DCC6); }
    
</style>

<!-- Header Section -->
<div class="dashboard-header">
    <div class="welcome-text">
        <h1 class="mb-3">مرحبًا بك في لوحة التحكم</h1>
        <p class="mb-0 fs-5">إدارة شاملة لجميع خدمات منصة مسار</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h2 class="section-title">الإجراءات السريعة</h2>
    <div class="d-flex flex-wrap">
        <a href="/accounts" class="action-btn">
            <i class="bi bi-person-plus"></i>
            إدارة مستخدم جديد
        </a>
        <a href="/requests" class="action-btn">
            <i class="bi bi-plus-circle"></i>
            إدارة طلب جديد
        </a>
        <a href="/securityPermits" class="action-btn">
            <i class="bi bi-shield-plus"></i>
            إدارة تصريح أمني
        </a>
        <a href="/AppSettings" class="action-btn">
            <i class="bi bi-gear"></i>
            إعدادات النظام
        </a>
    </div>
</div>


@endsection

