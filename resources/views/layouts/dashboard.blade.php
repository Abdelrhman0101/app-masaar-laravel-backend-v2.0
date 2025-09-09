<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #FC8700;
            --primary-gray: #6c757d;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --white: #ffffff;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        body { 
            
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .sidebar {
    width: 280px;
    background: linear-gradient(180deg, var(--primary-orange) 0%, #e67600 100%);
    color: var(--white);
    height: 100vh;
    box-shadow: var(--shadow-lg);
    position: fixed; /* تغيير هام: جعل القائمة ثابتة */
    top: 0;
    right: 0; /* لأن التصميم عربي */
    z-index: 1030;
    transition: transform 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            pointer-events: none;
        }
        
        .sidebar .logo-section {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .sidebar .logo {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 20px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
            font-size: 2rem;
            color: var(--primary-orange);
            font-weight: bold;
        }
        
        .logo-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .sidebar h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-pills {
            position: relative;
            z-index: 1;
            
          
        }
        
        .sidebar .nav-link {
            color: var(--white);
            text-decoration: none;
            padding: 0.875rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link i {
            margin-left: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            transform: translateX(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link.active {
            background: var(--white);
            color: var(--primary-orange);
            font-weight: 600;
            box-shadow: var(--shadow);
        }
        
        .sidebar .user-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin: 0 1rem 1.5rem;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }
        
        .sidebar .user-info strong {
            font-size: 1.1rem;
        }
        
        .sidebar .user-info small {
            opacity: 0.8;
        }
        
        .logout-btn {
            background: rgba(220, 53, 69, 0.9);
            border: none;
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .logout-btn:hover {
            background: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
       .main-content {
    min-height: 100vh;
    margin-right: 280px; /* ترك مساحة للقائمة الجانبية */
    transition: margin-right 0.3s ease-in-out;
    width: calc(100% - 280px); /* حساب العرض المتبقي */
}
        .content-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* تحسينات للكروت */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: var(--white);
            border-bottom: 1px solid #e9ecef;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
        }
        
        .text-primary {
            color: var(--primary-orange) !important;
        }
        
        .btn-primary {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #e67600;
            border-color: #e67600;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(252, 135, 0, 0.3);
        }
        .mb-4 {
            background: linear-gradient(135deg, rgba(252, 135, 0, 0.05) 0%, rgba(248, 249, 250, 0.8) 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(252, 135, 0, 0.08);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(252, 135, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            
        }
        
        .mb-4::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(252, 135, 0, 0.08), transparent);
            transition: left 0.5s ease;
            
        }
        
        .mb-4:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(252, 135, 0, 0.12);
            border-color: rgba(252, 135, 0, 0.2);
            background: linear-gradient(135deg, rgba(252, 135, 0, 0.08) 0%, rgba(248, 249, 250, 0.9) 100%);
        }
        
        .mb-4:hover::before {
            left: 100%;
        }
        
        .mb-4 * {
            position: relative;
            z-index: 1;
            
        }
        .row{
            width: 100%;
        }
        
        /* تحسينات للجداول */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--primary-orange);
            color: var(--white);
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table tbody tr:hover {
            background: rgba(252, 135, 0, 0.05);
        }
        
        /* تحسينات للنماذج */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-orange), #e67600);
            color: var(--white);
            border-radius: 16px 16px 0 0;
            border: none;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
            .nav-pills {
        /* background: #f8f9fa; */
        border-radius: 12px;
        padding: 0.5rem;
              
    }
    
    .nav-pills .nav-link {
        border-radius: 8px;
        margin: 0 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        color: var(--light-gray);
        background-color: #F38100;
        transition: all 0.3s ease;
        border: none;
      
      
    }
    
    .nav-pills .nav-link:hover {
        background: rgba(252, 135, 0, 0.1);
        color: var(--dark-gray);
    }
    
    .nav-pills .nav-link.active {
        background: var(--primary-orange);
        color: var(--white);
        box-shadow: 0 2px 8px rgba(252, 135, 0, 0.3);
    }
        /* تصميم مربع البحث المتقدم */
        .search-container {
            position: relative;
            max-width: 500px;
            margin: 0 auto 2rem;
        }
        
        .search-box {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 0.75rem 3rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid transparent;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            margin-bottom: 5%;
        }
        
        .search-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, var(--primary-orange), #ff9500, var(--primary-orange));
            background-size: 200% 200%;
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
            animation: gradientShift 3s ease infinite;
        }
        
        .search-box:hover::before,
        .search-box:focus-within::before {
            opacity: 0.1;
        }
        
        .search-box:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 12px 40px rgba(252, 135, 0, 0.2);
            border-color: rgba(252, 135, 0, 0.3);
        }
        
        .search-box:focus-within {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 50px rgba(252, 135, 0, 0.3);
            border-color: var(--primary-orange);
        }
        
        .search-input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--dark-gray);
            padding: 0.5rem 3rem 0.5rem 1rem;
            letter-spacing: 0.5px;
        }
        
        .search-input::placeholder {
            color: rgba(108, 117, 125, 0.7);
            font-weight: 400;
            transition: all 0.3s ease;
        }
        
        .search-input:focus::placeholder {
            color: rgba(108, 117, 125, 0.4);
            transform: translateX(5px);
        }
        
        .search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-orange);
            font-size: 1.3rem;
            transition: all 0.4s ease;
            cursor: pointer;
        }
        
        .search-box:hover .search-icon {
            transform: translateY(-50%) rotate(90deg) scale(1.1);
            color: #e67600;
        }
        
        .search-box:focus-within .search-icon {
            transform: translateY(-50%) rotate(180deg) scale(1.2);
            color: var(--primary-orange);
        }
        
        /* تأثيرات إضافية للبحث */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 0 0 20px 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            pointer-events: none;
        }
        
        .search-suggestions.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }
        
        .search-suggestion-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .search-suggestion-item:hover {
            background: rgba(252, 135, 0, 0.1);
            transform: translateX(5px);
        }
        
        .search-suggestion-item i {
            margin-left: 0.75rem;
            color: var(--primary-orange);
            font-size: 1.1rem;
        }
        
        /* تحريك الخلفية */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* تأثيرات الاستجابة */
        @media (max-width: 768px) {
            .search-container {
                max-width: 100%;
                margin: 0 0 1.5rem;
            }
            
            .search-box {
                padding: 0.6rem 1.2rem;
            }
            
            .search-input {
                font-size: 1rem;
                padding: 0.4rem 2.5rem 0.4rem 0.8rem;
            }
            
            .search-icon {
                right: 1.2rem;
                font-size: 1.2rem;
            }
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
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    
    .details-image {
        max-width: 120px;
        max-height: 80px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
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
        font-size: 1.5rem;
    }
    
    .details-nested {
        margin-right: 15px;
        border-right: 2px solid rgba(252, 135, 0, 0.2);
        padding-right: 15px;
    }
    
    .nested-item {
        margin-bottom: 15px;
        background: rgba(252, 135, 0, 0.03);
        border-radius: 10px;
        padding: 15px;
        border: 1px solid rgba(252, 135, 0, 0.1);
    }
   .nav-pills{
    justify-content: center;
   }
    
    /* الرسوم المتحركة الجديدة */
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
     
     /* التصميم المتجاوب للشاشات الصغيرة */
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
         
         .modern-modal-body {
             padding: 20px 15px;
         }
         
         .details-item {
             padding: 12px;
         }
         
         .details-label {
             font-size: 0.85rem;
             gap: 8px;
         }
         
         .details-content {
             margin-right: 15px;
         }
         
         .details-image {
             max-width: 80px;
             max-height: 60px;
         }
     }
      
      
