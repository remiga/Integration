<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Stream\Stream;

class InitializationController extends Controller
{
    public static function TakeAuthorization($generationParameter){
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key'    => env('CONSUMER_KEY'),
            'consumer_secret' => '',
            'private_key_file' => env('CONSUMER_KEY_PATH'),
            'signature_method' => Oauth1::SIGNATURE_METHOD_RSA,
            'private_key_passphrase' => '',
        ]);
        $stack->push($middleware);
        $requestor_id = env('MAIN_REQUESTOR_ID'),; /*Produkcines aplinkos requestor*/
        switch ($generationParameter) {
            case 'getResource':
                $general = ['requestor_id' => $requestor_id, 'update' => 'true', 'content-type' => 'application/json'];
                break;
            case 'getPdf':
                $general = ['requestor_id' => $requestor_id,   'Accept'=>'application/pdf'];
                break;
            case 'postResource':
                $general = ['requestor_id' => $requestor_id,   'content-type' => 'application/atom+xml', 'Accept' => 'application/json'];
                break;
        }
        $client = new Client([
            'handler' => $stack,
            'headers' => $general,
            'http_errors' => false
        ]);
        return $client;
    }



}
