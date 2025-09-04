<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// تم الاحتفاظ بكل الموديلات الخاصة بك
use App\Models\NormalUser;
use App\Models\RealEstate;
use App\Models\RestaurantDetail;
use App\Models\CarRental;
// لنفترض وجود هذه الموديلات أيضاً بناءً على الكود الأصلي
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\SecurityPermit;
use App\Models\CarServiceOrder;
use App\Models\Conversation; // <-- إضافة مهمة
use App\Models\Message; // <-- إضافة مهمة

use Illuminate\Database\Eloquent\Relations\HasMany;  // تأكد من أن هذا المسار صحيح
use Illuminate\Database\Eloquent\Relations\HasOne;   // تأكد من أن هذا المسار صحيح

// الموديلات الأخرى (كما هي)
use App\Models\Order; // مهم جدًا لعلاقة orders()

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'governorate',
        'user_type',
        'is_approved',
        'the_best',
        'google_id',
        'avatar',
        'login_type',
        'email_verification_code',
        'email_verification_expires_at',
        'email_verification_sent_at',
        'email_verification_attempts',
        'password_reset_code',
        'password_reset_expires_at',
        'password_reset_sent_at',
        'password_reset_attempts',
        'is_email_verified',
        'account_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'password_reset_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_verification_expires_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'password_reset_expires_at' => 'datetime',
            'password_reset_sent_at' => 'datetime',
            'is_email_verified' => 'boolean',
            'account_active' => 'boolean',
            'is_approved' => 'boolean',
            'the_best' => 'boolean',
        ];
    }

    // --- العلاقات الخاصة بتفاصيل أنواع الحسابات (تبقى كما هي) ---
    public function normalUser()
    {
        return $this->hasOne(NormalUser::class);
    }

    public function realEstate()
    {
        return $this->hasOne(RealEstate::class);
    }

    // public function restaurantDetail()
    // {
    //     return $this->hasOne(RestaurantDetail::class);
    // }

    public function carRental()
    {
        return $this->hasOne(CarRental::class);
    }

    // --- العلاقات الأخرى في تطبيقك (تبقى كما هي) ---
    public function appointments()
    {
        // return $this->hasMany(Appointment::class);
       return $this->hasMany(Appointment::class, 'customer_id');

    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function securityPermits()
    {
        return $this->hasMany(SecurityPermit::class);
    }

    public function carServiceOrders()
    {
        return $this->hasMany(CarServiceOrder::class, 'client_id');
    }

    public function providedCarOrders()
    {
        return $this->hasMany(CarServiceOrder::class, 'provider_id');
    }
    

    // --- !! قسم المحادثات الجديد والمبسط !! ---

    /**
     * إذا كان المستخدم من النوع العادي (غير مشرف)، فهذه هي العلاقة لجلب محادثته الوحيدة مع الإدارة.
     * العلاقة هي hasOne لأن تصميمنا يضمن أن لكل مستخدم محادثة واحدة فقط.
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class, 'user_id');
    }


    /**
     * علاقة عامة لجلب كل الرسائل التي أرسلها هذا المستخدم عبر النظام.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

     public function restaurantDetail()
    {
        return $this->hasOne(RestaurantDetail::class);
    }

     public function orders(): HasMany
    {
        // افترض أن جدول `orders` يحتوي على عمود `user_id`
        return $this->hasMany(Order::class, 'user_id');
    }
}
