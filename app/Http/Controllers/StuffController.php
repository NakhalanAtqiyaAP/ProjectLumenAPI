<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;

class StuffController extends Controller
{
  
    public function index()
    {
       $stuff = Stuff::with('stuffstock')->get();

       return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $stuff);
    // $stuff = Stuff::all();

    // if ($stuff->isEmpty()) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tidak ada data ditemukan',
    //     ], 404);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua barang',
    //     'data' => $stuff,
    // ], 200);
    }

    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);
            $stuff = Stuff::create([
                'name' => $request->input('name'),
                'category' => $request->input('category'),
            ]);
            
            return ApiFormatter::sendResponse(201, true, 'Barang Berhasil Disimpan!', $stuff);
        }
         catch (\Illuminate\Validation\ValidationException $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } catch (\Throwable $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
        }

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'category' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Semua kolom Wajib Diisi!',
        //         'data' => $validator->errors(),
        //     ], 400);
        // } else {
        //     $stuff = Stuff::create($request->all());

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Barang Berhasil Disimpan!',
        //         'data' => $stuff,
        //     ], 201);

        //     if ($stuff) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Barang Berhasil Disimpan',
        //             'data' => $stuff,
        //         ], 201);
        //     } else {
        //         $stuff = Stuff::create($request->all());
    
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
            $stuff = Stuff::with('stuffstock')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$stuff);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }



        // $stuff = Stuff::find($id);
        // if ($stuff) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => "Lihat Barang dengan id $id",
        //         'data' => $stuff,
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
    $stuff = Stuff::findOrFail($id);

    $name = ($request ->name) ? $request->name : $stuff->name;
    $category = ($request ->category) ? $request->  category : $stuff->category;

//     if($stuff){
    $stuff->update([
        'name' => $name,
    'category' => $category,
    ]);

    return ApiFormatter::sendResponse(200,true, "Data berhasil diubag dengan id $id");
  }
  catch(\Throwable $th){
    return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
  }
             
    //         return response()->json([
    //             'success' => true,
    //             'message' => "Data Berhasil Diubah dengan id $id",
    //             'data' => $stuff,
    //         ], 200);

    //     }else{
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Proses Gagal!',
    //         ], 404);
    //     }
        
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
            $stuff = Stuff::onlyTrashed()->get();
            //jika tidak ada data yang dihapus
            if ($stuff->count() === 0) {
                return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            }
            //menampilkan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $stuff);
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
    }



    public function restoreAll()
    {
        try{
            $stuffs = Stuff::onlyTrashed();

            $stuffs->restore();
            //jika tidak ada data yang dihapus
            if ($stuffs->count() === 0) {
                return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            }
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
            $stuff = Stuff::onlyTrashed()->where('id', $id)->forceDelete();
            if($stuff){
            $stuff->delete();
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
        $stuff = Stuff::onlyTrashed();

        $stuff->forceDelete();
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
            $stuff = Stuff::findOrFail($id);

            $stuff->delete();
    
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