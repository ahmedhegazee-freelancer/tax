<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'closing_date',
        'ticket_id',
        'sub_total',
        'total',
        'discount',
        'tax',
        'fees',
        'terminal_id',
        'paid',
        'voided',
        'deleted',
    ];
    protected $casts = [
        'sub_total' => 'double',
        'total' => 'double',
        'discount' => 'double',
        'tax' => 'double',
        'fees' => 'double',
    ];
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'ticket_id', 'ticket_id')->select([
            'item_id',
            'ticket_id',
            'name',
            'quantity',
            'price',
            'sub_total',
            'total',
            'discount',
        ]);
    }
    // /**
    //  * Define the json format of the model
    //  */
    // public function jsonSerialize(): array
    // {
    //     return [
    //         'closing_date'  => $this->closing_date,
    //         'ticket_id'     => $this->ticket_id,
    //         'sub_total'     => $this->sub_total,
    //         'total'         => $this->total,
    //         'discount'      => $this->discount,
    //         'tax'           => $this->tax,
    //         'fees'          => $this->fees,
    //         'terminal_id'   => $this->terminal_id,
    //         'paid'          => $this->paid,
    //         'voided'        => $this->voided,
    //         'deleted'       => $this->deleted,
    //     ];
    // } // jsonSerialize
}