<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Store;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama',
        'receiver_name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'address',
        'city',
        'district',
        'postal_code',
        'phone',
        'ktp_image',
        'ktp_verified_at',
        'is_fraud',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login' => 'datetime',
        'ktp_verified_at' => 'datetime',
        'is_fraud' => 'boolean',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['nama'] = $value;
    }

    // 🔥 RELASI KE TOKO
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\Pembeli\UserAddress::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id', 'id');
    }

    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'sender_id', 'id');
    }

    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id', 'id');
    }

    public function buyerConversations()
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function sellerConversations()
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function isOnline(int $minutes = 5): bool
    {
        $hasActiveSession = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->subMinutes($minutes)->getTimestamp())
            ->exists();

        if ($hasActiveSession) {
            return true;
        }

        return \Illuminate\Support\Facades\DB::table('personal_access_tokens')
            ->where('tokenable_id', $this->id)
            ->where('last_used_at', '>=', now()->subMinutes($minutes))
            ->exists();
    }

    public function getLastSeenAttribute(): ?string
    {
        $lastSession = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $this->id)
            ->orderByDesc('last_activity')
            ->first();

        $lastToken = \Illuminate\Support\Facades\DB::table('personal_access_tokens')
            ->where('tokenable_id', $this->id)
            ->orderByDesc('last_used_at')
            ->first();

        $sessionTime = $lastSession ? \Carbon\Carbon::createFromTimestamp($lastSession->last_activity) : null;
        $tokenTime = $lastToken?->last_used_at ? \Carbon\Carbon::parse($lastToken->last_used_at) : null;
        $loginTime = $this->last_login ? \Carbon\Carbon::parse($this->last_login) : null;

        $latest = collect([$sessionTime, $tokenTime, $loginTime])->filter()->max();

        return $latest ? $latest->diffForHumans() : null;
    }
}
