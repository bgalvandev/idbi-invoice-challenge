<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Procesamiento de Comprobantes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .details {
            display: none;
        }

        .details.open {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-6 py-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white py-4 px-6">
                <h1 class="text-3xl font-semibold">Estimado {{ $user->name }},</h1>
                <p class="mt-2 text-lg">Hemos recibido tus comprobantes con los siguientes detalles:</p>
            </div>

            <div class="p-6">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                        Comprobantes Procesados Exitosamente ({{ count($successfulVouchers) }})
                    </h2>
                    
                    @if(count($successfulVouchers) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white shadow-md rounded-lg">
                                <thead class="bg-blue-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-gray-600">ID</th>
                                        <th class="px-6 py-3 text-left text-gray-600">Voucher</th>
                                        <th class="px-6 py-3 text-right text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($successfulVouchers as $index => $voucher)
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="px-6 py-4">{{ $voucher['id'] }}</td>
                                        <td class="px-6 py-4">{{ $voucher['voucher'] }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button 
                                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" 
                                                onclick="toggleDetails('details-success-{{ $index }}')">
                                                Ver Detalle
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="details-success-{{ $index }}" class="details">
                                        <td colspan="3" class="px-6 py-4 bg-gray-50">
                                            <ul class="list-disc pl-6 text-gray-700">
                                                <li>Nombre del Emisor: {{ $voucher['issuer_name'] }}</li>
                                                <li>Tipo de Documento del Emisor: {{ $voucher['issuer_document_type'] }}</li>
                                                <li>Número de Documento del Emisor: {{ $voucher['issuer_document_number'] }}</li>
                                                <li>Nombre del Receptor: {{ $voucher['receiver_name'] }}</li>
                                                <li>Tipo de Documento del Receptor: {{ $voucher['receiver_document_type'] }}</li>
                                                <li>Número de Documento del Receptor: {{ $voucher['receiver_document_number'] }}</li>
                                                <li>Monto Total: {{ $voucher['total_amount'] }}</li>
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 mt-4">No se procesaron comprobantes exitosamente.</p>
                    @endif
                </section>
                <section>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                        Comprobantes con Errores ({{ count($failedVouchers) }})
                    </h2>

                    @if(count($failedVouchers) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white shadow-md rounded-lg">
                                <thead class="bg-red-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-gray-600">Nombre del Archivo:</th>
                                        <th class="px-6 py-3 text-left text-gray-600">Mensaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failedVouchers as $index => $failed)
                                    <tr class="border-b hover:bg-red-50">
                                        <td class="px-6 py-4">{{ $failed['file_name'] }}</td>
                                        <td class="px-6 py-4">{{ $failed['error'] }}</td>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 mt-4">No hubo errores en el procesamiento.</p>
                    @endif
                </section>
            </div>
            <div class="bg-gray-200 p-6 text-center">
                <p class="text-gray-600">Gracias por usar nuestro servicio!</p>
            </div>
        </div>
    </div>
    <script>
        function toggleDetails(id) {
            const allDetails = document.querySelectorAll('.details');
            const selectedDetail = document.getElementById(id);
            
            if (selectedDetail.classList.contains('open')) {
                selectedDetail.classList.remove('open');
            } else {
                allDetails.forEach(function(detail) {
                    detail.classList.remove('open');
                });
                selectedDetail.classList.add('open');
            }
        }
    </script>
</body>
</html>