#sidebarToggle {
    display: none; /* مخفي على الشاشات الكبيرة */
}
.sidebar-overlay {
    display: none; /* مخفي دائماً إلا عند فتح القائمة */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1029; /* خلف القائمة وأمام المحتوى */
}

/* شاشات التابلت والموبايل (أقل من 992px) */
@media (max-width: 991.98px) {
  
.quick-actions-grid {
    display: flex;
    flex-direction: column; /* ترتيب الأزرار عمودياً */
    gap: 1rem; /* مسافة 1rem بين كل زر */
    align-items: center; /* توسيط الأزرار أفقياً داخل الحاوية */
    width: 100%;
}

.action-btn {
    width: 100%; /* جعل الزر يأخذ العرض الكامل للحاوية */
    max-width: 400px; /* حد أقصى للعرض ليبدو جيداً على الشاشات الأكبر قليلاً */
    margin: 0; /* إزالة الهوامش والاعتماد على gap */
    justify-content: center; /* توسيط الأيقونة والنص داخل الزر */
}

.section-title {
    font-size: 1.2rem;
    text-align: center; /* توسيط العنوان على الموبايل */
}
.section-title::after {
    left: 50%; /* تحريك الخط ليكون في المنتصف */
    transform: translateX(-50%);
}
    .sidebar {
        transform: translateX(100%); /* إخفاء القائمة خارج الشاشة إلى اليمين */
    }

    .main-content {
        margin-right: 0; /* المحتوى يأخذ كامل العرض */
        width: 100%;
    }

    #sidebarToggle {
        display: block; /* إظهار زر فتح القائمة */
        position: fixed;
        top: 15px;
        right: 15px;
        z-index: 1031; /* أعلى z-index ليكون فوق كل شيء */
        background: var(--white);
        color: var(--primary-orange);
        border: 1px solid var(--primary-orange);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        box-shadow: var(--shadow-lg);
    }

    .content-wrapper {
        padding: 1rem; /* تقليل الحواشي على الشاشات الصغيرة */
    }

    /* حالة فتح القائمة */
    body.sidebar-open .sidebar {
        transform: translateX(0); /* إظهار القائمة بإعادتها لمكانها الأصلي */
    }

    body.sidebar-open .sidebar-overlay {
        display: block; /* إظهار الخلفية المعتمة */
    }
}
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <script>
        // حماية الصفحة
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
        }
    </script>
    <div class="d-flex">
        <nav class="sidebar d-flex flex-column">
            <div class="logo-section">
                <div class="logo">
                    <img src="https://msar.app/storage/uploads/images/masar.png" alt="Masar Logo" class="logo-image">
                </div>
                <h4>مسار</h4>
                <small style="opacity: 0.8;">لوحة التحكم</small>
            </div>
            
            <div class="user-info" id="userInfo">
                <!-- سيتم تعبئة معلومات المستخدم هنا -->
            </div>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link">
                        <i class="bi bi-house-door"></i>
                        الرئيسية
                    </a>
                </li>
               <li class="nav-item">
                    <a href="/notifications" class="nav-link">
                        <i class="bi bi-house-door"></i>
                        إدارة الأشعارات
                  
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/accounts" class="nav-link">
                        <i class="bi bi-people"></i>
                        إدارة الحسابات
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/requests" class="nav-link">
                        <i class="bi bi-clipboard-check"></i>
                        إدارة الطلبات
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/securityPermits" class="nav-link">
                        <i class="bi bi-shield-check"></i>
                        التصاريح الأمنية
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('chat') }}" class="nav-link">
                        <i class="bi bi-chat-dots-fill"></i>
                        محادثات العملاء
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/appController" class="nav-link">
                        <i class="bi bi-gear"></i>
                        إدارة التطبيق
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/AppSettings" class="nav-link">
                        <i class="bi bi-info-circle"></i>
                        معلومات التطبيق
                    </a>
                </li>
              
            </ul>

            <button id="logoutBtn" class="logout-btn">
                <i class="bi bi-box-arrow-right me-2"></i>
                تسجيل الخروج
            </button>
        </nav>
        <main class="flex-fill main-content">
          <button class="btn" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>
            <div class="content-wrapper">
              
                
                @yield('content')
            </div>
        </main>
      <div class="sidebar-overlay" id="sidebarOverlay"></div>
    </div>

    <script>
        // عرض اسم المستخدم من localStorage
        var user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null;
        if (user) {
            document.getElementById('userInfo').innerHTML = `
                <div>
                    <strong>${user.name}</strong><br>
                    <small>${user.email}</small>
                </div>
            `;
        }

        // زر تسجيل الخروج
        document.getElementById('logoutBtn').onclick = function () {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        };
        
        // وظائف مربع البحث المتقدم
        const searchInput = document.getElementById('globalSearch');
        const searchIcon = document.getElementById('searchIcon');
        const searchSuggestions = document.getElementById('searchSuggestions');
        
        // بيانات الاقتراحات
        const searchData = [
            { title: 'إدارة الحسابات', url: '/accounts', icon: 'bi-people' },
            { title: 'إدارة الطلبات', url: '/requests', icon: 'bi-clipboard-check' },
            { title: 'التصاريح الأمنية', url: '/securityPermits', icon: 'bi-shield-check' },
            { title: 'إدارة التطبيق', url: '/appController', icon: 'bi-gear' },
            { title: 'معلومات التطبيق', url: '/AppSettings', icon: 'bi-info-circle' },
            { title: 'الرئيسية', url: '/dashboard', icon: 'bi-house-door' }
        ];
        
        // البحث والتصفية
        function filterSuggestions(query) {
            if (!query.trim()) {
                searchSuggestions.classList.remove('show');
                return;
            }
            
            const filtered = searchData.filter(item => 
                item.title.includes(query) || 
                item.title.toLowerCase().includes(query.toLowerCase())
            );
            
            if (filtered.length > 0) {
                searchSuggestions.innerHTML = filtered.map(item => `
                    <div class="search-suggestion-item" onclick="navigateTo('${item.url}')">
                        <i class="bi ${item.icon}"></i>
                        <span>${item.title}</span>
                    </div>
                `).join('');
                searchSuggestions.classList.add('show');
            } else {
                searchSuggestions.innerHTML = `
                    <div class="search-suggestion-item">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>لا توجد نتائج</span>
                    </div>
                `;
                searchSuggestions.classList.add('show');
            }
        }
        
        // التنقل إلى الصفحة
        function navigateTo(url) {
            window.location.href = url;
        }
        
        // أحداث مربع البحث - مع فحص وجود العناصر
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                filterSuggestions(e.target.value);
            });
            
            searchInput.addEventListener('focus', function() {
                if (this.value.trim()) {
                    filterSuggestions(this.value);
                }
            });
            
            // البحث عند الضغط على Enter
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query) {
                        const firstMatch = searchData.find(item => 
                            item.title.includes(query) || 
                            item.title.toLowerCase().includes(query.toLowerCase())
                        );
                        if (firstMatch) {
                            navigateTo(firstMatch.url);
                        }
                    }
                }
            });
        }
        
        // إخفاء الاقتراحات عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (searchSuggestions && !e.target.closest('.search-container')) {
                searchSuggestions.classList.remove('show');
            }
        });
        
        // تأثير النقر على أيقونة البحث
        if (searchIcon) {
            searchIcon.addEventListener('click', function() {
                if (searchInput) {
                    const query = searchInput.value.trim();
                    if (query) {
                        const firstMatch = searchData.find(item => 
                            item.title.includes(query) || 
                            item.title.toLowerCase().includes(query.toLowerCase())
                        );
                        if (firstMatch) {
                            navigateTo(firstMatch.url);
                        }
                    } else {
                        searchInput.focus();
                    }
                }
            });
        }
    </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const body = document.body;

        // عند الضغط على زر فتح القائمة
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                body.classList.toggle('sidebar-open');
            });
        }

        // عند الضغط على الخلفية المعتمة لإغلاق القائمة
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                body.classList.remove('sidebar-open');
            });
        }
    });
