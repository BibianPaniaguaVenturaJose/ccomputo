<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Software;
use Illuminate\Http\Request;

class softwareController extends Controller
{
    public function index(){

        $software = Software::All();

        return response()->json($software);
    }

    public function showSoftware(){
        $softwareList = Software::all();
        return view('home.index', ['softwareList' => $softwareList]);
    }
}
