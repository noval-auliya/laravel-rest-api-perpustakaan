<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\KategoriBukuRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\KategoriBuku;
use Log;

class KategoriBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->query('per-page', 10);

        $kategori = KategoriBuku::orderBy('id', 'desc')->paginate($perPage);

        if ($kategori->isEmpty()) {
            return response()->json([
                'message' => 'Data kategori buku kosong.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar kategori buku.',
            'data' => $kategori->items(),
            'pagination' => [
                'current_page' => $kategori->currentPage(),
                'per_page'     => $kategori->perPage(),
                'total'        => $kategori->total(),
                'last_page'    => $kategori->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KategoriBukuRequest $request)
    {
        DB::beginTransaction();
        try {
            $kategori = KategoriBuku::create($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Kategori berhasil dibuat.',
                'data'    => $kategori
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ERROR APP : " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal membuat kategori.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = KategoriBuku::find($id);

        if (!$kategori) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail kategori.',
            'data'    => $kategori
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KategoriBukuRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $kategori = KategoriBuku::find($id);

            if (!$kategori) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan.'
                ], 404);
            }

            $kategori->update($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Kategori berhasil diperbarui.',
                'data' => $kategori
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ERROR APP : " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal memperbarui kategori.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kategori = KategoriBuku::withCount('buku')->find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        if ($kategori->buku_count > 0) {
            return response()->json([
                'message' => 'Kategori memiliki buku terkait. Hapus semua buku (child) terlebih dahulu sebelum menghapus kategori.'
            ], 409);
        }

        try {
            $kategori->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus kategori karena constraint database.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Kategori berhasil dihapus.'], 200);
    }
}
