<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use YooKassa\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $shop_id = 373653;
    public $api_key = 'test_xwRF_HoGldBLFraRJ42CCKd3f0ex-2JzT1iQtBCAqbA';
    public function test(Request $request)
    {
        $test = $request->input();
        $book = Book::find(1);
        return view('test', compact('book'));
    }

    public function orders(Request $request){
        $book = Book::find(1);
        return view('order', compact('book'));
    }
    public function order(Request $request)
    {
        $request = $request->input();
        $client = new Client();
        $client->setAuth("{$this->shop_id}", "{$this->api_key}");
        $resp = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $request['price'],
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://scope-crash-key-doug.trycloudflare.com/api/v1/yookassa/',
                ),
                'capture' => true,
                'description' => 'Заказ №1',
            ),
            uniqid('', true)
        );


        
        $conf = $resp->getConfirmation()->getConfirmationUrl();
        return redirect()->to($conf);
    }
}
