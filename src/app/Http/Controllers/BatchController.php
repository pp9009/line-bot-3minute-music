<?php

namespace App\Http\Controllers;

use Artisan;

class BatchController extends Controller
{
    public function index()
    {
        Artisan::call('command:getTracks');
        Artisan::call('command:deleteTracks');
        return response('Batch process completed', 200);
    }
}
