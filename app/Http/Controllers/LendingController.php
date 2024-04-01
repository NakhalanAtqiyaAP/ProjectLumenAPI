<?php

namespace App\Http\Controllers;
use App\Models\Stuff;
use App\Models\Restoration;
use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;


class LendingController extends Controller
{
    public function index()
    {
        $lending = Lending::with('stuff','restoration')->get();
        $stuff = Stuff::get();
        $resto = Restoration::get();

        $data = ['barang' => $stuff, 'pengembalian' => $resto];

        return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $lending);
    // $lending = Lending::all();

    // if ($Lending->isEmpty()) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tidak ada data ditemukan',
    //     ], 404);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua data peminjaman',
    //     'data' => $Lending,
    // ], 200);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
            'stuff_id' => 'required',
            'date_time' => 'required',
            'name' => 'required',
            'user_id' => 'required',
            'notes' => 'required',
            'total_stuff' => 'required',
            ]);

            $lending= Lending::create([
                'stuff_id' => $request->input('stuff_id'),
                'date_time' => $request->input('date_time'),
                'name' => $request->input('name'),
                'user_id' => $request->input('user_id'),
                'notes' => $request->input('notes'),
                'total_stuff' => $request->input('total_stuff'),

            ]);
            
            return ApiFormatter::sendResponse(201, true, 'Barang Berhasil Disimpan!', $lending);
        }
         catch (\Illuminate\Validation\ValidationException $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } catch (\Throwable $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
       

        // $validator = Validator::make($request->all(), [
        //     'stuff_id' => 'required',
        //     'date_time' => 'required',
        //     'name' => 'required',
        //     'user_id' => 'required',
        //     'notes' => 'required',
        //     'total_stuff' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Semua kolom Wajib Diisi!',
        //         'data' => $validator->errors(),
        //     ], 400);
        // } else {
        //     $Lending = Lending::create($request->all());

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Peminjaman Berhasil Disimpan!',
        //         'data' => $Lending,
        //     ], 201);

        //     if ($Lending) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Peminjaman Berhasil Disimpan',
        //             'data' => $Lending,
        //         ], 201);
        //     } else {
        //         $Lending = Lending::create($request->all());
    
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Barang Gagal Disimpan!',
        //         ], 400);
        //     }
           
        // }
    }
    }
    public function show($id)
    {
        try{
            $lending = Lending::with('stuff','restorations')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$lending);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }
        // $Lending = Lending::find($id);
        // if ($Lending) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => "Lihat data dengan id $id",
        //         'data' => $Lending,
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
                $Lending = Lending::findOrFail($id);
                
            $stuff_id = ($request ->stuff_id) ? $request->stuff_id : $Lending->stuff_id;
            $date_time = ($request ->date_time) ? $request->date_time : $Lending->date_time;
            $name = ($request ->name) ? $request->name : $Lending->name;
            $user_id = ($request ->user_id) ? $request->user_id : $Lending->user_id;
            $notes = ($request ->notes) ? $request->notes : $Lending->notes;
            $total_stuff = ($request ->total_stuff) ? $request->total_stuff : $Lending->total_stuff;

        // if($Lending){
            $Lending->update([
            'stuff_id' => $stuff_id,
            'date_time' => $date_time,
            'name' => $name,
            'user_id' => $user_id,
            'notes' => $notes,
            'total_stuff' => $total_stuff,
            ]);

            return ApiFormatter::sendResponse(200,true, "Data berhasil diubah dengan id $id");
        }
        catch(\Throwable $th){
          return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
        }

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Data Berhasil Diubah dengan id $id",
    //             'data' => $Lending,
    //         ], 200);

    //     // else{
    //     //     return response()->json([
    //     //         'success' => false,
    //     //         'message' => 'Proses Gagal!',
    //     //     ], 404);
    //     // }
        
    // }
    // catch(\Throwable $th)
    //     {
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Proses gagal! Data dengan id $id tidak ditemukan"
    //         ], 404);
        }
        public function deleted()
        {
            try {
                $lending = Lending::onlyTrashed()->get();
                //jika tidak ada data yang dihapus
                // if ($lending->count() === 0) {
                //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
                // }
                //menampilkan data-data yang dihapus
                return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $lending);
            } catch (\Throwable $th) {
                return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
            }
        }
        public function restore($id)
        {
            try {
                $stuff = Stuff::onlyTrashed()->where('id', $id);
    
                $stuff->restore();
                //jika tidak ada data yang dihapus
                if ($stuff->count() === 0) {
                    return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
                }
                //mengembalikan data-data yang dihapus
                return ApiFormatter::sendResponse(200, true, "Berhasil Mengembalikan data yang telah dihapus!", ['id' => $id]);
            } catch (\Throwable $th) {
                return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
            }
            // try {
            //     $lending = Lending::onlyTrashed()->findOrFail($id);
            //     $has_stuff = Stuff::where('stuff_id',$lending->stuff_id)->get();
            //     $has_resto = Restoration::where('user_id',$lending->user_id)->get();
    
            //     //mengecek apakah data stock sudah ada/duplikat dan jika ada tidak perlu di restart
            //     if ($has_stuff && $has_resto->count() === 1) {
            //         $message = "Data Stock sudah ada, tidak boleh ada yang duplikat data stok untuk satu barang silakan update data dengan id stock $lending->stuff_id dan $lending->user_id";
            //         return ApiFormatter::sendResponse(400, false, $message);
            //     }
            //         $lending->restore();
            //         $message = "Berhasil mengembalikan data yang telah dihapus!";
              
            //     // $lending->restore();
            //     //jika tidak ada data yang dihapus
            //     // if ($lending->count() === 0) {
            //     //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            //     // }
            //     //mengembalikan data-data yang dihapus
            //     return ApiFormatter::sendResponse(200, true, "Berhasil Mengembalikan data yang telah dihapus!", [
            //         'id' => $id, 
            //         'stuff_id' => $lending->stuff_id, 
            //         'user_id' => $lending->user_id
            //     ]);
            // } catch (\Throwable $th) {
            //     return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
            // }
        }

        public function restoreAll()
        {
            try{
                $lendings = Lending::onlyTrashed()->restore();
                $lendings->restore();
                //jika tidak ada data yang dihapus
                // if ($lendings->count() === 0) {
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
                $lending = Lending::onlyTrashed()->where('id', $id)->forceDelete();
                if($lending){
                $lending->forceDelete();
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
            $lending = Lending::onlyTrashed();
            $lending->forceDelete();
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
            $lending = Lending::findOrFail($id);

            $lending->delete();
    
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data dengan id $id",['id' => $id]);

        }        
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
    }
}
