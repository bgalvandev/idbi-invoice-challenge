<?php

use App\Http\Controllers\Vouchers\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\ReguVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\TotalVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::delete('/{id}', DeleteVoucherHandler::class);
        Route::post('/', StoreVouchersHandler::class);

        Route::get('/totalAmounts', TotalVoucherHandler::class);
        Route::post('/regularize', ReguVouchersHandler::class);
    }
);
