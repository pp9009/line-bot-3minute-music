<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Usecases\Spotify\GetTracks as UsecasesGetTracks;

class GetTracks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getTracks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get spotify tracks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $usecase = App::make(UsecasesGetTracks::class);
        $usecase->invoke();
        return 0;
    }
}
