<?php

namespace App\Http\Controllers;

use App\Category;
use App\FacebookData;
use App\GoogleData;
use App\Place;
use Illuminate\Http\Request;

use App\Http\Requests;

class DataController extends Controller
{
    protected $scrapper;
    
    public function __construct()
    {
        $this->scrapper = new ScrapperController();
    }

    /**
     * Create/Update places,categories based on Forusquare data
     *
     * @param Requests\SyncRequest $request
     * @return mixed
     */
    public function sync(Requests\SyncRequest $request)
    {
        // checks DB if foursquare data is already in db
        $data = $request->input('fsq');
        $googleData = null;
        $facebookData = null;


        $place = Place::where('lng', $data['venue']['location']['lng'])
            ->where('lat', $data['venue']['location']['lat'])
            ->first();

        $newPlace = null;

        if(!$place) {
            // does not exists, create this place
            $newPlace = Place::create([
                'lng' => $data['venue']['location']['lng'],
                'lat' => $data['venue']['location']['lat'],
                'name' => $data['venue']['name'],
                'address' => json_encode($data['venue']['location']['formattedAddress']),
                'contact' => '',
                'last_fetch' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            // create categories
            foreach($data['venue']['categories'] as $category)
            {
                $dbCategory = Category::where('name', $category['name'])->exists();
                if(!$dbCategory) {
                    $newCategory = Category::create([
                        'name' => $category['name']
                    ]);

                    $newPlace->categories()->attach($newCategory->id);
                }

            }
        } else {
            // already exists in database
            $place->update([
                'name' => $data['venue']['name'],
                'address' => json_encode($data['venue']['location']['formattedAddress']),
            ]);

            // check whether google data exists
            $dbGoogleData = GoogleData::where('place_id', $place->id)->orderBy('relevantOrder', 'asc')->get();
            $dbFacebookData = FacebookData::where('place_id', $place->id)->orderBy('ratings', 'desc')->get();

            // only format data if true(exists)
            if(count($dbGoogleData) > 0) $googleData = $this->formatGoogleData($dbGoogleData);
            if(count($dbFacebookData) > 0) $facebookData = $this->formatFacebookData($dbFacebookData);

        }

        return response()->json([
            'success' => true,
            'place_id' => (!$place) ? $newPlace->id : $place->id,
            'google' => $googleData,
            'facebook' => $facebookData
        ]);
    }


    /**
     * @param Requests\GetDataRequest $request
     * @return array
     */
    public function getGoogleData(Requests\GetDataRequest $request)
    {
        $query = $request->input('query');
        $place_id = $request->input('place_id');
        

        $results = $this->scrapper->scrapGoogle($query)->getItems();

        $data = [];

        if(count($results) > 0) {

            $count  = 0;

            foreach ($results as $result) {
                if($count >= 5) break;
                $newGoogleData = GoogleData::create([
                    'place_id' => $place_id,
                    'title' => base64_encode($result->getDataValue('title')),
                    'link' => $result->getDataValue('url'),
                    'description' => base64_encode($result->getDataValue('description')),
                    'relevantOrder' => $result->getOnPagePosition(),
                ]);

                array_push($data, $newGoogleData);
                $count++;
            }
        }

        return $this->formatGoogleData($data);
    }

    /**
     * @param Requests\GetDataRequest $request
     * @return array
     */
    public function getFacebookData(Requests\GetDataRequest $request)
    {
        $responses = $this->scrapper->scrapFacebook($request->input('query'));
        $place_id = $request->input('place_id');

        $data = [];
        if(count($responses['data']) > 0) {

            $count = 0;

            foreach($responses['data'] as $response) {
                if($count >= 5) break;
                $place =  $this->scrapper->getFacebookData($response['id']);
                $fbData = FacebookData::where('obj_id', $response['id'])->first();

                if(!$fbData) {

                    // create new data
                    $newFbData = FacebookData::create([
                        'place_id' => $place_id,
                        'ratings' => $place['overall_star_rating'],
                        'obj_id' => $place['id'],
                        'data' => json_encode($place)
                    ]);

                    array_push($data, $newFbData);

                } else {

                    // update data
                    $fbData->update([
                        'place_id' => $place_id,
                        'ratings' => $place['overall_star_rating'],
                        'obj_id' => $place['id'],
                        'data' => json_encode($place)
                    ]);

                    array_push($data, $fbData);

                }

                $count++;
            }

        }

        return $this->formatFacebookData($data);

    }

    /**
     * Format Facebook Data for Javascript Consumption
     *
     * @param $data
     * @return array
     */
    private function formatFacebookData($data)
    {

        $results = [];

        if(count($data) > 0) {

            foreach ($data as $item) {

                $decoded = json_decode($item->data);

                array_push($results, [
                        'id' => $item->id,
                        'name' => $decoded->name,
                        'ratings' => $item->ratings,
                        'rating_count' => (isset($decoded->rating_count))? $decoded->rating_count: 0,
                        'were_here_count' => (isset($decoded->were_here_count)) ? $decoded->were_here_count : 0,
                        'link' => 'https://facebook.com/'.$decoded->id,
                        'description' => (isset($decoded->about)) ? $decoded->about: 'No description available' ,
                        'check_ins' => (isset($decoded->checkins)) ? $decoded->checkins: 0,
                        'price_range' => (isset($decoded->price_range)) ? $decoded->price_range: 'No price range available',
                    ]
                );

            }
        }

        return $results;

    }


    /**
     * Formats Google Data for Javascript Consumption
     *
     * @param $data
     * @return array
     */
    private function formatGoogleData($data)
    {
        $results = [];

        if(count($data) > 0) {

            foreach ($data as $item) {
                array_push($results, [
                        'id' => $item->id,
                        'title' => utf8_encode(base64_decode($item->title)),
                        'link' => $item->link,
                        'description' => utf8_encode(base64_decode($item->description)),
                        'relevantOrder' => $item->relevantOrder,
                    ]
                );

            }
        }

        return $results;
    }


}
