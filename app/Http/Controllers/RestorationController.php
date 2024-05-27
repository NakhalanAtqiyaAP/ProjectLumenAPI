<?php

namespace App\Http\Controllers;

use App\Models\Restoration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;
use App\Models\Lending;
use App\Models\StuffStock;



class RestorationController extends Controller
{
    public function __construct()
{
    $this->middleware('auth:api');
}

    public function index()
    {
        $restoration = Restoration::with('lending','user')->get();

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

    public function store(Request $request, $lending_id)
{
    try {
        $this->validate($request, [
            'date_time' => 'required',
            'total_good_stuff' => 'required',
            'total_defect_stuff' => 'required',  
        ]);

        $lending = Lending::where('id', $lending_id)->first();

        $totalStuffRestoration = (int)$request->total_good_stuff + (int)$request->total_defect_stuff;
        if ((int)$totalStuffRestoration > (int)$lending['total_stuff']) {
            return ApiFormatter::sendResponse(400, 'bad request', 'Total barang kembali lebih banyak dari barang dipinjam!');
        } else {
            $restoration = Restoration::updateOrCreate([
                'lending_id' => $lending_id
            ], [
                'date_time' => $request->date_time,
                'total_good_stuff' => $request->total_good_stuff,
                'total_defect_stuff' => $request->total_defect_stuff, 
                'user_id' => auth()->user()->id,
            ]);

            $stuffStock = StuffStock::where('stuff_id', $lending['stuff_id'])->first();
            $totalAvailableStock = (int)$stuffStock['total_available'] + (int)$request->total_good_stuff;
            $totalDefectStock = (int)$stuffStock['total_defec'] + (int)$request->total_defect_stuff;
            $stuffStock->update([
                'total_available' => $totalAvailableStock,
                'total_defec' => $totalDefectStock,
            ]);

            $lendingRestoration = Lending::where('id', $lending_id)->with('user', 'restoration', 'restoration.user', 'stuff', 'stuff.stuffStock')->first();
            return ApiFormatter::sendResponse(200, true,'success', $lendingRestoration);
        }
    } catch (\Exception $err) {
        return ApiFormatter::sendResponse(400, false,'bad request', $err->getMessage());
    }
}

 
    public function show($id)
    {
        try {
            // $data = Lending::with('user', 'restoration', 'restoration.user', 'stuff', 'stuff.stuffStock')->first();
            $data = Restoration::with('lending', 'user', 'lending.stuff', 'lending.stuff.stuffStock', )->first();
            if (!$data) {
                return ApiFormatter::sendResponse(404,false, 'Data tidak ditemukan', null);
            }
            return ApiFormatter::sendResponse(200,true, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(500,false, 'Kesalahan Server Internal', $err->getMessage());
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
            $total_defect_stuff = ($request ->total_defect_stuff) ? $request->total_defect_stuff : $Restoration->total_defect_stuff;
            

        if($Restoration){
            $Restoration->update([
            'Restoration_id' => $Restoration_id,
            'lending_id' => $lending_id,
            'date_time' => $date_time,
            'total_good_stuff' => $total_good_stuff,
            'total_defect_stuff' => $total_defect_stuff,
            
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
            // if ($restoration->count() === 0) {
            //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
            // }
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
