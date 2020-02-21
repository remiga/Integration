<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GetEspbiInformation;

class DocumentsController extends Controller
{
    public function index(Request $request)
    {
        return GetEspbiInformation::GetPatientDocument($request->id);
    }

}
