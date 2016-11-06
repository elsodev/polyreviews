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

    public function __construct()
    {
        $this->client = new Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false)));
        $this->clientID = config('app.foursquare_clientId');
        $this->clientSecret = config('app.foursquare_clientSecret');
        $this->oauthQuery =  '&client_id='. $this->clientID. '&client_secret='. $this->clientSecret.'&v=20161015&m=foursquare';

    }

    /**
     * @return mixed
     */
    public function index()
    {
        // get neighbourhood data
        // default to subang jaya for testing
        $areas = Area::where('name', 'Subang Jaya')->with('neighbourhoods')->get();
        $categories = Category::orderBy('name')->get();

        return view('home')->with('areas', $areas)
            ->with('categories', $categories);
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

    /**
     * Get Foursquare Locations
     * 
     * @param $lat
     * @param $lng
     * @param int $radius
     * @return string
     */
    private function getFoursquareLocations($lat, $lng, $radius = 1000)
    {
        // using foursquare Explore API
        $response = $this->client->request('GET',
            'https://api.foursquare.com/v2/venues/explore?'.'ll='.$lat.','.$lng.'&radius='.$radius.'&section=food'.$this->oauthQuery,
            [
                'decode_content' => false
            ]);

        return $response->getBody()->getContents();
    }

    /**
     * Search Foursquare
     * 
     * @param Requests\FilterRequest $request
     * @return string
     */
    public function search(Requests\FilterRequest $request)
    {
        $category = $request->input('category');
        $query = trim($request->input('query'));
        $area = $request->input('area');

        if($query == 'all') {
            $category = '4d4b7105d754a06374d81259'; //foursquare food category id
        }

        // using Foursquare venue search API
        $response = $this->client->request('GET',
            'https://api.foursquare.com/v2/venues/search?near='.$area.'&query='.$query.'&limit=5&categoryId='.$category.$this->oauthQuery,
            [
                'decode_content' => false
            ]);

        $content = $response->getBody()->getContents();

        return $content;
    }
    
    
}
