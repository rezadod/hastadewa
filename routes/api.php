<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckupController;
use App\Http\Controllers\ClinicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);


    // ! GET DATA JK & CATEGORY DOKTER
    Route::get('form_dokter', [AuthController::class, 'form_dokter']);
    // Route::get('form_klinik', [AuthController::class, 'form_klinik']);
});


Route::group(['middleware' => 'auth'], function () {
    // ! DOKTER
    Route::post('save_dokter', [AuthController::class, 'save_dokter']);
    // ! SAVE KLINIK
    Route::post('save_klinik', [AuthController::class, 'save_klinik']);
    // ! SAVE JADWAL DOKTER PER KLINIK (LIBUR)
    Route::post('save_dokter_clinic', [ClinicController::class, 'save_dokter_clinic']);
    // ! REGISTRASI PASIEN
    Route::post('save_data_pasien', [AuthController::class, 'save_data_pasien']);
    // TODO BERANDA PAGE
    Route::get('data_pasien_by_user', [CheckupController::class, 'data_pasien_by_user']);

    // TODO DOKTER PAGE
    Route::get('data_klinik', [ClinicController::class, 'data_klinik']);
    Route::post('detail_klinik', [ClinicController::class, 'detail_klinik']);


    // ! CHECKUP
    Route::post('data_antrian', [CheckupController::class, 'data_antrian']);
    Route::post('check_no_antrian', [CheckupController::class, 'check_no_antrian']);
});
