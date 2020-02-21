<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InitializationController as ServiceAuth;
use Spatie\Url\Url;
use App\Espbiorganization as espbiorg;

class GetEspbiOrganization extends Model
{
    public static function GetEspbiOrganization(){
        $client = ServiceAuth::TakeAuthorization();
        try{
            $activeSheet = 0;
            $fullOrganizationInformation = self::getNextSheet($activeSheet, 'ESPBIORGANIZATION', $client);

            foreach ($fullOrganizationInformation->link as $linksResult){
                if  (Url::fromString($linksResult['rel']) == 'first'){
                    $urlFirst =Url::fromString($linksResult['href'])->getQueryParameter('page');
                }
                if  (Url::fromString($linksResult['rel']) == 'last'){
                    $urlLast =Url::fromString($linksResult['href'])->getQueryParameter('page');
                }
            }
            $urlLast = 2;
            for($i = $urlFirst; $i <= $urlLast; $i++){
                if($i != 1) {
                    $fullOrganizationInformation = self::getNextSheet($i, 'ESPBIORGANIZATION', $client);
                    }
                foreach($fullOrganizationInformation->entry as $oneOrganization) {
                    if ($oneOrganization->content->Organization->active['value'] == "true") {
                        $oneTimeForOrganization = new espbiorg;
                        $oneTimeForOrganization->company_id = (string)$oneOrganization->id;
                        $oneTimeForOrganization->company_id = (string)$oneOrganization->content->Organization->active['value'];
                        $oneTimeForOrganization->company_name = (string)$oneOrganization->content->Organization->name['value'];
                        foreach ($oneOrganization->extension as $extensionManyDetail) {
                            $oneTimeForOrganization->legal_entity_number = (string)$oneOrganization->id;
                        }
                        $oneTimeForOrganization->save();
                    }
                }
            }
        }

        catch (Guzzle\Http\Exception\BadResponseException $e) {
            echo 'Problems: ' . $e->getMessage();
        }
    }

    public static function getNextSheet($activeSheet, $keyPoint, $client){
        if ($activeSheet == 0) {
            $response = $client->get(env($keyPoint), ['auth' => 'oauth']);
        }
        else{
            $response = $client->get(env($keyPoint).'?page='.$activeSheet, ['auth' => 'oauth']);
        }
        $contents = $response->getBody()->getContents();
        return simplexml_load_string ($contents);
    }
}
