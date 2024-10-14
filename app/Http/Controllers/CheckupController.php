<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckupController extends Controller
{


    public function data_pasien_by_user()
    {
        try {
            $data = DB::table('clinic_pasien')
                ->leftJoin('jenis_kelamin', 'clinic_pasien.jk_id', '=', 'jenis_kelamin.id')
                ->leftJoin('users', 'clinic_pasien.register_id', '=', 'users.id')
                ->select('nama_pasien', 'clinic_pasien.alamat as alamat pasien', 'tanggal_lahir', 'jenis_kelamin.desc as jenis_kelamin', 'users.name')
                ->where('register_id', Auth::user()->id)
                ->get();

            return ResponseFormatter::success($data, 'Get data pasien');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }

    public function data_antrian()
    {
        try {
            $data = DB::table('clinic_dokter_clinic')
                ->leftJoin('clinic_dokter', 'clinic_dokter_clinic.dokter_id', 'clinic_dokter.id')
                ->select('clinic_dokter.nama_dokter as jj')
                ->where('clinic_dokter_clinic.klinik_id', request('klinik_id'))
                ->get();




            return ResponseFormatter::success(
                $data,
                "Antrian kamu"
            );
        } catch (\Throwable $error) {
            return ResponseFormatter::error(
                $error,
                $error
            );
        }
    }
    public function check_no_antrian()
    {
        try {

            $dateNow = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $antrian = DB::table('clinic_checkup')
                ->whereDate('clinic_checkup.tanggal_checkup', $dateNow)
                ->where('clinic_checkup.klinik_id', request('klinik_id'))
                ->where('clinic_checkup.dokter_id', request('dokter_id'))
                ->count();

            $data = [
                'antian' => $antrian
            ];
            return ResponseFormatter::success(
                $data,
                "Antrian kamu"
            );
        } catch (\Throwable $error) {
            return ResponseFormatter::error(
                $error,
                $error
            );
        }
    }


    public function save_check_up()
    {
        try {
            DB::table('clinic_checkup')->insert([
                'klinik_id' => request('klinik_id'),
                'pasien_id' => request('pasien_id'),
                'tanggal_checkup' => request('tanggal_checkup'),
                'waktu_checkup_id' => request('waktu_checkup_id'),
            ]);
            return ResponseFormatter::success(
                null,
                "Data berhasil disimpan"
            );
        } catch (\Throwable $error) {
            return ResponseFormatter::error(
                $error,
                $error
            );
        }
    }
}
