<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InitializationController as ServiceAuth;
use Spatie\Url\Url;

class GetEspbiInformation extends Model
{

    public static function GetPatientOrders($patientId){
        $generationParameter = 'getResource';
        $client = ServiceAuth::TakeAuthorization($generationParameter);
            try {
                $response = $client->get(env('TESTPATIENTSENDINGS') . $patientId, ['auth' => 'oauth']);
                $contents = $response->getBody()->getContents();
                $fullOrderInformation = simplexml_load_string($contents);
                foreach ($fullOrderInformation->link as $linksResult) {
                    if (Url::fromString($linksResult['rel']) == 'first') {
                        $urlFirst = Url::fromString($linksResult['href'])->getQueryParameter('page');
                    }
                    if (Url::fromString($linksResult['rel']) == 'last') {
                        $urlLast = Url::fromString($linksResult['href'])->getQueryParameter('page');
                    }
                }
                $fullEntryInformation = $fullOrderInformation->entry;
                $responseArray = array();
                if ($fullOrderInformation->totalResults[0] != 0) {
                    foreach ($fullEntryInformation as $entrySimpleElement) {
                        $responseArray[(string)$entrySimpleElement->id]['SendingCreationDate'] = (string)$entrySimpleElement->content->Composition->date['value'];
                        $responseArray[(string)$entrySimpleElement->id]['SendingAuthor'] = self::GetPractitioner((string)$entrySimpleElement->content->Composition->author->reference['value'], $client);
                        $responseArray[(string)$entrySimpleElement->id]['SendingCustodian'] = self::GetOrganisation((string)$entrySimpleElement->content->Composition->custodian->reference['value'], $client);
                        $responseArray[(string)$entrySimpleElement->id]['SendingTarget'] = self::GetOrderTarget($entrySimpleElement->content->Composition, $client);
                        $responseArray[(string)$entrySimpleElement->id]['SendingLinkPdf'] = env('DOCUMENTLINK') . str_replace('Binary/', '', self::GetDocumentRefrenceBinary(str_replace('Composition/', '', (string)$entrySimpleElement->id), $client));
                        $responseArray[(string)$entrySimpleElement->id]['PatientFullInformation'] = self::GetPatientFull((string)$entrySimpleElement->content->Composition->subject->reference['value'], $client);
//                    $responseArray[(string)$entrySimpleElement->id]['SendingCreationDate'] = (string)$entrySimpleElement->content->DocumentReference->created['value'];
//                    $responseArray[(string)$entrySimpleElement->id]['SendingAuthor'] = self::GetPractitioner((string)$entrySimpleElement->content->DocumentReference->author->reference['value'], $client);
//                    $responseArray[(string)$entrySimpleElement->id]['SendingCustodian'] = self::GetOrganisation((string)$entrySimpleElement->content->DocumentReference->custodian->reference['value'], $client);
//                    $responseArray[(string)$entrySimpleElement->id]['SendingTarget'] = self::GetOrderTarget('Composition/'.(string)$entrySimpleElement->content->DocumentReference->masterIdentifier->value['value'], $client);
//                    $responseArray[(string)$entrySimpleElement->id]['SendingLinkPdf'] = env('DOCUMENTLINK').str_replace('Binary/','',(string)$entrySimpleElement->content->DocumentReference ->location['value']);

//                    $responseArray[(string)$entrySimpleElement->id]['OrderDate'] = (string)$entrySimpleElement->content->Order->date['value'];
//                    $responseArray[(string)$entrySimpleElement->id]['Order'] = (string)$entrySimpleElement->id;
//                    $responseArray[(string)$entrySimpleElement->id]['OrderHistory'] = (string)$entrySimpleElement->link['href'];
//                    $orderContent = $entrySimpleElement->content->Order;
//                    $responseArray[(string)$entrySimpleElement->id]['OrderPatient'] = (string)$orderContent->subject->reference['value'];
//                    $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctor'] = (string)$orderContent->source->reference['value'];
//                    $doctorAndOrganization = self::GetPractitioner( $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctor'], $client);
//                    $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctorSurname'] = (string)$doctorAndOrganization[0];
//                    $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctorName'] = (string)$doctorAndOrganization[1];
//                    //$Organization = self::Getorganization( (string)$doctorAndOrganization[2], $client);
//                    //$responseArray[(string)$entrySimpleElement->id]['OrderSourceCompany'] = (string)$Organization;
//                    $responseArray[(string)$entrySimpleElement->id]['OrderReasonResources'] = (string)$orderContent->reasonResource->reference['value'];
//                    $responseArray[(string)$entrySimpleElement->id]['OrderEncounter'] = (string)$orderContent->detail->reference['value'];
//                    foreach($orderContent->extension as $extensionDetail){
//                        if($extensionDetail->attributes()->url == 'http://esveikata.lt/Profile/ltnhr-order#type'){
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTypeSystem'] = (string)$extensionDetail->valueCodeableConcept->coding->system['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTypeCode'] = (string)$extensionDetail->valueCodeableConcept->coding->code['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTypeDisplay'] = (string)$extensionDetail->valueCodeableConcept->coding->display['value'];
//                            continue;
//                        }
//                        if($extensionDetail->attributes()->url == 'http://esveikata.lt/Profile/ltnhr-order#relatedDiagnosis'){
//                            $responseArray[(string)$entrySimpleElement->id]['OrderDiagnosisSystem'] = (string)$extensionDetail->valueCodeableConcept->coding->system['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderDiagnosisCode'] = (string)$extensionDetail->valueCodeableConcept->coding->code['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderDiagnosisDisplay'] = (string)$extensionDetail->valueCodeableConcept->coding->display['value'];
//                            continue;
//                        }
//                        if($extensionDetail->attributes()->url == 'http://esveikata.lt/Profile/ltnhr-order#targetQualification'){
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTargetDoctorSystem'] = (string)$extensionDetail->valueCodeableConcept->coding->system['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTargetDoctorCode'] = (string)$extensionDetail->valueCodeableConcept->coding->code['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderTargetDoctorDisplay'] = (string)$extensionDetail->valueCodeableConcept->coding->display['value'];;
//                            continue;
//                        }
//                        if($extensionDetail->attributes()->url == 'http://esveikata.lt/Profile/ltnhr-order#sourceQualification'){
//                            $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctorSystem'] = (string)$extensionDetail->valueCodeableConcept->coding->system['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctorCode'] = (string)$extensionDetail->valueCodeableConcept->coding->code['value'];
//                            $responseArray[(string)$entrySimpleElement->id]['OrderSourceDoctorDisplay'] = (string)$extensionDetail->valueCodeableConcept->coding->display['value'];;
//                            continue;
//                        }
//                    }


                    }
                }
                return response()->json($responseArray, 200, [], JSON_UNESCAPED_SLASHES);
            } catch (Guzzle\Http\Exception\BadResponseException $e) {
                echo 'Problems: ' . $e->getMessage();
            }
    }

