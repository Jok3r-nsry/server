<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class EirController extends Controller
{
    // https://api.autoaddress.ie/2.0/autocomplete?key=61B217D4-E63C-441F-591A-32121A528E9D&address=D02ADADAODJ&addressProfileName=AnPostShop&vanityMode=true
    public function handle_autocomplete(Request $request){
        $response = Http::get('https://api.autoaddress.ie/2.0/findaddress', [
            'key' => '61B217D4-E63C-441F-591A-32121A528E9D',
            'address' => $request->address,
            'vanityMode' => "true",
            'addressProfileName' => "AnPostShop",
            'language' => "EN",
            'limit' => -1,
        ]);
        return response($response, 200);
    }

//https://api.autoaddress.ie/2.0/findaddress?key=61B217D4-E63C-441F-591A-32121A528E9D&address=azadad-%20Address%20Search&vanityMode=true&addressProfileName=AnPostShop&language=EN&limit=-1
    public function handle_findaddress(Request $request){
        $response = Http::get('https://api.autoaddress.ie/2.0/findaddress', [
            'key' => '61B217D4-E63C-441F-591A-32121A528E9D',
            'address' => $request->address,
            'vanityMode' => "true",
            'addressProfileName' => "AnPostShop",
            'language' => "EN",
            'limit' => -1,
        ]);
        return response($response, 200);
    }

    public function base_endpoint(Request $request){
        return response("", 200);
    }
}
