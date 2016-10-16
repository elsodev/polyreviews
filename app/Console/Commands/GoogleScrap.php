<?php

namespace App\Console\Commands;

use App\Http\Controllers\ScrapperController;
use Illuminate\Console\Command;

class GoogleScrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:google {keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraps google search results';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $scrapper = new ScrapperController();

        $this->info($this->argument('keyword'));
        
        $results = $scrapper->scrapGoogle($this->argument('keyword'));
        
        var_dump($results);
    }
}
