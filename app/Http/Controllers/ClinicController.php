<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClinicController extends Controller
{

    public function save_klinik(Request $request)
    {
        try {
            $nama_klinik = $request->nama_klinik;
            $alamat = $request->alamat;
            $no_hp = $request->no_hp;
            DB::table('clinic_klinik')->insert([
                'nama_klinik' => $nama_klinik,
                'alamat' => $alamat,
                'no_hp' => $no_hp,
                'user_id' => Auth::user()->id,
            ]);


            return ResponseFormatter::success(null, 'Data klinik berhasil di simpan');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }

    public function save_dokter_clinic(Request $request)
    {
        try {
            $jam_operasional_pagi = $request->jam_operasional_pagi;
            $jam_operasional_sore = $request->jam_operasional_sore;
            $biaya_bpjs = $request->biaya_bpjs;
            $biaya_non_bpjs = $request->biaya_non_bpjs;
            $dokter_id = $request->dokter_id;
            $libur_tambahan = $request->libur_tambahan;

            $libur = $request->libur_id;
            $klinikId = DB::table('clinic_klinik')
                ->select('id')
                ->where('clinic_klinik.user_id', Auth::user()->id)
                ->first();

            $operasional = DB::table('clinic_jam_operasional')->insertGetId([
                'pagi' => $jam_operasional_pagi,
                'sore' => $jam_operasional_sore,
            ]);

            $biaya = DB::table('clinic_biaya')->insertGetId([
                'biaya_bpjs' => $biaya_bpjs,
                'biaya_non_bpjs' => $biaya_non_bpjs,
            ]);
n
            foreach ($libur as $key => $l) {

                DB::table('clinic_dokter_clinic')->insert([
                    'libur_id' => $l,
                    'klinik_id' => $kliikId->id,
                    'dokter_id' => $dokter_id,
                    'libur_tambahan' => $libur_tambahan,
                    'jam_operasional_dokter_id' => $operasional,
                    'biaya_dokter_id' => $biaya,
                ]);
            }

            return ResponseFormatter::success(null, 'Data klinik berhasil di simpan');
        } catch (\Throwable $error) {
            return ResponseFormatter::error($error, $error);
        }
    }



    public function data_klinik(Request $request)
    {
        try {
            $klinik = DB::table('clinic_klinik')
                ->select('id', 'nama_klinik', 'alamat')
                ->get();

            $data = [
                'klinik' => $klinik
            ];

            return ResponseFormatter::success(
                $data,
                "Get data klinik"
            );
        } catch (\Throwable $error) {
            return ResponseFormatter::error(
                $error,
                $error
            );
        }
    }
    public function detail_klinik(Request $request)
    {
        try {

            $klinik_id = $request->klinik_id;

            $data = DB::table('clinic_dokter_clinic')
                ->leftJoin('clinic_dokter', 'clinic_dokter_clinic.dokter_id', 'clinic_dokter.id')
                ->leftJoin('clinic_master_katagori_dokter', 'clinic_dokter.kategori_id', 'clinic_master_katagori_dokter.id')
                ->leftJoin('clinic_biaya', 'clinic_dokter_clinic.biaya_dokter_id', 'clinic_biaya.id')
                ->leftJoin('clinic_jam_operasional', 'clinic_dokter_clinic.jam_operasional_dokter_id', 'clinic_jam_operasional.id')
                ->select(
                    'clinic_dokter_clinic.id',
                    'clinic_dokter.nama_dokter',
                    'clinic_master_katagori_dokter.desc as kategori',
                    'clinic_biaya.biaya_bpjs',
                    'clinic_biaya.biaya_non_bpjs',
                    'clinic_jam_operasional.pagi as jadwal pagi',
                    'clinic_jam_operasional.pagi as jadwal sore',
                    'libur_tambahan'
                )
                ->where('clinic_dokter_clinic.klinik_id', $klinik_id)
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
}
