<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use App\Models\Restoration;
use App\Models\Lending;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;

class LendingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    // $data = Lending::with('stuff', 'user', 'restoration')->get(); 
    public function index()
    {
          $data = Lending::with('stuff', 'user', 'restoration')->get(); 

        return ApiFormatter::sendResponse(200,true,'Lihat semua barang', $data);
    // $stuff = InboundStuff::all();
    // $stuffstock = StuffStock::all();
    // $inboundstuff = InboundStuff::all();

    // if ($iStuff->isEmpty()) {
    //     return ApiFormatter::sendResponse(404,false,'Data tidak ditemukan', $iStuff);
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Lihat semua stock barang',
    //     'data' => [
    //         'barang' => $stuff,
    //         'stock barang' => $stuffstock,
    //         'barang masuk' => $inboundstuff
    //     ]
    // ], 200);
    }

    public function store(Request $request)
    {
        try {

            $this->validate($request, [
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'total_stuff' => 'required|numeric',
                'notes' => 'required',
            ]);
    
            $totalAvailable = StuffStock::where('stuff_id', $request->stuff_id)->value('total_available');
            if (is_null($totalAvailable)) {
                return ApiFormatter::sendResponse(400, 'Bad request', 'Belum ada data inbound');
            }
    
            if ((int)$request->total_stuff > (int)$totalAvailable) {
                return ApiFormatter::sendResponse(400, 'Bad request', 'Stock tidak tersedia');
            }
    
            $lending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'total_stuff' => $request->total_stuff,
                'notes' => $request->notes ? $request->notes : '-',
                'user_id' => auth()->id(),
            ]);
    
            $totalAvailableNow = (int)$totalAvailable - (int)$request->total_stuff;
    
            StuffStock::where('stuff_id', $request->stuff_id)->update(['total_available' => $totalAvailableNow]);
    
            $dataLending = Lending::where('id', $lending->id)->with('user', 'stuff', 'stuff.stuffstock')->first();
            return ApiFormatter::sendResponse(201, true, 'Barang berhasil disimpan!', $dataLending);
        } catch (\Exception $err) {

            return ApiFormatter::sendResponse(400, 'Bad request', $err->getMessage());
        }
    }
        public function show($id)
    {
        try {
            $lending = Lending::with('stuff', 'restoration')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id", $lending);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $Lending = Lending::findOrFail($id);

            $stuff_id = ($request->stuff_id) ? $request->stuff_id : $Lending->stuff_id;
            $date_time = ($request->date_time) ? $request->date_time : $Lending->date_time;
            $name = ($request->name) ? $request->name : $Lending->name;
            $user_id = ($request->user_id) ? $request->user_id : $Lending->user_id;
            $notes = ($request->notes) ? $request->notes : $Lending->notes;
            $total_stuff = ($request->total_stuff) ? $request->total_stuff : $Lending->total_stuff;

            $Lending->update([
                'stuff_id' => $stuff_id,
                'date_time' => $date_time,
                'name' => $name,
                'user_id' => $user_id,
                'notes' => $notes,
                'total_stuff' => $total_stuff,
            ]);

            return ApiFormatter::sendResponse(200, true, "Data berhasil diubah dengan id $id");
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(400, false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $lending = Lending::find($id);

            if (!$lending) {
                return ApiFormatter::sendResponse(404, 'not found', 'Data peminjaman tidak ditemukan');
            }

            $Restoration = $lending->restoration()->exists();

            if ($Restoration) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Peminjaman sudah memiliki pengembalian, tidak dapat dibatalkan');
            }

            $totalStuff = $lending->total_stuff;
            $stuffId = $lending->stuff_id;

            $lending->delete();

            // mengembalikan total_stuff ke total_available pada stuff_stock jika berhasil menghapus peminjaman
            $stuffStock = StuffStock::where('stuff_id', $stuffId)->first();
            if ($stuffStock) {
                $stuffStock->total_available += $totalStuff;
                $stuffStock->save();
            }

            return ApiFormatter::sendResponse(200, 'success', 'Berhasil hapus data peminjaman');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
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

    
    public function permanentDelate($id)
    {
        try{
            $Lending = Lending::onlyTrashed()->where('id', $id)->forceDelete();
            if($Lending){
            $Lending->delete();
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
        $lending =Lending::onlyTrashed();

        $lending->forceDelete();
        return ApiFormatter::sendResponse(200, true, "Berhasil menghapus semua data secara permanen!");
    }
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
    }
}

public function restore($id)
    {
        try {
            $lending = Lending::onlyTrashed()->where('id', $id);

            $lending->restore();
            //jika tidak ada data yang dihapus
            // if ($lending->count() === 0) {
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
            $lending = Lending::onlyTrashed();

            $lending->restore();
            //jika tidak ada data yang dihapus
            // if ($lending->count() === 0) {
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

}
