<?php

namespace App\Http\Controllers;

use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //
    public function form(){
        return view('inform/form');
    }

    public function import(Request $request){

        $file = $request->file('file');
        Excel::import(new DataImport, $file);

        return view('inform/form');
    }
}
