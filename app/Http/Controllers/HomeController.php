<?php

namespace App\Http\Controllers;

use App\Area;
use App\Category;
use App\Neighbourhood;
use App\Place;
use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    protected $client;

    public function index()
    {
        // get neighbourhood data
        // default to subang jaya for testing
        $areas = Area::where('name', 'Subang Jaya')->with('neighbourhoods')->get();
        $categories = Category::orderBy('name')->get();

        return view('home')->with('areas', $areas)
            ->with('categories', $categories);
    }

    public function __construct()
    {
        $this->client = new Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false)));
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


    public function filterByCategory(Requests\FilterByCategoryRequest $request)
    {
        $places = Place::with(['categories' => function($q) use ($request) {
            $q->where('name', $request->input('category'));
        }])->get();
        
        dd($places);
    }
    
    
}
