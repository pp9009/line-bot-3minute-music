<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\UseCases\Spotify\DeleteTracks as UsecasesDeleteTracks;

class DeleteTracks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteTracks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete spotify tracks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $usecase = App::make(UsecasesDeleteTracks::class);
        $usecase->invoke();
        return 0;
    }
}
