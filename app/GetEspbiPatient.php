<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InitializationController as ServiceAuth;

class GetEspbiPatient extends Model
{
    public static function GetEspbiPatientResourceId($patientId){
        $generationParameter = 'getResource';
        $client = ServiceAuth::TakeAuthorization($generationParameter);
        try {
            $responsePatient = $client->get(env('TESTMAINFULLINFORMATION') . env('BYIDENTIFIER') . $patientId, ['auth' => 'oauth']);
            $contentsPatient = $responsePatient->getBody()->getContents();
            $fullPatientInformation = simplexml_load_string($contentsPatient);
            $fullPatientEntryInformation = $fullPatientInformation->entry;
            return $fullPatientEntryInformation->link['href'];
        }
        catch (Guzzle\Http\Exception\BadResponseException $e) {
            echo 'Problems: ' . $e->getMessage();
        }
    }
}
