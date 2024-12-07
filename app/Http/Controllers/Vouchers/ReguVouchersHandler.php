<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class ReguVouchersHandler extends Controller
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke(Request $request)
    {
        try {
            $result = $this->voucherService->reguVouchers();
            $regularizedVouchers = VoucherResource::collection($result['details']['regularized_vouchers']);

            $failedVouchers = array_map(function ($failedVoucher) {
                return [
                    'voucher' => new VoucherResource($failedVoucher['voucher']),
                    'error' => $failedVoucher['error'],
                ];
            }, $result['details']['failed_vouchers']);

            return response()->json([
                'data' => [
                    'processed' => $result['processed'],
                    'details' => [
                        'regularized_vouchers' => $regularizedVouchers,
                        'failed_vouchers' => $failedVouchers,
                    ]
                ]
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
