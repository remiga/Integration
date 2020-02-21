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
            //'private_key_file' => '/var/www/restapi/storage/keys_espbi/private_kul_test.pem',
            'private_key_file' => '/var/www/restapi/storage/keys_espbi/private_kul_prod.pem',//kul ranktas
            'signature_method' => Oauth1::SIGNATURE_METHOD_RSA,
            'private_key_passphrase' => '',
        ]);
        $stack->push($middleware);
        //$requestor_id = '1000144237';/*Testines aplinkos requestor*/
        //$requestor_id = '1002442536';/*Testines aplinkos requestor*/

        $requestor_id = '1000112859'; /*Produkcines aplinkos requestor*/
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
