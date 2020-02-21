<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InitializationController as ServiceAuth;

class GetEspbiPractitioner extends Model
{
    public static function GetEspbiPractitionerResourceId($practitionerId){
        $generationParameter = 'getResource';
        $client = ServiceAuth::TakeAuthorization($generationParameter);
        try {
            $responsePractitioner = $client->get(env('TESTMAINFULLINFORMATION') . env('TESTPRACTITIONERFULLINFORMATION') . $practitionerId, ['auth' => 'oauth']);
            $contentsPractitioner = $responsePractitioner->getBody()->getContents();
            $fullPractitionerInformation = simplexml_load_string($contentsPractitioner);
            $fullPractitionerEntryInformation = $fullPractitionerInformation->entry;
            return $fullPractitionerEntryInformation->link['href'];
        }
        catch (Guzzle\Http\Exception\BadResponseException $e) {
            echo 'Problems: ' . $e->getMessage();
        }
    }
}
