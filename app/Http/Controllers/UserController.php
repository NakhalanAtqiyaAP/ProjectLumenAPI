<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lending;
use App\Models\Restoration;
use app\Models\keyApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiFormatter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // public function login(Request $request)
    // {
    //     try{
    //         $credentials = $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ]);
        
    //         if (Auth::attempt($credentials)) {
    //             $request->session()->regenerate();
        
    //             $user = Auth::user();
        
    //             unset($user->password);
        
    //             $keyData = [
    //                 'user_id' => $user->id,
    //                 'key' => Str::random(40), 
    //                 'level' => 1,
    //                 'ignore_limits' => 0,
    //                 'is_private_key' => 0,
    //                 'ip_addresses' => $request->ip(),
    //                 'date_created' => Carbon::now()->timestamp, 
    //             ];
        
    //             $key = keyApi::create($keyData);
        
    //             return ApiFormatter::sendResponse(200,true,'Berhasil Login!',  ['user' => $user,
    //             'api_key' => $key]);
    //         }else{
    //             return ApiFormatter::sendResponse(401,false,'Login Gagal, Email atau Password Salah!' );
    //         }
    //     }

    //     catch (\Illuminate\Validation\ValidationException $th) {
            
    //         return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
    //     } catch (\Throwable $th) {
            
    //         return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
    //     }
       
    // }

    public function index()
    {
        $user = User::with('lending', 'restorations')->get();
        $user = User::get();
        $lending = Lending::get();
        $restoration = Restoration::get();

        $data = ['user' => $user, 'barang' => $lending, 'pengembalian' => $restoration];

        return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $user);
    // $User = User::all();

    // if ($User->isEmpty()) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Data tidak ditemukan',
    //     ], 404);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua data users',
    //     'data' => $User,
    // ], 200);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
            'username' => 'required|unique:users|min:3',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'role' => ['required', Rule::in(['admin', 'staff'])],

        
            ]);
            $user = User::create([
                'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make( $request->input('password')),
                    'role' => $request->input('role'),
            ]);
            
            return ApiFormatter::sendResponse(201, true, 'Barang Berhasil Disimpan!', $user);
        }
         catch (\Illuminate\Validation\ValidationException $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } 
        // catch (\Throwable $th) {
            
        //     return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
        // }

        // $validator = Validator::make($request->all(), [
        //     'username' => 'required|min:3',
        //     'email' => 'required|unique:users|email',
        //     'password' => 'required|min:6',
        //     'role' => 'required|in:admin,staff'
        // ]);
    
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Semua kolom Wajib Diisi!',
        //         'data' => $validator->errors(),
        //     ], 400);
        // }
    
        // // Hash password sebelum menyimpannya ke dalam basis data
        // $hashedPassword = Hash::make($request->input('password'));
    
        // // Buat User dan simpan password yang telah di-hash
        // $user = User::create([
        //     'username' => $request->input('username'),
        //     'email' => $request->input('email'),
        //     'password' => $hashedPassword,
        //     'role' => $request->input('role'),
        // ]);
    
        // if ($user) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Data Berhasil Disimpan!',
        //         'data' => $user,
        //     ], 201);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Data Gagal Disimpan!',
        //     ], 400);
        // }
    }
    public function show($id)
    {
        try{
            $user = User::with('lending', 'restorations')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$user);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }


        // $User = User::find($id);
        // if ($User) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => "Lihat data dengan id $id",
        //         'data' => $User,
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => "Data dengan id $id tidak ditemukan",
        //     ], 404);
        // }
    }

    public function update(Request $request, $id)
    {
        try{
            $user = User::findOrFail($id);
        
            $username = ($request ->username) ? $request->username : $user->username;
            $email = ($request ->email) ? $request->email : $user->email;
            $password = ($request ->password) ? $request->password : $user->password;
            $role = ($request ->role) ? $request->role : $user->role;
                        
        //     if($user){
            $user->update([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
            ]);
        
            return ApiFormatter::sendResponse(200,true, "Data berhasil diubah dengan id $id");
          }
          catch(\Throwable $th){
            return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
          }
        
//         try{
//                 $User = User::findOrFail($id);
                
//             $username = ($request ->username) ? $request->username : $User->username;
//             $email = ($request ->email) ? $request->email : $User->email;
//             $password = ($request ->password) ? $request->password : $User->password;
//             $role = ($request ->role) ? $request->role : $User->role;
            
//         if($User){
//             $User->update([
//             'username' => $username,
//             'email' => $email,
//             'password' => $password,
//             'role' => $role,
// ]);


//             return response()->json([
//                 'success' => true,
//                 'message' => "Data Berhasil Diubah dengan id $id",
//                 'data' => $User,
//             ], 200);

//         }else{
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Proses Gagal!',
//             ], 404);
//         }
        
//     }
//     catch(\Throwable $th)
//         {
//             return response()->json([
//                 'success' => false,
//                 'message' => "Proses gagal! Data dengan id $id tidak ditemukan"
//             ], 404);
//         }

    }
    public function deleted()
    {
        try {
            $user = User::onlyTrashed()->get();
            //jika tidak ada data yang dihapus
            // if ($user->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            //menampilkan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $user);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->where('id', $id);
            // $has_lending = Lending::where('stuff_id',$user->stuff_id)->get();
            // $has_res= Restoration::where('user_id',$user->stuff_id)->get();

            // $user->restore();
            // //jika tidak ada data yang dihapus
            // if ($has_lending && $has_res->count() === 1) {
            //     return ApiFormatter::sendResponse(200, true, "data sudah ada tidak boleh duplikat");
            // }else{
                $user->restore();
            //     $message = "Berhasil mengembalikan data yang telah dihapus!";
            // }
            //mengembalikan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Berhasil Mengembalikan data yang telah dihapus!", ['id' => $id]);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restoreAll()
    {
        try{
            $users = User::onlyTrashed();

            $users->restore();
            //jika tidak ada data yang dihapus
            // if ($users->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            //mengembalikan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Berhasil mengembalikan barang yang telah dihapus");
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
        }
    }

    public function permanentDelate($id)
    {
        try{
            $user = User::onlyTrashed()->where('id', $id)->forceDelete();
            if($user){
            $user->forceDelete();
            }
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data secara permanen!", ["id"=> $id]);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
        }
    }

    public function permanentDelateAll()
{
    try{
        $user = User::onlyTrashed();

        $user->forceDelete();
        return ApiFormatter::sendResponse(200, true, "Berhasil menghapus semua data secara permanen!");
    }
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
}

    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);

            $user->delete();
    
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data dengan id $id",['id' => $id]);
        }        
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
    }

    // public function uploadProfileImage(Request $request)
    // {
    //     $this->validate($request, [
    //         'profileImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $user = Auth::user();
    //     $image = $request->file('profileImage');
    //     $imageName = time().'.'.$image->getClientOriginalExtension();
    //     $path = $image->storeAs('profile_images', $imageName, 'public');

    //     // Update user profile image path
    //     $user->profile_image = $path;
    //     $user->save();

    //     return response()->json(['profileImage' => $path], 200);
    // }

}
