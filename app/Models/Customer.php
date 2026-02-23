<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'gst_number',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeSearchByContact(Builder $query, string $search): Builder
    {
        return $query->where('phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%");
    }

    public static function findOrCreateByContact(string $phone, string $email, array $attributes = []): self
    {
        $query = self::query();
        
        if (!empty($phone) && !empty($email)) {
            $customer = $query->where(function($q) use ($phone, $email) {
                $q->where('phone', $phone)->orWhere('email', $email);
            })->first();
        } elseif (!empty($phone)) {
            $customer = $query->where('phone', $phone)->first();
        } elseif (!empty($email)) {
            $customer = $query->where('email', $email)->first();
        } else {
            $customer = null;
        }

        if (!$customer) {
            $data = array_merge([
                'phone' => !empty($phone) ? $phone : null,
                'email' => !empty($email) ? $email : null,
            ], $attributes);
            $customer = self::create($data);
        } else {
            // Update customer if new attributes provided
            if (!empty($attributes)) {
                $customer->update($attributes);
                $customer->refresh();
            }
        }

        return $customer;
    }
}
