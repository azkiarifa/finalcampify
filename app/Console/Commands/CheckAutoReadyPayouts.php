<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Services\SellerOrderService;

class CheckAutoReadyPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payouts:check-auto-ready';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis periksa dan ubah status dana pesanan menjadi Siap Dicairkan setelah 2 hari (48 jam) tanpa komplain';

    /**
     * Execute the console command.
     */
    public function handle(SellerOrderService $sellerOrderService): int
    {
        $holdingHours = SellerOrderService::HOLDING_HOURS; // 48 jam
        $threshold = now()->subHours($holdingHours);

        // Ambil semua SellerOrder yang delivered sebelum threshold
        $candidates = SellerOrder::with(['payout', 'order'])
            ->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $threshold)
            ->whereDoesntHave('payout', function ($query) {
                $query->where('status', Payout::STATUS_DISBURSED);
            })
            ->get();

        $updatedCount = 0;

        foreach ($candidates as $sellerOrder) {
            // Cek apakah ada komplain aktif
            if ($sellerOrderService->hasActiveComplaint($sellerOrder)) {
                $this->line("Pesanan #{$sellerOrder->id} memiliki komplain aktif, dana tetap ditahan.");
                continue;
            }

            // Sync payout sehingga otomatis beralih ke READY_TO_DISBURSE
            $payout = $sellerOrderService->syncPayout($sellerOrder);
            if ($payout->status === Payout::STATUS_READY_TO_DISBURSE) {
                $updatedCount++;
                $this->info("Pesanan #{$sellerOrder->id} ({$sellerOrder->seller_order_number}) status dana berubah -> SIAP DICAIRKAN.");
            }
        }

        $this->info("Pemeriksaan selesai. Total {$updatedCount} dana pesanan beralih ke Siap Dicairkan.");

        return Command::SUCCESS;
    }
}
