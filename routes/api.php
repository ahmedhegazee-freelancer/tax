<?php

use App\Imports\InvoiceItemsImport;
use App\Imports\InvoicesImport;
use App\Models\Invoice;
use App\Services\InvoiceFormatter;
use App\Services\InvoiceService;
use App\Services\SerializeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('import-csv', function () {
    Excel::queueImport(new InvoiceItemsImport, request()->file('csv'));
});

Route::post('flatten-json', function (Request $request) {

    $arr = json_decode(file_get_contents(public_path('storage/' . $request->json->store('json', ['disk' => 'public']))), true);
    // $serliazedData = '"ISSUER""ADDRESS""BRANCHID""1""COUNTRY""EG""GOVERNATE""Cairo""REGIONCITY""Nasr City""STREET""580 Clementina Key""BUILDINGNUMBER""Bldg. 0""POSTALCODE""68030""FLOOR""1""ROOM""123""LANDMARK""7660 Melody Trail""ADDITIONALINFORMATION""beside Townhall""TYPE""B""ID""113317713""NAME""Issuer Company""RECEIVER""ADDRESS""COUNTRY""EG""GOVERNATE""Egypt""REGIONCITY""Mufazat al Ismlyah""STREET""580 Clementina Key""BUILDINGNUMBER""Bldg. 0""POSTALCODE""68030""FLOOR""1""ROOM""123""LANDMARK""7660 Melody Trail""ADDITIONALINFORMATION""beside Townhall""TYPE""B""ID""313717919""NAME""Receiver""DOCUMENTTYPE""I""DOCUMENTTYPEVERSION""0.9""DATETIMEISSUED""2020-10-27T23:59:59Z""TAXPAYERACTIVITYCODE""4620""INTERNALID""IID1""PURCHASEORDERREFERENCE""P-233-A6375""PURCHASEORDERDESCRIPTION""purchase Order description""SALESORDERREFERENCE""1231""SALESORDERDESCRIPTION""Sales Order description""PROFORMAINVOICENUMBER""SomeValue""PAYMENT""BANKNAME""SomeValue""BANKADDRESS""SomeValue""BANKACCOUNTNO""SomeValue""BANKACCOUNTIBAN""""SWIFTCODE""""TERMS""SomeValue""DELIVERY""APPROACH""SomeValue""PACKAGING""SomeValue""DATEVALIDITY""2020-09-28T09:30:10Z""EXPORTPORT""SomeValue""COUNTRYOFORIGIN""EG""GROSSWEIGHT""10.50""NETWEIGHT""20.50""TERMS""SomeValue""INVOICELINES""INVOICELINES""DESCRIPTION""Computer1""ITEMTYPE""GPC""ITEMCODE""10001774""UNITTYPE""EA""QUANTITY""5""INTERNALCODE""IC0""SALESTOTAL""947.00""TOTAL""2969.89""VALUEDIFFERENCE""7.00""TOTALTAXABLEFEES""817.42""NETTOTAL""880.71""ITEMSDISCOUNT""5.00""UNITVALUE""CURRENCYSOLD""EUR""AMOUNTEGP""189.40""AMOUNTSOLD""10.00""CURRENCYEXCHANGERATE""18.94""DISCOUNT""RATE""7""AMOUNT""66.29""TAXABLEITEMS""TAXABLEITEMS""TAXTYPE""T1""AMOUNT""272.07""SUBTYPE""T1""RATE""14.00""TAXABLEITEMS""TAXTYPE""T2""AMOUNT""208.22""SUBTYPE""T2""RATE""12""TAXABLEITEMS""TAXTYPE""T3""AMOUNT""30.00""SUBTYPE""T3""RATE""0.00""TAXABLEITEMS""TAXTYPE""T4""AMOUNT""43.79""SUBTYPE""T4""RATE""5.00""TAXABLEITEMS""TAXTYPE""T5""AMOUNT""123.30""SUBTYPE""T5""RATE""14.00""TAXABLEITEMS""TAXTYPE""T6""AMOUNT""60.00""SUBTYPE""T6""RATE""0.00""TAXABLEITEMS""TAXTYPE""T7""AMOUNT""88.07""SUBTYPE""T7""RATE""10.00""TAXABLEITEMS""TAXTYPE""T8""AMOUNT""123.30""SUBTYPE""T8""RATE""14.00""TAXABLEITEMS""TAXTYPE""T9""AMOUNT""105.69""SUBTYPE""T9""RATE""12.00""TAXABLEITEMS""TAXTYPE""T10""AMOUNT""88.07""SUBTYPE""T10""RATE""10.00""TAXABLEITEMS""TAXTYPE""T11""AMOUNT""123.30""SUBTYPE""T11""RATE""14.00""TAXABLEITEMS""TAXTYPE""T12""AMOUNT""105.69""SUBTYPE""T12""RATE""12.00""TAXABLEITEMS""TAXTYPE""T13""AMOUNT""88.07""SUBTYPE""T13""RATE""10.00""TAXABLEITEMS""TAXTYPE""T14""AMOUNT""123.30""SUBTYPE""T14""RATE""14.00""TAXABLEITEMS""TAXTYPE""T15""AMOUNT""105.69""SUBTYPE""T15""RATE""12.00""TAXABLEITEMS""TAXTYPE""T16""AMOUNT""88.07""SUBTYPE""T16""RATE""10.00""TAXABLEITEMS""TAXTYPE""T17""AMOUNT""88.07""SUBTYPE""T17""RATE""10.00""TAXABLEITEMS""TAXTYPE""T18""AMOUNT""123.30""SUBTYPE""T18""RATE""14.00""TAXABLEITEMS""TAXTYPE""T19""AMOUNT""105.69""SUBTYPE""T19""RATE""12.00""TAXABLEITEMS""TAXTYPE""T20""AMOUNT""88.07""SUBTYPE""T20""RATE""10.00""INVOICELINES""DESCRIPTION""Computer2""ITEMTYPE""GPC""ITEMCODE""10003752""UNITTYPE""EA""QUANTITY""7""INTERNALCODE""IC0""SALESTOTAL""662.90""TOTAL""2226.61""VALUEDIFFERENCE""6.00""TOTALTAXABLEFEES""621.51""NETTOTAL""652.90""ITEMSDISCOUNT""9.00""UNITVALUE""CURRENCYSOLD""EUR""AMOUNTEGP""94.70""AMOUNTSOLD""5.00""CURRENCYEXCHANGERATE""18.94""DISCOUNT""RATE""0""AMOUNT""10.00""TAXABLEITEMS""TAXABLEITEMS""TAXTYPE""T1""AMOUNT""205.47""SUBTYPE""T1""RATE""14.00""TAXABLEITEMS""TAXTYPE""T2""AMOUNT""157.25""SUBTYPE""T2""RATE""12""TAXABLEITEMS""TAXTYPE""T3""AMOUNT""30.00""SUBTYPE""T3""RATE""0.00""TAXABLEITEMS""TAXTYPE""T4""AMOUNT""32.20""SUBTYPE""T4""RATE""5.00""TAXABLEITEMS""TAXTYPE""T5""AMOUNT""91.41""SUBTYPE""T5""RATE""14.00""TAXABLEITEMS""TAXTYPE""T6""AMOUNT""60.00""SUBTYPE""T6""RATE""0.00""TAXABLEITEMS""TAXTYPE""T7""AMOUNT""65.29""SUBTYPE""T7""RATE""10.00""TAXABLEITEMS""TAXTYPE""T8""AMOUNT""91.41""SUBTYPE""T8""RATE""14.00""TAXABLEITEMS""TAXTYPE""T9""AMOUNT""78.35""SUBTYPE""T9""RATE""12.00""TAXABLEITEMS""TAXTYPE""T10""AMOUNT""65.29""SUBTYPE""T10""RATE""10.00""TAXABLEITEMS""TAXTYPE""T11""AMOUNT""91.41""SUBTYPE""T11""RATE""14.00""TAXABLEITEMS""TAXTYPE""T12""AMOUNT""78.35""SUBTYPE""T12""RATE""12.00""TAXABLEITEMS""TAXTYPE""T13""AMOUNT""65.29""SUBTYPE""T13""RATE""10.00""TAXABLEITEMS""TAXTYPE""T14""AMOUNT""91.41""SUBTYPE""T14""RATE""14.00""TAXABLEITEMS""TAXTYPE""T15""AMOUNT""78.35""SUBTYPE""T15""RATE""12.00""TAXABLEITEMS""TAXTYPE""T16""AMOUNT""65.29""SUBTYPE""T16""RATE""10.00""TAXABLEITEMS""TAXTYPE""T17""AMOUNT""65.29""SUBTYPE""T17""RATE""10.00""TAXABLEITEMS""TAXTYPE""T18""AMOUNT""91.41""SUBTYPE""T18""RATE""14.00""TAXABLEITEMS""TAXTYPE""T19""AMOUNT""78.35""SUBTYPE""T19""RATE""12.00""TAXABLEITEMS""TAXTYPE""T20""AMOUNT""65.29""SUBTYPE""T20""RATE""10.00""TOTALDISCOUNTAMOUNT""76.29""TOTALSALESAMOUNT""1609.90""NETAMOUNT""1533.61""TAXTOTALS""TAXTOTALS""TAXTYPE""T1""AMOUNT""477.54""TAXTOTALS""TAXTYPE""T2""AMOUNT""365.47""TAXTOTALS""TAXTYPE""T3""AMOUNT""60.00""TAXTOTALS""TAXTYPE""T4""AMOUNT""75.99""TAXTOTALS""TAXTYPE""T5""AMOUNT""214.71""TAXTOTALS""TAXTYPE""T6""AMOUNT""120.00""TAXTOTALS""TAXTYPE""T7""AMOUNT""153.36""TAXTOTALS""TAXTYPE""T8""AMOUNT""214.71""TAXTOTALS""TAXTYPE""T9""AMOUNT""184.04""TAXTOTALS""TAXTYPE""T10""AMOUNT""153.36""TAXTOTALS""TAXTYPE""T11""AMOUNT""214.71""TAXTOTALS""TAXTYPE""T12""AMOUNT""184.04""TAXTOTALS""TAXTYPE""T13""AMOUNT""153.36""TAXTOTALS""TAXTYPE""T14""AMOUNT""214.71""TAXTOTALS""TAXTYPE""T15""AMOUNT""184.04""TAXTOTALS""TAXTYPE""T16""AMOUNT""153.36""TAXTOTALS""TAXTYPE""T17""AMOUNT""153.36""TAXTOTALS""TAXTYPE""T18""AMOUNT""214.71""TAXTOTALS""TAXTYPE""T19""AMOUNT""184.04""TAXTOTALS""TAXTYPE""T20""AMOUNT""153.36""TOTALAMOUNT""5191.50""EXTRADISCOUNTAMOUNT""5.00""TOTALITEMSDISCOUNTAMOUNT""14.00"';
    $str = InvoiceFormatter::flattenJson($arr);
    // foreach ($arr as $key => $value) {
    //     $str .= "\"" . Str::upper($key) . "\"";
    //     if (is_array($value)) {
    //         foreach ($value as $k => $v) {
    //             $str .= "\"" . Str::upper($k) . "\"";
    //             if (is_array($v)) {
    //                 foreach ($v as $ky => $val) {
    //                     $str .= "\"" . Str::upper($ky) . "\"";
    //                     if (!empty($val)) {
    //                         $str .= "\"" . $val . "\"";
    //                     }
    //                 }
    //             } else {
    //                 if (!empty($v)) {
    //                     $str .= "\"" . $v . "\"";
    //                 }
    //             }
    //         }
    //     } else {
    //         if (!empty($value)) {
    //             $str .= "\"" . $value . "\"";
    //         }
    //     }
    // }
    // dd($str);
    return Str::upper(hash('sha256', $str));
    // dd(SerializeService::hashedSerializedData($arr));
    // dd(SerializeService::serialize($arr));
});


