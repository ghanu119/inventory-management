<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'hsn_code',
        'price',
        'gst_rate',
        'stock_quantity',
        'is_gst_included',
        'warranty_years',
        'custom_short_text',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_gst_included' => 'boolean',
        'warranty_years' => 'decimal:2',
    ];

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function productSerials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class)->orderByDesc('created_at');
    }

    public function scopeAvailableStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->where('stock_quantity', '<=', $threshold);
    }

    public function reduceStock(int $quantity, string $reason = 'sale', ?string $referenceId = null): StockHistory
    {
        $stockBefore = $this->stock_quantity;
        $this->decrement('stock_quantity', $quantity);

        return StockHistory::create([
            'product_id' => $this->id,
            'type' => 'out',
            'quantity' => $quantity,
            'reason' => $reason,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore - $quantity,
            'reference_id' => $referenceId,
        ]);
    }

    public function addStock(int $quantity, string $reason = 'purchase', ?string $notes = null, ?string $referenceId = null): StockHistory
    {
        $stockBefore = $this->stock_quantity;
        $this->increment('stock_quantity', $quantity);

        return StockHistory::create([
            'product_id' => $this->id,
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => $reason,
            'notes' => $notes,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore + $quantity,
            'reference_id' => $referenceId,
        ]);
    }

    public function adjustStock(int $newQuantity, string $reason = 'adjustment', ?string $notes = null): StockHistory
    {
        $stockBefore = $this->stock_quantity;
        $quantity = $newQuantity - $stockBefore;
        $this->update(['stock_quantity' => $newQuantity]);

        return StockHistory::create([
            'product_id' => $this->id,
            'type' => 'adjust',
            'quantity' => abs($quantity),
            'reason' => $reason,
            'notes' => $notes,
            'stock_before' => $stockBefore,
            'stock_after' => $newQuantity,
        ]);
    }

    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }
}

