<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class TotalVoucherHandler extends Controller
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke()
    {
        $user = auth()->user();

        return $this->voucherService->getTotalAmounts($user);
    }
}