Route::post('upload-files', function (Request $request) {
    $request->validate([
        'tickets' => 'required|file',
        'items' => 'required|file',
    ]);
    DB::table('invoices')->truncate();
    // DB::table('invoice_items')->truncate();
    if ($request->file('tickets')) {
        Excel::queueImport(new InvoicesImport, $request->file('tickets'))->allOnQueue('invoices');
    }

    // if ($request->file('items')) {
    //     Excel::queueImport(new InvoiceItemsImport, $request->file('items'))->allOnQueue('invoices');
    // }
});

Route::post('send-invoice', function (Request $request) {
    abort_if(Invoice::count() == 0, 400, 'No Invoices Found');
    $idUrl = "";
    $apiUrl = "";
    if ($request->get('pre')) {
        $idUrl = "https://id.preprod.eta.gov.eg/connect/token";
        $apiUrl = "https://api.preprod.invoicing.eta.gov.eg/api/v1/receiptsubmissions";
    } else {
        $idUrl = "https://id.eta.gov.eg/connect/token";
        $apiUrl = "https://api.invoicing.eta.gov.eg/api/v1/receiptsubmissions";
    }
    $json = Http::withHeaders([
        "pososversion" => $request->get('pososversion'),
        "posserial" => $request->get('posserial'),
        'presharedkey' => '',
    ])->asForm()->post($idUrl, [
        "client_id" => $request->get('client_id'),
        "client_secret" => $request->get('client_secret'),
        'grant_type' => 'client_credentials',
    ])->json();
    if (isset($json['access_token']))
        $token = $json['access_token'];
    else {
        abort(400, 'Invalid Credentials');
    }

    $invoices = Invoice::select(['id'])
        ->whereDate('closing_date', $request->get('date'))
        ->get();

    if ($invoices->count() == 0)
        abort(400, 'No Invoices Found');
    $json = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->withBody(json_encode([
        'receipts' => InvoiceService::make()
            ->formatInvoices(
                $invoices->pluck('id')
                    ->toArray(),
                $request->get('posserial')
            ),
        // 'receipts' => InvoiceService::make()->formatInvoices([4]),
    ], JSON_PRESERVE_ZERO_FRACTION), 'application/json')
        ->post($apiUrl);
    dd($json->json());
});