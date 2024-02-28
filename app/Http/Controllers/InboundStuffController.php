<?php

namespace App\Http\Controllers;

use App\Models\InboundStuff;
use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InboundStuffController extends Controller
{
    public function index()
    {
    
    $stuff = Stuff::all();
    $stuffstock = StuffStock::all();
    $inboundstuff = InboundStuff::all();

    if ($inboundstuff->isEmpty() && $stuffstock -> isEmpty() && $inboundstuff -> isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Lihat semua stock barang',
        'data' => [
            'barang' => $stuff,
            'stock barang' => $stuffstock,
            'barang masuk' => $inboundstuff
        ]
    ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stuff_id' => 'required',
            'total' => 'required',
            'date' => 'required',
            'proof_file' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Semua kolom Wajib Diisi!',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $file = $request->file("proof_id");
            $fileName =$request->input('stuff_id') . '_' . strtotime($request->input('Date')) . '-' .
            $file->getClientOriginalExtension();
            $file->move('proof', $fileName);
            $inbound = InboundStuff::create([
                'stuff_id' => $request->input('stuff_id'),
                'total' => $request->input('total'),
                'date' => $request->input('date'),
                'proof_file' => $request->input('proof_file'),
            ]);

            $stock = StuffStock::where('stuff_id', $request->input('stuff_id')->first());

            $total_stock = (int)$stock->total_available + (int) $request->input('total');
            
            $stock -> update([
                'total_availble' => (int) $total_stock
            ]);
        }
    }

    public function show($id)
    {
        $inboundstuff = InboundStuff::with('stuff')->find($id);
        if ($inboundstuff) {
            return response()->json([
                'success' => true,
                'message' => "Lihat Barang dengan id $id",
                'data' => $inboundstuff,
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
                $stock = InboundStuff::with('stuff')->find($id);

            $stuff_id = ($request ->stuff_id) ? $request->stuff_id : $stock->stuff_id;
            $total = ($request ->total) ? $request->total : $stock->total;
            $date = ($request ->date) ? $request->date : $stock->date;
            $proff_id = ($request ->proff_id) ? $request->proff_id : $stock->proff_id;

        if($stock){
            $stock->update([
            'stuff_id' => $stuff_id,
            'total' => $total,
            'date' => $date,
            'proff_id' => $proff_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Data Berhasil Diubah dengan id $id",
                'data' => $stock,
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
            $inboundstuff = InboundStuff::findOrFail($id);

            $inboundstuff->delete();
    
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
