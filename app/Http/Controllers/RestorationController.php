<?php

namespace App\Http\Controllers;

use App\Models\Restoration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RestorationController extends Controller
{
    public function index()
    {
       
    $Restoration = Restoration::all();

    if ($Restoration->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Lihat semua barang',
        'data' => $Restoration,
    ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'lending_id' => 'required',
            'date_time' => 'required',
            'total_good_stuff' => 'required',
            'total_defec_stuff' => 'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom Wajib Diisi!',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $Restoration = Restoration::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Barang Berhasil Disimpan!',
                'data' => $Restoration,
            ], 201);

            if ($Restoration) {
                return response()->json([
                    'success' => true,
                    'message' => 'Barang Berhasil Disimpan',
                    'data' => $Restoration,
                ], 201);
            } else {
                $Restoration = Restoration::create($request->all());
    
                return response()->json([
                    'success' => false,
                    'message' => 'Barang Gagal Disimpan!',
                ], 400);
            }
           
        }
    }

    public function show($id)
    {
        $Restoration = Restoration::find($id);
        if ($Restoration) {
            return response()->json([
                'success' => true,
                'message' => "Lihat Barang dengan id $id",
                'data' => $Restoration,
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

            return response()->json([
                'success' => true,
                'message' => "Data Berhasil Diubah dengan id $id",
                'data' => $Restoration,
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
            $Restoration = Restoration::findOrFail($id);

            $Restoration->delete();
    
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
