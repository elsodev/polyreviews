<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    protected $client;

    public function index()
    {
        return view('home');
    }

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    public function getStartingPins()
    {
        $longitude = config('app.locations.default_center.lng');
        $latitude = config('app.locations.default_center.lat');
        $clientId = config('app.foursquare_clientId');
        $clientSecret = config('app.foursquare_clientSecret');
        $oauth_query = '&client_id='. $clientId. '&client_secret='. $clientSecret.'&v=20161015&m=foursquare';
        $response = $this->client->request('GET',
            'https://api.foursquare.com/v2/venues/explore?'.'ll='.$latitude.','.$longitude.'&radius=1000&section=food,'.$oauth_query,
            [
                'decode_content' => false
            ]);

        $content = $response->getBody()->getContents();
        
        return $content;
    }
    
    
}
