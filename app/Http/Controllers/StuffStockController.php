<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Support\Facades\Validator;

class StuffStockController extends Controller
{
    public function __construct()
{
    $this->middleware('auth:api');
}
    public function index()
    {
        $stuffstock = StuffStock::with('stuff')->get();
        $stuff = Stuff::get();
        $stock = StuffStock::get();

        $data = ['barang' => $stuff, 'stok' => $stock];

        return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $stuffstock);
    // $stuffStock = StuffStock::all();
    //     $stuff = Stuff::all();

    // if ($stuffStock->isEmpty()) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Tidak ada data ditemukan',
    //     ], 404);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua Stock',
    //     'data'=>[ 
    //         $stuffStock,
    //         $stuff
    //         ]
    // ], 200);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total_available' => 'required',
                'total_defec' => 'required',
            ]);
            $stuffstock = StuffStock::create([
                'stuff_id' => $request->input('stuff_id'),
                'total_available' => $request->input('total_available'),
                'total_defec' => $request->input('total_defec'),
            ]);
            
            return ApiFormatter::sendResponse(201, true, 'Barang Berhasil Disimpan!', $stuffstock);
        }
         catch (\Illuminate\Validation\ValidationException $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } catch (\Throwable $th) {
            
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->getMessage());
       

        // $validator = Validator::make($request->all(), [
        //     'stuff_id' => 'required',
        //     'total_available' => 'required',
        //     'total_defec' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Semua kolom Wajib Diisi!',
        //         'data' => $validator->errors(),
        //     ], 400);
        // } else {
        //     $stuffStock = StuffStock::updateOrCreate([
        //         'stuff_id'  => $request-> input('stuff_id')
        //     ],[
        //         'total_available' => $request->input('total_available'),
        //         'total_defec' => $request->input('total_defec'),
        //     ]);
        //     if ($stuffStock) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Stock Berhasil Disimpan',
        //             'data' => $stuffStock,
        //         ], 201);
        //     } else {
        //         $stuffStock = StuffStock::create($request->all());
    
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Stock  Gagal Disimpan!',
        //         ], 400);
        //     }
           
        // }
    }
    }

    public function show($id)
        {
            try{
                $stuffstock = StuffStock::with('stuff')->findOrFail($id);

                return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$stuffstock);
            }
            catch(\Throwable $th)
            {
                return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
            }

            // $stuffStock = StuffStock::with('stuff')->find($id);
            // if ($stuffStock) {
            //     return response()->json([
            //         'success' => true,
            //         'message' => "Lihat Stock dengan id $id",
        //         'data' => $stuffStock,
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
            $stuff = StuffStock::findOrFail($id);
        
            $stuff_id = ($request ->stuff_id) ? $request->stuff_id : $stuff->stuff_id;
            $total_available = ($request ->total_available) ? $request->  total_available : $stuff->total_available;
            $total_defec = ($request ->total_defec) ? $request->  total_defec : $stuff->total_defec;

        
        //     if($stuff){
            $stuff->update([
                'stuff_id' => $stuff_id,
            'total_available' => $total_available,
            'total_defec' => $total_defec,
            ]);
        
            return ApiFormatter::sendResponse(200,true, "Data berhasil diubah dengan id $id");
          }
          catch(\Throwable $th){
            return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
          }
          
    //     try{
    //             $stuffStock = StuffStock::findOrFail($id);

    //         $total_available = ($request ->total_available) ? $request->total_available : $stuffStock->total_available;
    //         $total_defec = ($request ->total_defec) ? $request->  total_defec : $stuffStock->total_defec;

    //     if($stuffStock){
    //         $stuffStock->update([
    //         'total_availble' => $total_available,
    //         'total_defec' => $total_defec,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Data Stock Berhasil Diubah dengan id $id",
    //             'data' => $stuffStock,
    //         ], 200 );

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
    //     }

    }
    public function deleted()
    {
        try {
            $stuffstock = StuffStock::onlyTrashed()->get();
            //jika tidak ada data yang dihapus
            // if ($stuffstock->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            //menampilkan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $stuffstock);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $stuffstock = StuffStock::onlyTrashed()->findOrFail($id);
            $has_stock = StuffStock::where('stuff_id',$stuffstock->stuff_id)->get();

            //mengecek apakah data stock sudah ada/duplikat dan jika ada tidak perlu di restart
            if ($has_stock->count() === 1) {
                $message = "Data Stock sudah ada, tidak boleh ada yang duplikat data stok untuk satu barang silakan update data dengan id stock $stuffstock->stuff_id";
            }else{
                $stuffstock->restore();
                $message = "Berhasil mengembalikan data yang telah dihapus!";
            }

            // $stuffstock->restore();
            //jika tidak ada data yang dihapus
            // if ($stuffstock->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
            //mengembalikan data-data yang dihapus
            return ApiFormatter::sendResponse(200, true, "Berhasil Mengembalikan data yang telah dihapus!", ['id' => $id, 'stuff_id' => $stuffstock->stuff_id]);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
        }
    }

    public function restoreAll()
    {
        try{
            $stuffstocks = StuffStock::onlyTrashed()->restore();
            $stuffstocks->restore();
            //jika tidak ada data yang dihapus
            // if ($stuffstocks->count() === 0) {
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
            $stuffstock = StuffStock::onlyTrashed()->where('id', $id)->forceDelete();
            if($stuffstock){
            $stuffstock->forceDelete();
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
        $stuffstock = StuffStock::onlyTrashed();
        $stuffstock->forceDelete();
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
            $stuff = StuffStock::findOrFail($id);

            $stuff->delete();
    
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data dengan id $id",['id' => $id]);

        }        
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
    }
    }