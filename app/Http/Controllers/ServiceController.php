<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Service;
use App\Models\Stuff;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
      
    public function index()
    {
        try{
            $service = Service::with('stuff')->get();

            return ApiFormatter::sendResponse(200,true,"berhasil menampilkan data", $service);
        }
        catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
       
    }

    public function store(Request $request)
    {
        try{
            

            $this->validate($request,[
                // 'stuff_id' => 'required',
                'keterangan' => 'required',
                'barang' => 'required',
                'periksa' => 'required',
            ]);
            $service = Service::create([
                // 'stuff_id' => $request->input('stuff_id'),
                'keterangan' => $request->input('keterangan'),
                'barang' => $request->input('barang'),
                'periksa' => $request->input('periksa'),
            ]);

            return ApiFormatter::sendResponse(201,true,"Berhasil Membuat Data",$service);
        }
        catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service, $id)
    {
        try{
            $service = Service::with('stuff')->findOrFail($id);

            return ApiFormatter::sendResponse(200, true, "Lihat Barang dengan id $id",$service);
        }
        catch(\Throwable $th)
        {
            return ApiFormatter::sendResponse(404, false, "Barang dengan id $id tidak ditemukan");
        }
    }
    public function update(Request $request, Service $service, $id)
    {
        try{
            $service = Service::findOrFail($id);
        
            // $stuff_id = ($request ->stuff_id) ? $request->stuff_id : $service->stuff_id;
            $keterangan = ($request ->keterangan) ? $request->  keterangan : $service->keterangan;
            $periksa = ($request ->periksa) ? $request->  periksa : $service->periksa;
            $barang = ($request ->barang) ? $request->  barang : $service->barang;
        
        //     if($service){
            $service->update([
            // 'stuff_id' => $stuff_id,
            'keterangan' => $keterangan,
            'periksa' => $periksa,
            'barang' => $barang,
            ]);
        
            return ApiFormatter::sendResponse(200,true, "Data berhasil diubah dengan id $id");
          }
          catch(\Throwable $th){
            return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
          }
          
    }

    
    public function destroy(Service $service, $id)
    {
        try{
            $service = Service::findOrFail($id);

        $service->delete();
        } catch(\Throwable $th){
            return ApiFormatter::sendResponse(400,false, 'Proses Gagal! Silakan coba lagi!', $th->getMessage());
          }
    }
}
