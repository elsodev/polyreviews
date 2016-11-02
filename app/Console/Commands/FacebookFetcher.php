<?php

namespace App\Console\Commands;

use App\Http\Controllers\ScrapperController;
use Illuminate\Console\Command;

class FacebookFetcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:facebook {keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $responses = $scrapper->scrapFacebook($this->argument('keyword'));

        $results = [];
        foreach($responses['data'] as $place) {
            $results[] = [
                'page_id' => $place['id'],
                'meta' => $scrapper->getFacebookData($place['id'])
            ];
        }

        dd($results);

    }
}
