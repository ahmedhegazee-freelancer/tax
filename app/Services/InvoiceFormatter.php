<?php

namespace App\Services;

use App\Models\BranchAddressSetting;
use App\Models\BusinessSetting;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InvoiceFormatter
{
    private  static  $instance;
    private array $businessSettings = [];
    private array $branchAddress = [];
    private function __construct()
    {
    }
    public static function make()
    {
        if (!isset(static::$instance)) {
            static::$instance = new InvoiceFormatter();
        }

        return static::$instance->initialize();
    }
    public function initialize()
    {
        Cache::forget('business_settings');
        Cache::forget('branch_address');
        $this->businessSettings = [];
        $this->branchAddress = [];
        if (!count($this->businessSettings)) {
            $this->businessSettings = BusinessSettingsService::make()->get();
        }
        if (!count($this->branchAddress)) {
            $this->branchAddress = BranchAddressService::make()->get();
        }

        return $this;
    }

    public function format(Invoice $invoice, string $previous = "")
    {
        return
            [
                'header' => $this->formatHeader($invoice->closing_date, $invoice->ticket_id, $previous),
                'documentType' => $this->formatDocument(),
                'seller' => $this->formatSeller($this->businessSettings['deviceSerialNumber']),
                'buyer' => [
                    'type' => 'P'
                ],
                'itemData' => InvoiceItemFormatter::make()->handle($invoice->items, $this->businessSettings),
                // 'totalSales' => number_format($invoice->sub_total, 2, '.', ''), //subtotal
                'totalSales' => round($invoice->sub_total, 1), //subtotal
                'totalCommercialDiscount' => round(0, 1), //total discounts,
                // 'taxTotals' => $this->formatTax($invoice->tax),
                // 'taxTotals' => $this->formatTax($invoice->total * 0.14),
                'paymentMethod' => $this->businessSettings['paymentMethod'],
                // 'netAmount' => number_format($invoice->sub_total, 2, '.', ''), //subtotal
                'netAmount' => round($invoice->sub_total, 1), //subtotal
                // 'feesAmount' => number_format($invoice->fees, 2, '.', ''), //service charge,
                'feesAmount' => round(0, 1),
                // 'totalAmount' => number_format($invoice->total, 2, '.', '') //total price
                'totalAmount' => round($invoice->total, 1) //total price
            ];
    }
    public function formatSeller($terminalId)
    {
        return [
            'rin' => $this->businessSettings['rin'],
            'companyTradeName' => $this->businessSettings['companyTradeName'],
            'branchCode' => $this->businessSettings['branchCode'],
            'branchAddress' => $this->formatBranchAddress(),
            'deviceSerialNumber' => $terminalId, //Terminal ID
            'activityCode' => $this->businessSettings['activityCode']
        ];
    }
    public function formatBranchAddress()
    {
        return $this->branchAddress;
    }
    public function formatTax($taxAmount)
    {
        //get business settings taxType
        return [
            'taxType' => $this->businessSettings['taxType'],
            'amount' => $taxAmount //total tax
        ];
    }
    public function formatDocument()
    {
        return [
            'receiptType' => $this->businessSettings['receiptType'],
            'typeVersion' => $this->businessSettings['typeVersion']
        ];
    }
    public function formatHeader(string $date, string $receiptNumber, string $previous = "")
    {

        return [
            'dateTimeIssued'    => Carbon::parse($date)->toIso8601ZuluString(), //closing Date
            'receiptNumber'     => $receiptNumber,
            'uuid'              => '',
            // 'previousUUID'      => !empty($previous) ? $this->generateUUID($previous) : "",
            'previousUUID'      =>  "",
            'currency'          => 'EGP',
            "exchangeRate"      => 0.0,
            // 'orderdeliveryMode' => $this->businessSettings['Order Delivery Mode'],
        ];
    }
    public function generateUUID(string $id)
    {
        // return Str::uuid();
        // return hash('sha256', $id);
        return hash('sha256', Str::uuid());
    }
    public static function flattenJson(array $data)
    {

        $str = "";
        foreach ($data as $key => $value) {
            $str .= "\"" . Str::upper($key) . "\"";
            if (is_array($value)) {

                if (is_array($value[\array_key_first($value)]) && is_array($value[\array_key_last($value)]))
                    $str .= static::flattenArray($key, $value);
                else
                    $str .= static::flattenJson($value);
            } else {
                if (gettype($value) == 'double') {
                    $str .= '"' . number_format($value, 1, '.', '') . '"';
                } else
                    $str .= "\"" . $value . "\"";
            }
        }
        return $str;
    }
    public static function flattenArray(string $key, array $values)
    {
        $str = "";
        foreach ($values as $value) {
            $str .= "\"" . Str::upper($key) . "\"";
            if (is_array($value)) {
                $str .= static::flattenJson($value);
            } else {
                if (gettype($value) == 'double') {
                    $str .= '"' . number_format($value, 1, '.', '') . '"';
                } else
                    $str .= "\"" . $value . "\"";
            }
        }
        return $str;
    }
}
