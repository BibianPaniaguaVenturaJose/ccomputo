<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Docentes;

class LoginController extends Controller
{
    public function show(){
        if(Auth::check()){
            return redirect('/home');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request){
        $credentials = $request->getCredentials();

        // Buscar el usuario en la tabla Docentes
        $user = Docentes::where('clave', $credentials['clave'])->first();

        if (!$user) {
            return redirect()->to('/login')->withErrors(['clave' => 'Las credenciales no coinciden.']);
        }

        // Loguear al usuario
        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    public function authenticated(Request $request, $user){
        return redirect('/home');
    }
}
