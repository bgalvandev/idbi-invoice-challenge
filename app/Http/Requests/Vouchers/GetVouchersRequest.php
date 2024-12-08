<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'integer', 'gt:0'],
            'paginate' => ['required', 'integer', 'gt:0'],

            'type' => ['nullable', 'string', 'max:15'],
            'serie' => ['nullable', 'string', 'max:10'],
            'number' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:3'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ];
    }

    public function filters(): array
    {
        return $this->only([
            'serie',
            'numero',
            'tipo_comprobante',
            'moneda',
            'fecha_inicio',
            'fecha_fin',
        ]);
    }
}
