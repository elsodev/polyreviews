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
    protected $client, $clientID, $clientSecret, $oauthQuery;

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
        $this->clientID = config('app.foursquare_clientId');
        $this->clientSecret = config('app.foursquare_clientSecret');
        $this->oauthQuery =  '&client_id='. $this->clientID. '&client_secret='. $this->clientSecret.'&v=20161015&m=foursquare';

    }

    /**
     * Get Starting Pins based on default coordinates
     *
     * @return string   JSON
     */
    public function getStartingPins()
    {
        $lng = config('app.locations.default_center.lng');
        $lat = config('app.locations.default_center.lat');
        return $this->getFoursquareLocations($lat, $lng);
    }
    
    /**
     * Change Location(on select neighbourhood dropdown)
     * 
     * @param Requests\ChangeLocationRequest $request
     * @return string
     */
    public function changeLocation(Requests\ChangeLocationRequest $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        return $this->getFoursquareLocations($lat, $lng);
    }
    
    private function getFoursquareLocations($lat, $lng, $radius = 1000)
    {
        $response = $this->client->request('GET',
            'https://api.foursquare.com/v2/venues/explore?'.'ll='.$lat.','.$lng.'&radius='.$radius.'&section=food'.$this->oauthQuery,
            [
                'decode_content' => false
            ]);

        return $response->getBody()->getContents();
    }


    public function filter(Requests\FilterRequest $request)
    {
        // center point of filter
        $longitude = $request->input('centerLong');
        $latitude = $request->input('centerLat');

        // set forusquare api query parameter
        $query = '&query=';
        if($request->input('search')) {
            $query .= $request->input('search');
        } else if($request->input('category') && $request->input('category') != 'all') {
            $query .= $request->input('category');
        } else {
            $query = '';
        }

        $response = $this->client->request('GET',
            'https://api.foursquare.com/v2/venues/explore?'.'ll='.$latitude.','.$longitude.'&radius=1000&section=food'.$query.$this->oauthQuery,
            [
                'decode_content' => false
            ]);

        $content = $response->getBody()->getContents();

        return $content;
    }
    
    
}
