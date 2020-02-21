<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InitializationController as ServiceAuth;
use App\EspbiXmlEncounter as XmlEncounter;
use App\GetEspbiPatient as EspbiPatient;
use App\GetEspbiPractitioner as EspbiPractitioner;
use Keygen;
use Parser;
use App\CreatedEncounter as EncounterCreated;

class EspbiEncounter extends Model
{
    public static function IsEspbiEncounter($patientId)
    {
        $generationParameter = 'getResource';
        $statusEncounter = "in progress";
        $client = ServiceAuth::TakeAuthorization($generationParameter);
        try {
            $isExistEncounter = $client->get(env('TESTENCOUNTER') . env('ENCOUNTERSUBJECT') . $patientId . env('SERVICEPROVIDER') . env('STATUSPROGRESS') . $statusEncounter, ['auth' => 'oauth']);
            $isExistEncounterContents = $isExistEncounter->getBody()->getContents();
            $isExistEncounterCounter = simplexml_load_string($isExistEncounterContents);
            return $isExistEncounterCounter->totalResults > 0 ? true : false;
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            echo 'Problems: ' . $e->getMessage();
        }

    }

    public static function CreateEspbiEncounter($patientId, $practitionerId)
    {
        try {
            $generationParameter = 'postResource';
            $forXML['PATIENT'] = EspbiPatient::GetEspbiPatientResourceId($patientId);
            $forXML['PRACTITIONER'] = EspbiPractitioner::GetEspbiPractitionerResourceId($practitionerId);
            $forXML['KORTELES_NR'] = 'ASSOC-' . Keygen::numeric(10)->generate();
            $forXML['ATIDARYMO_DATA'] = '2000-01-01T00:00:00';
            $returnedXML = "";
            $returnedXML = XmlEncounter::createEncounterXml($forXML);
            $newxml = new \SimpleXMLElement($returnedXML);
            $lastCompositionXML = $newxml->asXML();
            $client = ServiceAuth::TakeAuthorization($generationParameter);
            $response = $client->post(env('TESTMAINFULLINFORMATION'), ['auth' => 'oauth', 'body' => $lastCompositionXML]);
            //dd($response->getBody()->getContents());
        if ($response->getStatusCode() == '200') {
            $parser = new Parser();
            $parsed = Parser::xml($response->getBody()->getContents());
//            print_r($parsed);
            foreach ($parsed['entry'] as $oneEntry) {
                if (explode("/", $oneEntry['id'], 2)[0] == 'Encounter') {
                    $EncounterShortLink = $oneEntry['id'];
                } else {
                    $ProvenanceShortLink = $oneEntry['id'];
                }
            }
            $createdEncounter = new EncounterCreated;
            $createdEncounter->encounter_link = $EncounterShortLink;
            $createdEncounter->provenance_link = $ProvenanceShortLink;
            $createdEncounter->status = 'started';
            $createdEncounter->save();
            return true;
        }
        }
        catch (Guzzle\Http\Exception\BadResponseException $e) {
            echo 'Uh oh! ' . $e->getMessage();
            echo 'HTTP request URL: ' . $e->getRequest()->getUrl() . "\n";
            echo 'HTTP request: ' . $e->getRequest() . "\n";
            echo 'HTTP response status: ' . $e->getResponse()->getStatusCode() . "\n";
            echo 'HTTP response: ' . $e->getResponse() . "\n";
        }
        }


}
