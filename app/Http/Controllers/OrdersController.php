<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GetEspbiInformation;
use App\EspbiEncounter;
use App\GetEspbiPractitioner;
use App\GetEspbiPatient;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        if(EspbiEncounter::IsEspbiEncounter($request->id)) {
            return GetEspbiInformation::GetPatientOrders($request->id);
        }
        else{
            if(EspbiEncounter::CreateEspbiEncounter($request->id, $practitionerId)){
                return GetEspbiInformation::GetPatientOrders($request->id);
            }
        }
    }

}
