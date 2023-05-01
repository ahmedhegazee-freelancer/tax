<?php


namespace App\Services;

use App\Models\InvoiceItem;

class InvoiceItemFormatter
{
    private static $instance;
    private array $businessSettings;
    private function __construct()
    {
    }
    public static function make(): self
    {
        if (is_null(static::$instance))
            static::$instance = new self();
        return static::$instance;
    }
    public function handle($items, $businessSettings)
    {
        $this->businessSettings = $businessSettings;
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = $this->format($item);
        }
        return $formattedItems;
    }
    public function format(InvoiceItem $item)
    {
        $itemFormatted
            = [
                'internalCode' => $item->item_id, //item id
                'description' => $item->name, //description
                'itemType' => 'EGS',
                'itemCode' => $item->item_id, //item id
                'unitType' => 'EA',
                'quantity' => round($item->quantity, 1),
                'unitPrice' =>
                round($item->price, 1), //price
                'netSale' => round($item->sub_total, 1),
                'totalSale' =>  round($item->sub_total, 1), //subtotal
                // 'total' => number_format($item->total, //total price
                'total' => round($item->sub_total, 1), //total price

            ];
        // if ($item->discount)
        //     $itemFormatted['commercialDiscountData'] = [
        //         $this->formatDiscount($item->discount)
        //     ];
        // dd($itemFormatted);
        return $itemFormatted;
    }
    public function formatDiscount($amount)
    {
        return [
            'description' => 'Discount',
            'amount' => number_format($amount, 1, '.', '')
        ];
    }
    public function formatTax($amount)
    {
        return [
            'taxType' => $this->businessSettings['taxType'],
            'amount' => number_format($amount, 1, '.', ''),
            'subType' => $this->businessSettings['subTypeTax']
        ];
    }
}
