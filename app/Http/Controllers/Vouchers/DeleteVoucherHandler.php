<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteVoucherHandler
{
    public function __invoke($id)
    {
        try {
            $voucher = Voucher::find($id);

            if (!$voucher) {
                throw new \Exception('Comprobante no encontrado.');
            }

            if (Gate::denies('delete', $voucher)) {
                throw new \Exception('No tienes permiso para eliminar este comprobante.');
            }

            $voucher->delete();

            return response()->json([
                'message' => 'Comprobante eliminado exitosamente.'
            ]);
        } catch (Exception $exception) {

            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
