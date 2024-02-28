<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LendingController extends Controller
{
    public function index()
    {
       
    $Lending = Lending::all();

    if ($Lending->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Lihat semua data peminjaman',
        'data' => $Lending,
    ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stuff_id' => 'required',
            'date_time' => 'required',
            'name' => 'required',
            'user_id' => 'required',
            'notes' => 'required',
            'total_stuff' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom Wajib Diisi!',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $Lending = Lending::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman Berhasil Disimpan!',
                'data' => $Lending,
            ], 201);

            if ($Lending) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman Berhasil Disimpan',
                    'data' => $Lending,
                ], 201);
            } else {
                $Lending = Lending::create($request->all());
    
                return response()->json([
                    'success' => false,
                    'message' => 'Barang Gagal Disimpan!',
                ], 400);
            }
           
        }
    }

    public function show($id)
    {
        $Lending = Lending::find($id);
        if ($Lending) {
            return response()->json([
                'success' => true,
                'message' => "Lihat data dengan id $id",
                'data' => $Lending,
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
                $Lending = Lending::findOrFail($id);
                
            $stuff_id = ($request ->stuff_id) ? $request->stuff_id : $Lending->stuff_id;
            $date_time = ($request ->date_time) ? $request->date_time : $Lending->date_time;
            $name = ($request ->name) ? $request->name : $Lending->name;
            $user_id = ($request ->user_id) ? $request->user_id : $Lending->user_id;
            $notes = ($request ->notes) ? $request->notes : $Lending->notes;
            $total_stuff = ($request ->total_stuff) ? $request->total_stuff : $Lending->total_stuff;

        if($Lending){
            $Lending->update([
            'stuff_id' => $stuff_id,
            'date_time' => $date_time,
            'name' => $name,
            'user_id' => $user_id,
            'notes' => $notes,
            'total_stuff' => $total_stuff,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Data Berhasil Diubah dengan id $id",
                'data' => $Lending,
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
            $Lending = Lending::findOrFail($id);

            $Lending->delete();
    
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
