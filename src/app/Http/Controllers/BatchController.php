<?php

namespace App\Http\Controllers;

use Artisan;

class BatchController extends Controller
{
    public function index()
    {
        Artisan::call('command:getTracks');
        Artisan::call('command:deleteTracks');
        // 処理が完了したらレスポンスを返す
        return response('Batch process completed', 200);
    }
}
