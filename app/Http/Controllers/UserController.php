<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserRegistered;
use Illuminate\Support\Facades\Http;
use App\Rules\ReCaptcha;

class UserController extends Controller
{
    /** 
     * Register a new user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Define the error messages
        $errorMessages = [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña no cuenta con el formato solicitado.',
            'g-recaptcha-response.required' => 'La verificación de reCAPTCHA es obligatoria.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[@$!%*#?&.]/'
            ],
            'g-recaptcha-response' => ['required', new ReCaptcha()]
        ], $errorMessages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $verificationCode = Str::random(6);

        // Create a new user with the request data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'code' => Hash::make($verificationCode)
        ]);

        // Send the registration code via email
        Mail::to($user->email)->send(new UserRegistered($user, $verificationCode));

        return response()->json([
            'status'=> 'success',
            'message'=> 'Se le ha enviado un correo electrónico con un código de verificación.',
            'redirect' => route('login')
        ]);
    }

    /** 
     * Log in a user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $errorMessages = [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'code.required' => 'El código de verificación es obligatorio.',
            'recaptcha_token.required' => 'La verificación de reCAPTCHA es obligatoria.'
        ];


        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'code' => 'required|string',
            'recaptcha_token' => ['required', new ReCaptcha()]
        ], $errorMessages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || !Hash::check($request->code, $user->code)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas, vuelva a intentarlo.'
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status'=> 'success',
            'token'=> $token,
            'redirect' => route('home')
        ], 200);
    }
}


