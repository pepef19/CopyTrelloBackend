<?php


namespace App\Http\Controllers;

use App\Mail\NewEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $userValidator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users_trello,email'],
            'password' => ['required', 'max:100'],
        ]);

        if (!$userValidator->fails()) {
            $errors = $userValidator->errors()->getMessages();
            $code = Response::HTTP_NOT_ACCEPTABLE; //422
            return response()->json(['error' => $errors, 'code' => $code], $code);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return response()->json($user);

    }

    public function sendEmail(Request $request)
    {

        $data = $request->all();

        Mail::to($data['email'])->send(new NewEmail($data['name']));

        return response()->json($data);

    }

    public function findAll()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function findById($id)
    {
        $user = User::where('id', $id)->first();

        return response()->json($user);
    }

    //pendiente revisar - pepe 17 julio //gestionarlo desde frontend cargandome localstorage
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

}


