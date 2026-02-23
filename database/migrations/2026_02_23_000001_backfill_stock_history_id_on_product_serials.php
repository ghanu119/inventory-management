<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Link existing product_serials (with null stock_history_id) to the
     * corresponding stock-in history row by matching product and created_at order.
     */
    public function up(): void
    {
        $productIds = DB::table('product_serials')
            ->whereNull('stock_history_id')
            ->distinct()
            ->pluck('product_id');

        foreach ($productIds as $productId) {
            $histories = DB::table('stock_histories')
                ->where('product_id', $productId)
                ->where('type', 'in')
                ->orderBy('created_at')
                ->get(['id', 'quantity', 'created_at']);

            $serials = DB::table('product_serials')
                ->where('product_id', $productId)
                ->whereNull('stock_history_id')
                ->orderBy('created_at')
                ->get(['id', 'created_at']);

            if ($serials->isEmpty() || $histories->isEmpty()) {
                continue;
            }

            $serialIndex = 0;
            foreach ($histories as $history) {
                $needed = (int) $history->quantity;
                $historyCreated = $history->created_at;

                for ($i = 0; $i < $needed && $serialIndex < $serials->count(); $i++, $serialIndex++) {
                    $serial = $serials[$serialIndex];
                    DB::table('product_serials')
                        ->where('id', $serial->id)
                        ->update([
                            'stock_history_id' => $history->id,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Optional: set stock_history_id back to null for backfilled rows.
        // We don't have a way to know which were backfilled, so leave as-is.
    }
};
