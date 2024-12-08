<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Jobs\ProcessVouchers;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $xmlFiles = $request->file('files');

            if (!is_array($xmlFiles)) {
                $xmlFiles = [$xmlFiles];
            }

            if (empty($xmlFiles)) {
                throw new \Exception('No se subieron archivos');
            }

            $xmlContents = [];
            $fileNames = [];

            foreach ($xmlFiles as $xmlFile) {

                if (!$xmlFile || !$xmlFile->isValid()) {
                    throw new \Exception('Archivo subido no válido');
                }

                $xmlContents[] = file_get_contents($xmlFile->getRealPath());
                $fileNames[] = $xmlFile->getClientOriginalName();
            }

            $user = auth()->user();

            ProcessVouchers::dispatch($xmlContents, $user, $fileNames);

            return response()->json([
                'message' => 'Comprobantes en proceso de registro. Recibirá un correo con el resumen.',
                'files_processed' => count($xmlContents)
            ], 202);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
