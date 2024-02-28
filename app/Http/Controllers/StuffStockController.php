<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StuffStockController extends Controller
{
    public function index()
    {
       
    $stuffStock = StuffStock::all();
        $stuff = Stuff::all();

    if ($stuffStock->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Lihat semua Stock',
        'data'=>[ 
            $stuffStock,
            $stuff
            ]
    ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stuff_id' => 'required',
            'total_available' => 'required',
            'total_defec' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom Wajib Diisi!',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $stuffStock = StuffStock::updateOrCreate([
                'stuff_id'  => $request-> input('stuff_id')
            ],[
                'total_available' => $request->input('total_available'),
                'total_defec' => $request->input('total_defec'),
            ]);
            if ($stuffStock) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock Berhasil Disimpan',
                    'data' => $stuffStock,
                ], 201);
            } else {
                $stuffStock = StuffStock::create($request->all());
    
                return response()->json([
                    'success' => false,
                    'message' => 'Stock  Gagal Disimpan!',
                ], 400);
            }
           
        }
    }

    public function show($id)
    {
        $stuffStock = StuffStock::with('stuff')->find($id);
        if ($stuffStock) {
            return response()->json([
                'success' => true,
                'message' => "Lihat Stock dengan id $id",
                'data' => $stuffStock,
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
                $stuffStock = StuffStock::findOrFail($id);

            $total_available = ($request ->total_available) ? $request->total_available : $stuffStock->total_available;
            $total_defec = ($request ->total_defec) ? $request->  total_defec : $stuffStock->total_defec;

        if($stuffStock){
            $stuffStock->update([
            'total_availble' => $total_available,
            'total_defec' => $total_defec,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Data Stock Berhasil Diubah dengan id $id",
                'data' => $stuffStock,
            ], 200 );

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
            $stuffStock = StuffStock::findOrFail($id);

            $stuffStock->delete();
    
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