    public static function GetPractitioner($linkToPractitioner, $client){

        $fullDoctorResponse = $client->get(env('TESTESPBIMAIN').$linkToPractitioner,['auth' => 'oauth']);
        $fullDoctorContents = $fullDoctorResponse->getBody()->getContents();
        $fullDoctorInformation =  simplexml_load_string ($fullDoctorContents);
        $doctorName = $fullDoctorInformation->name->given['value'];
        $doctorSurname = $fullDoctorInformation->name->family['value'];
//        $linkToCompany = $fullDoctorInformation->organization->reference['value'];
//        return [$doctorSurname, $doctorName];
        return [$doctorSurname, $doctorName];
    }

     public static function GetOrganisation($linkToOrganization, $client){
         $fullCustodianResponse = $client->get(env('TESTESPBIMAIN').$linkToOrganization,['auth' => 'oauth']);
         $fullCustodianContents = $fullCustodianResponse->getBody()->getContents();
         $fullCustodianInformation =  simplexml_load_string ($fullCustodianContents);
         $custodianName = $fullCustodianInformation->name['value'];
         return [$custodianName];
     }

     public static function GetOrderTarget($compositionInformation,$client){
//     public static function GetOrderTarget($compositionInformation, $client){
//         $fullCompositionResponse = $client->get(env('TESTESPBIMAIN').$compositionId,['auth' => 'oauth']);
         foreach($compositionInformation->section as $firstSectionDetail){
             if($firstSectionDetail->code->coding->system['value'] == 'http://loinc.org' and $firstSectionDetail->code->coding->code['value'] == '57133-1') {
                     foreach($firstSectionDetail->section as $secondSectionDetail) {
                         if($secondSectionDetail->code->coding->system['value'] == 'http://loinc.org' and $secondSectionDetail->code->coding->code['value'] == '57139-8') {
                             $referenceToOrder = $secondSectionDetail->content->reference['value'];
                             break;
                         }
                     }
                 break;
             }
         }
         $fullOrderResponse = $client->get(env('TESTESPBIMAIN').$referenceToOrder,['auth' => 'oauth']);
         $fullOrderContents = $fullOrderResponse->getBody()->getContents();
         $fullOrderInformation =  simplexml_load_string ($fullOrderContents);
         foreach($fullOrderInformation->extension as $extensionOrderDetail){
             if($extensionOrderDetail->attributes()->url == 'http://esveikata.lt/Profile/ltnhr-order#targetQualification'){
                 if(isset($extensionOrderDetail->valueCodeableConcept->text['value'])){
                     $orderTargetInformation = (string)$extensionOrderDetail->valueCodeableConcept->text['value'];
                 }
                 else{
                     $orderTargetInformation = (string)$extensionOrderDetail->valueCodeableConcept->coding->display['value'];
                 }
                 break;
             }
         }
         return [$orderTargetInformation];
     }

     public static function GetPatientFull($patientId, $client){
         $fullPatientInformationResponse = $client->get(env('TESTESPBIMAIN').$patientId,['auth' => 'oauth']);
         $fullPatientInformationContents = $fullPatientInformationResponse->getBody()->getContents();
         $fullPatientFullInformation =  simplexml_load_string ($fullPatientInformationContents);
         $fullName = $fullPatientFullInformation->name->text['value'];
         $birthDate = $fullPatientFullInformation->birthDate['value'];
         return [$birthDate, $fullName];
     }

        //Get Document Reference Binary code
    public static function GetDocumentRefrenceBinary($compositionId, $client){
        $fullDocumentReferenceResponse = $client->get(env('TESTDOCUMENTREFERENCE').$compositionId,['auth' => 'oauth']);
        $fullDocumentReferenceContents = $fullDocumentReferenceResponse->getBody()->getContents();
        $fullDocumentReferenceInformation =  simplexml_load_string ($fullDocumentReferenceContents);
        return (string)$fullDocumentReferenceInformation->entry->content->DocumentReference->location['value'];
    }

        //Get PDF document about Sending
    public static function GetPatientDocument($documentId){
        $filename = 'Sending_'.$documentId.'.pdf';
        $generationParameter = 'getResource';
        $client = ServiceAuth::TakeAuthorization($generationParameter);
        $response = $client->get(env('TESTESPBIDOCUMENTLINK').$documentId,['auth' => 'oauth']);
        $contents = $response->getBody()->getContents();
        return response()->make($contents, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

}
