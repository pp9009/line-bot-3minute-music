<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakePlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makePlaylist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make playlist by specify time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
