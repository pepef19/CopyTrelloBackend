<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller

{
    public function register(Request $request)
    {
        $user = null;
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        } catch (\Exception $e) {
            if($e instanceof QueryException) {
                return response()->json(["error" => "El email indicado ya pertenece a un usuario registrado."])->setStatusCode(500);
            } //gestionar el error en frontend
        }
        //auth es un guard proporcionado por Laravel que llamará al provider que hemos registrado antes.
        $token = auth()->login($user); // al llamar a la función login() generamos un nuevo token
        //este token se guarda en la cache de Laravel.
        return $this->respondWithToken($token);
    }
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }
    public function getAuthenticatedUser(/*Request $request*/)
    {
        return response()->json(auth()->user());
    }
    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    //hacemos logout en frontend
    /*public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'Successfully logged out']);
    }*/
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 2592000
        ]);
    }
}
