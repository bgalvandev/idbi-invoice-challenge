<?php

namespace App\Jobs;

use App\Events\Vouchers\VouchersCreated;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private array $xmlContents;
    private User $user;
    private array $fileNames;

    /**
     * Create a new job instance.
     */
    public function __construct(array $xmlContents, User $user, array $fileNames)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
        $this->fileNames = $fileNames;
    }

    /**
     * Execute the job.
     */
    public function handle(VoucherService $voucherService): void
    {
        $vouchers = [];
        $failedVouchers = [];

        foreach ($this->xmlContents as $index => $xmlContent) {
            try {
                $result = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $vouchers[] = $result;
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'xml_content' => $xmlContent,
                    'error' => $e->getMessage(),
                    'file_name' => $this->fileNames[$index],

                ];
            }
        }
        $successfulVouchers = VoucherResource::collection($vouchers)->toArray(request());
        VouchersCreated::dispatch($successfulVouchers, $failedVouchers, $this->user);
    }
}
