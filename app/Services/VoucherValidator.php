<?php

namespace App\Services;

class VoucherValidator
{
    private const VALIDATION_RULES = [
        'type' => '/^[a-zA-Z0-9]{2}$/',
        'invoice_id' => '/^[FBN][0-9]{3}-[0-9]{1,8}$/',
        'currency' => '/^[A-Z]{3}$/',
        'issuer_document_type' => '/^[a-zA-Z0-9]{1}$/',
        'issuer_document_number' => '/^[0-9]{11}$/',
        'receiver_document_type' => '/^[a-zA-Z0-9]{1}$/',
        'receiver_document_number' => '/^[0-9]{11}$/',
        'total_amount' => '/^[0-9]{1,12}(\.[0-9]{1,2})?$/'
    ];

    private const ERROR_MESSAGES = [
        'type' => "El código de tipo de documento no cumple con el formato (an2).",
        'invoice_id' => "El ID de la factura no cumple con el formato (F###-NNNNNNNN).",
        'currency' => "El código de moneda no cumple con el formato (an3).",
        'issuer_name' => "El nombre comercial del emisor excede el máximo de 100 caracteres (an..100).",
        'issuer_document_type' => "El tipo de documento del emisor no cumple con el formato (an1).",
        'issuer_document_number' => "El número de documento del emisor no cumple con el formato (n11).",
        'receiver_name' => "El nombre del receptor excede el máximo de 100 caracteres (an..100).",
        'receiver_document_type' => "El tipo de documento del receptor no cumple con el formato (an1).",
        'receiver_document_number' => "El número de documento del receptor no cumple con el formato (n11).",
        'total_amount' => "El total (incluye impuestos) no cumple con el formato (n(12,2))."
    ];

    public function validate(array $data): void
    {
        foreach (
            [
                'type',
                'invoice_id',
                'currency',
                'issuer_document_type',
                'issuer_document_number',
                'receiver_document_type',
                'receiver_document_number',
                'total_amount'
            ] as $field
        ) {
            $this->validateRegexField($data, $field);
        }

        $this->validateTextLength($data, 'issuer_name', 100);
        $this->validateTextLength($data, 'receiver_name', 100);
    }

    private function validateRegexField(array $data, string $field): void
    {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            throw new \InvalidArgumentException("El campo '$field' es obligatorio y no puede estar vacío.");
        }

        if (!preg_match(self::VALIDATION_RULES[$field], $data[$field])) {
            throw new \InvalidArgumentException(self::ERROR_MESSAGES[$field]);
        }
    }

    private function validateTextLength(array $data, string $field, int $maxLength): void
    {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            throw new \InvalidArgumentException("El campo '$field' es obligatorio y no puede estar vacío.");
        }

        if (strlen($data[$field]) > $maxLength) {
            throw new \InvalidArgumentException(self::ERROR_MESSAGES[$field]);
        }
    }
}
