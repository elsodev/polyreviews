<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class FoursquareFetcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:foursquare {near}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch foursquare information base on area name';


    protected $client;

    /**
     * Create a new command instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clientId = config('app.foursquare_clientId'); //V01N1WCHCB0I5TPTRHB0VESPZPHUYMIEYLSE5JVGFDBFAQHX
        $clientSecret = config('app.foursquare_clientSecret'); //JSFSHRA5OHNIKMMABS4SJVXDEZWN1XIL1NGU0F4PO2JELFRT
        $oauth_query = '&client_id='. $clientId. '&client_secret='. $clientSecret. '&v=20161015&m=foursquare';
        $response = $this->client->request('GET','https://api.foursquare.com/v2/venues/search?near='.$this->argument('near').$oauth_query,
            [
                'decode_content' => false
            ]);

        $content = json_decode($response->getBody()->getContents());

        $venus = $content->response->venues;
        foreach ($venus as $venu)
        {
            $this->info($venu->id);
        }

    }
}
