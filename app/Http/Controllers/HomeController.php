<?php

// app/Http/Controllers/HomeController.php

// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Client;

class HomeController extends Controller
{
    public function getDashboardData()
    {
        $totalPerusahaan = Perusahaan::count();
        $totalClient = Client::count();

        return response()->json([
            'total_perusahaan' => $totalPerusahaan,
            'total_client' => $totalClient,
        ]);
    }
}
