<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Models\Karyawan;
use App\Models\Cuti;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function index()
    {
        return PostResource::collection(Karyawan::all())
            ->additional([
                'success' => true,
                'message' => 'All employees retrieved successfully',
                'code' => 200,
            ]);
    }

    public function show(Karyawan $karyawan)
    {
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
                'code' => 404,
            ], 404);
        }

        return new PostResource($karyawan, true, 'Employee retrieved successfully', 200);
    }

    public function store(Request $request)
    {
        // Default values if certain fields are missing
        $data = $request->all();
        $data['nama'] = $request->filled('nama') ? $request->nama : 'Unknown Name';
        $data['alamat'] = $request->filled('alamat') ? $request->alamat : 'No Address Provided';
        $data['tanggal_lahir'] = $request->filled('tanggal_lahir') ? $request->tanggal_lahir : '1900-01-01';
        $data['tanggal_bergabung'] = $request->filled('tanggal_bergabung') ? $request->tanggal_bergabung : now()->toDateString();

        // Validasi input
        $validator = Validator::make($data, [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'tanggal_lahir' => 'required|date',
            'tanggal_bergabung' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Generate nomor_induk
        $lastKaryawan = Karyawan::orderBy('id', 'desc')->first();
        $nextNumber = $lastKaryawan ? $lastKaryawan->id + 1 : 1;
        $nomorInduk = 'IP06' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Buat karyawan baru
        $karyawan = Karyawan::create(array_merge($data, ['nomor_induk' => $nomorInduk]));

        return new PostResource($karyawan, true, 'Employee created successfully', 201);
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
                'code' => 404,
            ], 404);
        }

        // Default values if certain fields are missing
        $data = $request->all();
        $data['nama'] = $request->filled('nama') ? $request->nama : $karyawan->nama;
        $data['alamat'] = $request->filled('alamat') ? $request->alamat : $karyawan->alamat;
        $data['tanggal_lahir'] = $request->filled('tanggal_lahir') ? $request->tanggal_lahir : $karyawan->tanggal_lahir;
        $data['tanggal_bergabung'] = $request->filled('tanggal_bergabung') ? $request->tanggal_bergabung : $karyawan->tanggal_bergabung;

        // Validasi input
        $validator = Validator::make($data, [
            'nama' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|required|string|max:500',
            'tanggal_lahir' => 'sometimes|required|date',
            'tanggal_bergabung' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $karyawan->update($data);
        return new PostResource($karyawan, true, 'Employee updated successfully', 200);
    }

    public function destroy(Karyawan $karyawan)
    {
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
                'code' => 404,
            ], 404);
        }

        $karyawan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully',
            'data' => null,
            'code' => 204,
        ], 204);
    }

    public function firstThreeJoined()
    {
        $karyawans = Karyawan::orderBy('tanggal_bergabung', 'asc')->take(3)->get();
        return PostResource::collection($karyawans)
            ->additional([
                'success' => true,
                'message' => 'First three employees retrieved successfully',
                'code' => 200,
            ]);
    }
    // Bagian untuk cuti
    public function indexCuti()
    {
        $cutis = Cuti::with('karyawan')->get();
        return PostResource::collection($cutis)
            ->additional([
                'success' => true,
                'message' => 'All leave records retrieved successfully',
                'code' => 200,
            ]);
    }

    public function karyawanYangPernahCuti()
    {
        $karyawanIds = Cuti::distinct()->pluck('nomor_induk');
        $karyawans = Karyawan::whereIn('nomor_induk', $karyawanIds)->get();
        return PostResource::collection($karyawans)
            ->additional([
                'success' => true,
                'message' => 'Employees who have taken leave retrieved successfully',
                'code' => 200,
            ]);
    }

    public function sisaCuti()
    {
        $karyawans = Karyawan::all();
        $sisaCuti = $karyawans->map(function ($karyawan) {
            $totalCuti = Cuti::where('nomor_induk', $karyawan->nomor_induk)->sum('lama_cuti');
            $sisaCuti = 12 - $totalCuti;
            return [
                'nomor_induk' => $karyawan->nomor_induk,
                'nama' => $karyawan->nama,
                'sisa_cuti' => $sisaCuti,
            ];
        });
        return response()->json($sisaCuti, 200)
            ->header('Content-Type', 'application/json')
            ->setStatusCode(200);
    }
}
