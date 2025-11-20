<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BukuRequest;
use Illuminate\Http\Request;
use App\Models\Buku;
use Log;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->query('per-page', 10);

        $buku = Buku::with('kategori')->orderBy('id', 'desc')->paginate($perPage);

        if ($buku->isEmpty()) {
            return response()->json([
                'message' => 'Data buku kosong.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar buku.',
            'data' => $buku->items(),
            'pagination' => [
                'current_page' => $buku->currentPage(),
                'per_page'     => $buku->perPage(),
                'total'        => $buku->total(),
                'last_page'    => $buku->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BukuRequest $request)
    {
        DB::beginTransaction();
        try {
            $buku = Buku::create($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Buku berhasil dibuat.',
                'data'    => $buku
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ERROR APP : " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal membuat buku.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'Buku tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail buku.',
            'data'    => $buku
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BukuRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $buku = Buku::find($id);

            if (!$buku) {
                return response()->json([
                    'message' => 'Buku tidak ditemukan.'
                ], 404);
            }

            $buku->update($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Buku berhasil diperbarui.',
                'data' => $buku
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ERROR APP : " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal memperbarui buku.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $buku = Buku::withCount('peminjaman')->find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.'
            ], 404);
        }

        if ($buku->peminjaman_count > 0) {
            return response()->json([
                'message' => 'Buku sudah dipinjam. Harap hapus data peminjaman terlebih dahulu.'
            ], 409);
        }

        try {
            $buku->delete();

            return response()->json([
                'message' => 'Buku berhasil dihapus.'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus buku karena constraint database.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
