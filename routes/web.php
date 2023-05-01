<?php

use App\Models\Invoice;
use App\Models\User;
use App\Services\DatabaseService;
use App\Services\HeaderFormatter;
use App\Services\InvoiceFormatter;
use App\Services\InvoiceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Str;

Route::get('/', function () {
    $formattedDate = Carbon::createFromFormat('d/m/Y H:i', '29/04/2023 14:04')->format('Y-m-d H:i:s');
    dd($formattedDate);
    return view('welcome');
});
Route::get('invoices', function () {

    return response()->json([
        'receipts' => InvoiceService::make()->formatInvoices([1]),
    ]);
    return [
        'receipts' => InvoiceService::make()->formatInvoices([1]),
        // 'signatures' => []
    ];
});
Route::get('/invoice', function () {
    return Invoice::with('items')->find(1);
});
// Route::get('invoices', function () {
//     dd('2022-01-21 13:52:44' > InvoiceService::make()->getLastInvoiceDate());
//     return;
// });