</script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
      (function () {
        if (window.__echoInitialized) return;
        try {
          // The Echo constructor is exposed by the IIFE build
          if (typeof Echo !== 'function') {
            console.warn('Laravel Echo library is not available on the page.');
            return;
          }

          const token = (function(){
            try { return localStorage.getItem('token') || ''; } catch (e) { return ''; }
          })();

          // Read config from environment via Blade
          const PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
          const PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER', 'mt1') }}";
          const PUSHER_HOST = "{{ env('PUSHER_HOST') }}"; // leave empty if using Pusher Cloud
          const PUSHER_PORT = Number("{{ env('PUSHER_PORT', 6001) }}");
          const PUSHER_SCHEME = "{{ env('PUSHER_SCHEME', request()->isSecure() ? 'https' : 'http') }}";

          let echoOptions = {
            broadcaster: 'pusher',
            key: PUSHER_APP_KEY,
            cluster: PUSHER_APP_CLUSTER,
            forceTLS: (PUSHER_SCHEME === 'https') || (location.protocol === 'https:'),
            encrypted: (PUSHER_SCHEME === 'https') || (location.protocol === 'https:'),
            disableStats: true,
            authEndpoint: "{{ url('/api/broadcasting/auth') }}",
            auth: {
              headers: {
                'Authorization': token ? `Bearer ${token}` : '',
                'Accept': 'application/json'
              }
            }
          };

          // If self-hosted websockets server is used, configure host/ports
          if (PUSHER_HOST && PUSHER_HOST.trim() !== '') {
            echoOptions.wsHost = PUSHER_HOST;
            echoOptions.wsPort = PUSHER_PORT;
            echoOptions.wssPort = PUSHER_PORT;
            echoOptions.enabledTransports = ['ws', 'wss'];
          }

          // Initialize and expose globally
          window.Echo = new Echo(echoOptions);
          window.__echoInitialized = true;
          console.log('%cEcho initialized','color:#28a745');
        } catch (err) {
          console.error('Failed to initialize Echo:', err);
        }
      })();
    </script>
  
    @stack('scripts')
    @yield('scripts')

</body>
</html>
