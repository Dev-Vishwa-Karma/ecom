<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

public function apiLogin(Request $request)
{
    $credentials = $request->only('email','password');

    if (!$token = JWTAuth::attempt($credentials)) {

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ],401);
    }

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => auth()->user()
    ]);
}
public function apiRegister(Request $request)
{
    $request->validate([
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:6'
    ]);

    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
        'role'=>'customer'
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json([
        'token'=>$token,
        'user'=>$user
    ]);
}

public function showRegister()
{
    return view('auth.register');
}

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'address' => 'required|string|max:500',
        'mobile' => 'required|digits_between:10,15',
        'password' => 'required|min:6|confirmed',


        ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        'mobile' => $request->mobile,
        'password' => Hash::make($request->password),
        'role' => 'customer',
        

    ]);

    return redirect()->route('login')->with('success','Registration Successful');
}


public function redirectToProvider($provider)
{
    return Socialite::driver($provider)->redirect();
}

public function handleProviderCallback($provider)
{
    try {
        $socialUser = Socialite::driver($provider)->stateless()->user();
    } catch (\Exception $e) {
        return redirect()->route('login')->withErrors(['social' => 'Unable to login using ' . $provider]);
    }

    // Check if provider_id exists
    $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

    // If not, check by email
    if (!$user) {
        $user = User::where('email', $socialUser->getEmail())->first();
        if ($user) {
            // link social account to existing user
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }
    }

    // If still no user, create new
    if (!$user) {
        $user = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(16)), // random password
            'role' => 'customer',
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'email_verified_at' => now(),
        ]);
    }

    // Login user
    auth()->login($user, true);

    // Redirect based on role
    if ($user->role === 'super_admin') return redirect()->route('super.dashboard');
    if ($user->role === 'admin') return redirect()->route('admin.dashboard');
    return redirect()->route('customer.dashboard');
}

public function showLogin()
{
    return view('auth.login');
}

public function login(Request $request)
{
    $credentials = $request->only('email','password');

    if (auth()->attempt($credentials)) {

        $request->session()->regenerate();

        $user = auth()->user();

        if ($request->filled('fcm_token')) {
            FcmToken::where('token', $request->fcm_token)->delete();

            FcmToken::create([
                'user_id' => $user->id,
                'token' => $request->fcm_token,
                'device_type' => $request->device_type ?? 'web',
                'is_active' => true
            ]);
        }
        if ($user->role === 'super_admin') {
            return redirect()->route('super.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials',
    ]);
}
public function logout(Request $request)
{
       if ($request->filled('fcm_token')) {
        FcmToken::where('token', $request->fcm_token)->delete();
       }
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
}

