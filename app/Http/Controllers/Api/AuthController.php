<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  // Function Register
  public function register(Request $req)
  {
    // Validate Input
    $validator = Validator::make($req->all(), [
      'username' => 'required|string|max:255',
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8'
    ]);

    // If Validation Failed
    if($validator->fails()){
      return response()->json($validator->errors());
    }

    // Create user if validate
    $user = User::create([
      'username' => $req->username, 
      'name' => $req->name,
      'email' => $req->email,
      'password' => Hash::make($req->password) 
    ]);

    // Create Token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Response
    return response()->json([
      'data' => $user,
      'token' => $token,
      'token_type' => 'Bearer'
    ], Response::HTTP_CREATED);
  }

  // Function Login
  public function login(Request $req)
  {
    // Validate Input
    $this->validate($req, [
      'username' => 'required',
      'password' => 'required'
    ]);

    // Filter Input is username or email
    $username = filter_var($req->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // If Login Failed
    if(!Auth::attempt(array($username => $req->username, 'password' => $req->password))){
      return response()->json([
        'message' => 'Username or Password is incorrect!'
      ], Response::HTTP_UNAUTHORIZED);
    }

    // Check user
    $user = User::where($username, $req->username)->firstOrFail();

    // Create Token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Response
    return response()->json([
      'message' => 'Login Success',
      'token' => $token,
      'token_type' => 'Bearer'
    ], Response::HTTP_OK);
  }

  // Function Logout
  public function logout()
  {
    // Revoke Token
    auth()->user()->tokens()->delete();
    
    // Response
    return response()->json([
      'message' => 'Logout Success'
    ], Response::HTTP_OK);
  }
}
