<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'gst_number',
        'address',
        'phone',
        'email',
        'invoice_terms_and_conditions',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    public static function getCompany(): ?self
    {
        return self::first();
    }

    /**
     * Display name for the app: company name from DB, then config(app.name), then fallback.
     */
    public static function getAppName(): string
    {
        try {
            $company = self::getCompany();
            if ($company && ! empty(trim((string) ($company->name ?? '')))) {
                return trim($company->name);
            }
        } catch (\Throwable $e) {
            // e.g. companies table not yet migrated
        }

        return config('app.name') ?: 'Inventory Management';
    }
}
