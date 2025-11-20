<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\PeminjamanRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Log;

class PeminjamanController extends Controller
{
    // Daftar Semua Peminjaman
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->query('per-page', 10);

        $peminjaman = Peminjaman::with('buku','user')->orderBy('id', 'desc')->paginate($perPage);

        if ($peminjaman->isEmpty()) {
            return response()->json([
                'message' => 'Data peminjaman kosong.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar peminjaman buku.',
            'data' => $peminjaman->items(),
            'pagination' => [
                'current_page' => $peminjaman->currentPage(),
                'per_page'     => $peminjaman->perPage(),
                'total'        => $peminjaman->total(),
                'last_page'    => $peminjaman->lastPage(),
            ]
        ], 200);
    }

    // Pinjam Buku
    public function store(PeminjamanRequest $request)
    {
        try {
            $peminjaman = DB::transaction(function () use ($request) {
                $buku = Buku::where('id', $request->buku_id)->lockForUpdate()->first();

                // trigger validasi seperti exists
                if (! $buku) {
                    throw ValidationException::withMessages([
                        'buku_id' => ['Buku tidak ditemukan.'],
                    ]);
                }

                // validasi stok tidak tersedia
                if ($buku->stok <= 0) {
                    throw ValidationException::withMessages([
                        'buku_id' => ['Stok buku tidak tersedia.'],
                    ]);
                }

                // pengurangan stok buku
                $buku->decrement('stok', 1);

                // buat record peminjaman
                $peminjaman = Peminjaman::create([
                    'user_id' => $request->user()->id,
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => Carbon::now()->toDateString(),
                    'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                    'status' => 'dipinjam',
                ]);

                return $peminjaman->load('buku');
            }, 5);

            return response()->json([
                'message' => 'Buku berhasil dipinjam.',
                'data'    => $peminjaman
            ], 201);

        } catch (ValidationException $e) {
            Log::error("ERROR APP : " . $e->getMessage());
            return response()->json([
                'message' => 'Validasi',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("ERROR APP : " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memproses peminjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Pengembalian Buku
    public function pengembalian(Request $request, $id)
    {
        try {
            $response = DB::transaction(function () use ($id) {

                $peminjaman = Peminjaman::with('buku')->lockForUpdate()->find($id);

                if (!$peminjaman) {
                    throw ValidationException::withMessages([
                        'data' => ['Data peminjaman tidak ditemukan.']
                    ]);
                }

                // cek apakah sudah dikembalikan
                if ($peminjaman->status === 'selesai') {
                    throw ValidationException::withMessages([
                        'data' => ['Buku sudah dikembalikan sebelumnya.']
                    ]);
                }

                // update data peminjaman (dikembalikan)
                $peminjaman->update([
                    'tanggal_kembali_sebenarnya' => Carbon::now()->toDateString(),
                    'dikembalikan_at' => Carbon::now(),
                    'status' => 'selesai',
                ]);

                // tambah stok buku
                $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->first();
                $buku->increment('stok', 1);

                return [
                    'message' => 'Pengembalian berhasil.',
                    'data' => $peminjaman->load('buku')
                ];
            });

            return response()->json($response, 200);

        } catch (ValidationException $e) {
            Log::error("ERROR APP : " . $e->getMessage());
            return response()->json([
                'message' => 'Validasi',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("ERROR APP : " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses pengembalian.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Lihat Peminjaman Detail
    public function show($id)
    {
        $peminjaman = Peminjaman::with('buku', 'user')->find($id);

        if (!$peminjaman) {
            return response()->json([
                'message' => 'Peminjaman tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail peminjaman.',
            'data'    => $peminjaman
        ], 200);
    }
}
