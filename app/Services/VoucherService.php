<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    protected $validator;

    public function __construct(VoucherValidator $validator)
    {
        $this->validator = $validator;
    }

    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $data = [
            'type' => (string) $xml->xpath('//cbc:InvoiceTypeCode')[0],
            'invoice_id' => (string) $xml->xpath('//cbc:ID')[0],
            'currency' => (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0],
            'issuer_name' => (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0],
            'issuer_document_type' => (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0],
            'issuer_document_number' => (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0],
            'receiver_name' => (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0],
            'receiver_document_type' => (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0],
            'receiver_document_number' => (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0],
            'total_amount' => (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0],
        ];

        $this->validator->validate($data);

        $idParts = explode('-', $data['invoice_id']);
        $serie = $idParts[0] ?? $data['invoice_id'];
        $number = $idParts[1] ?? '';


        $voucher = new Voucher([
            'type' => $data['type'],
            'serie' => $serie,
            'number' => $number,
            'currency' => $data['currency'],
            'issuer_name' => $data['issuer_name'],
            'issuer_document_type' => $data['issuer_document_type'],
            'issuer_document_number' => $data['issuer_document_number'],
            'receiver_name' => $data['receiver_name'],
            'receiver_document_type' => $data['receiver_document_type'],
            'receiver_document_number' => $data['receiver_document_number'],
            'total_amount' => $data['total_amount'],
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    public function reguVouchers()
    {
        $processed = [
            'total' => 0,
            'regularized' => 0,
            'failed' => 0,
        ];

        $details = [
            'regularized_vouchers' => [],
            'failed_vouchers' => [],
        ];

        Voucher::whereNull('type')
            ->orWhereNull('serie')
            ->orWhereNull('number')
            ->orWhereNull('currency')
            ->chunk(100, function ($vouchers) use (&$processed, &$details) {
                foreach ($vouchers as $voucher) {
                    try {
                        $xml = new SimpleXMLElement($voucher->xml_content);
                        $invoiceId = (string) $xml->xpath('//cbc:ID')[0];
                        $idParts = explode('-', $invoiceId);
                        $serie = $idParts[0] ?? $invoiceId;
                        $number = $idParts[1] ?? '';
                        $type = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
                        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

                        $voucher->update([
                            'type' => $type,
                            'serie' => $serie,
                            'number' => $number,
                            'currency' => $currency,
                        ]);

                        $processed['regularized']++;
                        $details['regularized_vouchers'][] = $voucher;
                    } catch (\Exception $e) {
                        $processed['failed']++;
                        $details['failed_vouchers'][] = [
                            'voucher' => $voucher,
                            'error' => $e->getMessage(),
                        ];
                    }

                    $processed['total']++;
                }
            });

        return [
            'processed' => $processed,
            'details' => $details,
        ];
    }
}
