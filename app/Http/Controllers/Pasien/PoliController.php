<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function get() {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with("dokter", "dokter.poli")->get();

        return view("pasien.daftar", [
            "user" => $user,
            "polis" => $polis,
            "jadwals" => $jadwal
        ]);
    }

   public function submit(Request $request)
{
    $request->validate([
        "id_jadwal" => "required|exists:jadwal_periksa,id",
        "keluhan" => "nullable|string",
    ]);

    // Ambil ID pasien dari user yang login
    $id_pasien = Auth::user()->id;

    // Hitung jumlah antrian pada jadwal yang sama
    $jumlahSudahDaftar = DaftarPoli::where("id_jadwal", $request->id_jadwal)->count();

    // Simpan ke database
    DaftarPoli::create([
        "id_pasien" => $id_pasien,
        "id_jadwal" => $request->id_jadwal,
        "keluhan" => $request->keluhan,
        "no_antrian" => $jumlahSudahDaftar + 1,
    ]);

    return redirect()->back()->with("message", "Berhasil mendaftar ke poli")->with("type", "success");
}
}
