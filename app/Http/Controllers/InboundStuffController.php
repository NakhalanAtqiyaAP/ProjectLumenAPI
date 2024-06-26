<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use App\Models\StuffStock;
use App\Models\InboundStuff;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;



class InboundStuffController extends Controller
{
    public function __construct()
{
    $this->middleware('auth:api');
}
public function index()
{
    $iStuff = InboundStuff::with('stuff.stuffStock')->get();

    $iStuff->each(function ($item) {
        $item->proff_file_url = url('public/proff/' . $item->proff_file);
    });

    return ApiFormatter::sendResponse(200, true, 'Lihat semua barang', $iStuff);
}

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stuff_id' => 'required|integer',
                'total' => 'required|integer',
                'date' => 'required',
                'proff_file' => 'required|image',
            ]);
    
            if ($validator->fails()) {
                return ApiFormatter::sendResponse(400, false, 'Semua Kolom Wajib Diisi!', $validator->errors());
            } else {
                // Memeriksa ketersediaan stok
                $stock = StuffStock::where('stuff_id', $request->input('stuff_id'))->first();
    
                if (!$stock) {
                    // Jika stok tidak ditemukan
                    return ApiFormatter::sendResponse(404, false, 'Stok tidak ditemukan untuk stuff_id yang ditemukan.');
                }
    
                $file = $request->file('proff_file');
                $fileName = $request->input('stuff_id') . '_' . strtotime($request->input('date')) . '_' . strtotime(date('H:i')) . '.' . $file->getClientOriginalExtension();
                $file->move(('public/proff'), $fileName);
    
                $inbound = InboundStuff::create([
                    'stuff_id' => $request->input('stuff_id'),
                    'total' => $request->input('total'),
                    'date' => $request->input('date'),
                    'proff_file' => $fileName,
                ]);
    
                // Update total stok setelah data inbound dibuat
                $total_stock = $stock->total_available + (int)$request->input('total');
                $stock->update(['total_available' => $total_stock]);
    
                return ApiFormatter::sendResponse(201, true, 'Barang Masuk Berhasil Disimpan!', $inbound);
            }
        } catch (\Illuminate\Validation\ValidationException $th) {
            return ApiFormatter::sendResponse(400, false, 'Terdapat Kesalahan Input Silahkan Coba Lagi!', $th->validator->errors());
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(500, false, 'Terjadi kesalahan pada server.', $e->getMessage());
        }
    }
    
    public function show($id)
    {
        try {
            $inbound = InboundStuff::with('stuff', 'stuff.stuffStock')->findOrFail($id);
            $inbound->proff_file_url = url('public/proff/' . $inbound->proff_file);
    
            return ApiFormatter::sendResponse(200, true, "Lihat Barang Masuk dengan id $id", $inbound);
        } catch (\Throwable $th) {
            return ApiFormatter::sendResponse(404, false, "Data dengan id $id tidak ditemukan", $th->getMessage());
        }
    }


       public function update(Request $request, $id)
{
    try {
        $inbound = InboundStuff::with('stuff', 'stuff.stuffStock')->findOrFail($id);

        $stuff_id = $request->input('stuff_id', $inbound->stuff_id);
        $total = $request->input('total', $inbound->total);
        $date = $request->input('date', $inbound->date);

        if ($request->hasFile('proff_file')) {
            $file = $request->file('proff_file');
            $fileName = $stuff_id . '_' . strtotime($date) . '_' . strtotime(date('H:i')) . '.' . $file->getClientOriginalExtension();
            $file->move(('public/proff'), $fileName);
        } else {
            $fileName = $inbound->proff_file;
        }

        $total_s = $total - $inbound->total;
        $total_stock = (int)$inbound->stuff->stuffStock->total_available + $total_s;

        $inbound->stuff->stuffStock->update(['total_available' => $total_stock]);

        // Update data inbound
        $inbound->update([
            'stuff_id' => $stuff_id,
            'total' => $total,
            'date' => $date,
            'proff_file' => $fileName
        ]);

        return ApiFormatter::sendResponse(200, true, "Berhasil Ubah Data Barang Masuk dengan id $id", $inbound);
    } catch (\Throwable $th) {
        return ApiFormatter::sendResponse(400, false, "Proses Gagal!", $th->getMessage());
    }
}

        public function deleted()
        {
            try {
                $stuff = InboundStuff::onlyTrashed()->get();
                $stuff->each(function ($item) {
                    $item->proff_file_url = url('public/proff/' . $item->proff_file);
                });
                //jika tidak ada data yang dihapus
                // if ($stuff->count() === 0) {
                //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
                // }
                //menampilkan data-data yang dihapus
                return ApiFormatter::sendResponse(200, true, "Lihat Data Barang yang dihapus", $stuff);
            } catch (\Throwable $th) {
                return ApiFormatter::sendResponse(404, false, "Proses gagal! silahkan coba lagi!", $th->getMessage());
            }
        }

        public function restore($id)
        {
            try {
                $stuff = InboundStuff::onlyTrashed()->where('id', $id);

                $stuff->restore();
                //jika tidak ada data yang dihapus
                // if ($stuff->count() === 0) {
                //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
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
                $stuffs = InboundStuff::onlyTrashed();

                $stuffs->restore();
        
                // if ($stuffs->count() === 0) {
                //     return ApiFormatter::sendResponse(200, true, "Tidak ada data yang dihapus");
                // }
        
                return ApiFormatter::sendResponse(200, true, "Berhasil mengembalikan barang yang telah dihapus");
            }
            catch(\Throwable $th)
            {
                return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());
            }
        }

        public function permanentDelete($id)
        {
            try {
                $inbound = InboundStuff::onlyTrashed()->findOrFail($id);
        
                $filePath = app()->basePath('public/proff/' . $inbound->proff_file);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
        
                $stock = StuffStock::where('stuff_id', $inbound->stuff_id)->first();
        
                $available = $stock->total_available - $inbound->total;
                $defect = ($available < 0) ? $stock->total_defect + ($available * -1) : $stock->total_defect;
        
                $stock->update([
                    'total_available' => $available,
                    'total_defect' => $defect
                ]);
        
                $inbound->forceDelete();
        
                return ApiFormatter::sendResponse(200, true, "Berhasil hapus permanen data yang telah dihapus!", ['id' => $id]);
            } catch (\Throwable $th) {
                return ApiFormatter::sendResponse(404, false, "Proses gagal! Silakan coba lagi!", $th->getMessage());
            }
        }
        
        public function permanentDeleteAll()
        {
            try {
                $inbounds = InboundStuff::onlyTrashed()->get();
        
                foreach ($inbounds as $inbound) {
                    $filePath = app()->basePath('public/proff/' . $inbound->proff_file);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
        
                    $stock = StuffStock::where('stuff_id', $inbound->stuff_id)->first();
        
                    if ($stock) {
                        $available = $stock->total_available - $inbound->total;
                        $defect = ($available < 0) ? $stock->total_defect + ($available * -1) : $stock->total_defect;
        
                        $stock->update([
                            'total_available' => $available,
                            'total_defect' => $defect
                        ]);
                    }
        
                    $inbound->forceDelete();
                }
        
                return ApiFormatter::sendResponse(200, true, "Berhasil hapus permanen semua data yang telah dihapus!");
            } catch (\Throwable $th) {
                return ApiFormatter::sendResponse(500, false, "Proses gagal! Silakan coba lagi.", $th->getMessage());
            }
        }
    public function destroy($id)
    {
        try{
            $stuff = InboundStuff::findOrFail($id);
            $stock=StuffStock::where('stuff_id', $stuff->stuff_id)->first();

            $available_min =$stock->total_available - $stuff->total;
            $available = ($available_min < 0) ? 0 : $available_min;
            $defec =($available_min < 0) ? $stock ->total_defec + ($available * 1) : $stock->total_defec;
            $stock->update([
                'total_available' => $available,
                'total_defec' => $defec
            ]);

            $stuff->delete();
    
            return ApiFormatter::sendResponse(200, true, "Berhasil menghapus data dengan id $id",['id' => $id]);

    
        }        
    catch(\Throwable $th)
    {
        return ApiFormatter::sendResponse(404, false, "Proses gagal! silakan coba lagi", $th->getMessage());

    }
    }

        
}
