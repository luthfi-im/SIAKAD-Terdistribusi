<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectRegional
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        $regionalMap = [
            'teknik.siakad.test' => 1,
            'ekonomi.siakad.test' => 2,
            'kesehatan.siakad.test' => 3,
            'pusat.siakad.test' => 'pusat',
        ];

        $idRegional = $regionalMap[$host] ?? 1;

        session(['current_regional' => $idRegional]);

        return $next($request);
    }
}
