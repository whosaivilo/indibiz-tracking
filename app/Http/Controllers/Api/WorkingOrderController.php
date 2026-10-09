<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkingOrder;
use Illuminate\Http\Request;

class WorkingOrderController extends Controller
{
    /**
     * Menampilkan daftar semua Working Order
     */
    public function index(Request $request)
    {
        // Menarik data WorkingOrder dari database
        // Kita urutkan dari yang terbaru, lalu membaginya 50 data per halaman (Pagination)
        $workingOrders = WorkingOrder::latest()->paginate(50);
        
        // Membungkus data ke dalam format JSON (nampan makanan) untuk dikirim ke Frontend
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Working Order',
            'data'    => $workingOrders
        ]);
    }
}
