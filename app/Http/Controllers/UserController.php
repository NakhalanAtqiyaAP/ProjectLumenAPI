<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
    
        $email = $request->input('email');
        $password = $request->input('password');
    
        // Cari pengguna berdasarkan email
        $user = User::where('email', $email)->first();
    
        // Periksa apakah pengguna ditemukan
        if (!$user) {
            return response()->json(['message' => 'Login failed, User Salah'], 401);
        }
    
        // Periksa apakah password cocok
        if (!Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Login failed, Password Salah'], 401);
        }
    
        // Jika email dan password sesuai, maka lakukan login
        Auth::login($user);
    
        // Setelah login berhasil, Anda dapat mengembalikan respons atau token akses
        return response()->json(['message' => 'Login berhasil', 'user' => $user]);
    }


    public function index()
    {
       
    $User = User::all();

    if ($User->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Lihat semua data users',
        'data' => $User,
    ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,staff'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom Wajib Diisi!',
                'data' => $validator->errors(),
            ], 400);
        }
    
        // Hash password sebelum menyimpannya ke dalam basis data
        $hashedPassword = Hash::make($request->input('password'));
    
        // Buat User dan simpan password yang telah di-hash
        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $hashedPassword,
            'role' => $request->input('role'),
        ]);
    
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan!',
                'data' => $user,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Disimpan!',
            ], 400);
        }
    }
    public function show($id)
    {
        $User = User::find($id);
        if ($User) {
            return response()->json([
                'success' => true,
                'message' => "Lihat data dengan id $id",
                'data' => $User,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Data dengan id $id tidak ditemukan",
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        
        try{
                $User = User::findOrFail($id);
                
            $username = ($request ->username) ? $request->username : $User->username;
            $email = ($request ->email) ? $request->email : $User->email;
            $password = ($request ->password) ? $request->password : $User->password;
            $role = ($request ->role) ? $request->role : $User->role;
            
        if($User){
            $User->update([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
]);


            return response()->json([
                'success' => true,
                'message' => "Data Berhasil Diubah dengan id $id",
                'data' => $User,
            ], 200);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Proses Gagal!',
            ], 404);
        }
        
    }
    catch(\Throwable $th)
        {
            return response()->json([
                'success' => false,
                'message' => "Proses gagal! Data dengan id $id tidak ditemukan"
            ], 404);
        }

    }

    public function destroy($id)
    {
        try{
            $User = User::findOrFail($id);

            $User->delete();
    
            return response()->json([
                'success' => true,
                'message' => "Data Berhasil Dihapus dengan id $id",
                'data' => [
                    'id' => $id,
                ],
            ], 200);
    
        }        
    catch(\Throwable $th)
    {
        return response()->json([
            'success' => false,
            'message' => "Proses gagal! Data dengan id $id tidak ditemukan"
        ], 404);
    }
    }
}
