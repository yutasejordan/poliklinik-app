<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view("auth.login");
    }
    public function showRegister() {
        return view("auth.register");
    }

    public function login(Request $request) {
        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role == "admin") {
                return redirect()->route("admin.dashboard");
            } else if ($user->role == "dokter") {
                return redirect()->route("dokter.dashboard");
            } else {
                return redirect()->route("pasien.dashboard");
            }
        }

        return back()->withErrors(["email" => "Email atau Password Salah!"]);
    }

    public function register(Request $request) {
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "alamat" => ["required", "string", "max:255"],
            "no_ktp" => ["required", "string", "max:30"],
            "no_hp" => ["required", "string", "max:20"],
            "email" => ["required", "string", "email", "max:255", "unique:users,email"],
            "password" => ["required", "confirmed"]
        ]);

        if (User::where("no_ktp", $request->no_ktp)->exist()) {
            return back()->withErrors(["no_ktp" => "Nomor KTP sudah terdaftar"]);
        }

        $no_rm = date("Ym") . "-" . str_pad(
            User::where("no_rm", "like", date("Ym") . "-%")->count() + 1, 3, "0", STR_PAD_LEFT
        );

        User::create([
            "name" => $request->nama,
            "alamat" => $request->alamat,
            "no_ktp" => $request->no_ktp,
            "no_hp" => $request->no_hp,
            "no_rm" => $no_rm,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role" => "pasien"
        ]);

        return redirect()->route("login");
    }

    public function logout(Request $request)
    {
        Auth::logout(); 

        // $request->session()->invalidate(); 
        // $request->session()->regenerateToken();

        // return redirect('/login')->with('success', 'Berhasil logout.');

        return redirect()->route("login");
    }
}
