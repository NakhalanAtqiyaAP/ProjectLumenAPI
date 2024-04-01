<?php

namespace App\Http\Controllers;

use App\Models\Restoration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;

class RestorationController extends Controller
{
    public function index()
    {
        $restoration = Restoration::with('lending')->get();

       return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $restoration);
    // $Restoration = Restoration::all();

    // if ($Restoration->isEmpty()) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tidak ada data ditemukan',
    //     ], 404);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua barang',
    //     'data' => $Restoration,
    // ], 200);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'user_id' => 'required',
                'lending_id' => 'required',
                'date_time' => 'required',
                'total_good_stuff' => 'required',
                'total_defec_stuff' => 'required',
            ]);
            $restoration = Restoration::create([
                'user_id' => $request->input('user_id'),
                'lending_id' => $request->input('lending_id'),
                'date_time' => $request->input('date_time'),
                'total_good_stuff' => $request->input('total_good_stuff'),
                'total_defec_stuff' => $request->input('total_defec_stuff'),
            ]);
            
            return ApiFormatter::sendResponse(201, true, 'Barang Berhasil Disimpan!', $restoration);
        }
         catch (\Illuminate\Validation\ValidationException $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } catch (\Throwable $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
        }

        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required',
        //     'lending_id' => 'required',
        //     'date_time' => 'required',
        //     'total_good_stuff' => 'required',
        //     'total_defec_stuff' => 'required',
            
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Semua kolom Wajib Diisi!',
        //         'data' => $validator->errors(),
        //     ], 400);
        // } else {
        //     $Restoration = Restoration::create($request->all());

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Barang Berhasil Disimpan!',
        //         'data' => $Restoration,
        //     ], 201);

        //     if ($Restoration) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Barang Berhasil Disimpan',
        //             'data' => $Restoration,
        //         ], 201);
        //     } else {
        //         $Restoration = Restoration::create($request->all());
    
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Barang Gagal Disimpan!',
        //         ], 400);
        //     }
           
        // }
    }

    public function show($id)
    {
        try{
            $resoration = Restoration::with('lending')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$resoration);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }

        // $Restoration = Restoration::find($id);
        // if ($Restoration) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => "Lihat Barang dengan id $id",
        //         'data' => $Restoration,
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
                $Restoration = Restoration::findOrFail($id);
                
            $date_time = ($request ->date_time) ? $request->date_time : $Restoration->date_time;
            $Restoration_id = ($request ->Restoration_id) ? $request->Restoration_id : $Restoration->Restoration_id;
            $lending_id = ($request ->lending_id) ? $request->lending_id : $Restoration->lending_id;
            $date_time = ($request ->date_time) ? $request->date_time : $Restoration->date_time;
            $total_good_stuff = ($request ->total_good_stuff) ? $request->total_good_stuff : $Restoration->total_good_stuff;
            $total_defec_stuff = ($request ->total_defec_stuff) ? $request->total_defec_stuff : $Restoration->total_defec_stuff;
            

        if($Restoration){
            $Restoration->update([
            'Restoration_id' => $Restoration_id,
            'lending_id' => $lending_id,
            'date_time' => $date_time,
            'total_good_stuff' => $total_good_stuff,
            'total_defec_stuff' => $total_defec_stuff,
            
            ]);

            return ApiFormatter::sendResponse(200,true, "Data berhasil diubah dengan id $id");
    }
}
    catch(\Throwable $th){
        return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
     
    }
}
public function deleted()
    {
        try {
            $restoration = Restoration::onlyTrashed()->get();
            //jika tidak ada data yang dihapus
            if ($restoration->count() === 0) {
                return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            }
            //menampilkan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $restoration);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $restoration = Restoration::onlyTrashed()->where('id', $id);

            $restoration->restore();
            //jika tidak ada data yang dihapus
            // if ($restoration->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            // //mengembalikan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Berhasil Mengembalikan data yang telah dihapus!", ['id' => $id]);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restoreAll()
    {
        try{
            $restoration = Restoration::onlyTrashed();

            $restoration->restore();
            //jika tidak ada data yang dihapus
            // if ($restoration->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            // //mengembalikan data-data yang dihapus
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
            $restoration = Restoration::onlyTrashed()->where('id', $id)->forceDelete();
            if($restoration){
            $restoration->delete();
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
        $restoration = Restoration::onlyTrashed();

        $restoration->forceDelete();
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
            $Restoration = Restoration::findOrFail($id);

            $Restoration->delete();
    
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data dengan id $id",['id' => $id]);

        }        
    catch(\Throwable $th)
    {       
         return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
    }
}
