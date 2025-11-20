<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    // Register
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // contoh: beri nama token sesuai context
            $plainToken = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Register berhasil',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $plainToken,
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ERROR APP : " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal register',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Login
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            // bisa juga gunakan event log atau increment throttle counters
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // buat token yang bisa kita labeli
        $plainToken = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'data' => [
                'user' => new UserResource($user),
                'token' => $plainToken,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200);
        }

        return response()->json([
            'message' => 'Token tidak ditemukan'
        ], 400);
    }
}
