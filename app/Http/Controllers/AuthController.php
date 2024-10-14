<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'form_dokter', 'form_klinik']]);
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'no_hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'no_hp' => request('no_hp'),
        ]);

        if ($user) {
            return response()->json(['message' => 'Pendataran berhasil']);
        } else {
            return response()->json(['message' => 'Pendataran gagal']);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!($token = auth()->attempt($credentials))) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function form_dokter()
    {
        try {
            $jk = DB::table('clinic_master_jenis_kelamin')->select('id', 'desc')->get();
            $kategori = DB::table('clinic_master_katagori_dokter')->select('id', 'desc')->get();
            $data = [
                'jenis_kelamin' => $jk,
                'kategori' => $kategori,
            ];

            return ResponseFormatter::success($data, 'Get data jenis_kelamin');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }

    public function save_dokter(Request $request)
    {
        try {
            $nama_dokter = $request->nama_dokter;
            $jenis_kelamin = $request->jenis_kelamin;
            $user_id = Auth::user()->id;
            $kategori_id = $request->kategori_id;


            DB::table('clinic_dokter')->insert([

                'nama_dokter' => $nama_dokter,
                'jenis_kelamin' => $jenis_kelamin,
                'user_id' => $user_id,
                'kategori_id' => $kategori_id,
            ]);
            return ResponseFormatter::success(null, 'Data dokter berhasil di tambahkan');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }


    public function save_data_pasien(Request $request)
    {
        try {
            $nama_pasien = $request->nama_pasien;
            $tanggal_lahir = $request->tanggal_lahir;
            $alamat = $request->alamat;
            $jk = $request->jk;
            DB::table('clinic_pasien')->insert([
                'nama_pasien' => $nama_pasien,
                'tanggal_lahir' => $tanggal_lahir,
                'alamat' => $alamat,
                'register_id' => Auth::user()->id,
                'jk' => $jk,
            ]);
            return ResponseFormatter::success(null, 'Data berhasil ditambahakan');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }
}
