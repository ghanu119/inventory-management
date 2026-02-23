<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSerial extends Model
{
    protected $fillable = [
        'product_id',
        'stock_history_id',
        'serial_number',
        'status',
        'invoice_item_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockHistory(): BelongsTo
    {
        return $this->belongsTo(StockHistory::class);
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function scopeAvailableForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId)->where('status', 'available');
    }
}
