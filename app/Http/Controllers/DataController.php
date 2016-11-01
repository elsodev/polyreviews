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
            $dbFacebookData = FacebookData::where('place_id', $place->id)->first();

            // only format data if true(exists)
            if($dbGoogleData) $googleData = $this->formatGoogleData($dbGoogleData);
            if($dbFacebookData) $facebookData = $this->formatFacebookData($dbFacebookData);

        }

        return response()->json([
            'success' => true,
            'place_id' => (!$place) ? $newPlace->id : $place->id,
            'google' => $googleData,
            'facebook' => $facebookData
        ]);
    }


    public function getGoogleData(Requests\GetDataRequest $request)
    {
        $query = $request->input('query');
        $place_id = $request->input('place_id');
        
        $scrapper = new ScrapperController();
        
        $results = $scrapper->scrapGoogle($query)->getItems();

        $data = [];

        if(count($results) > 0) {

            $count  = 0;

            foreach ($results as $result) {
                if($count > 5) break;
                $newGoogleData = GoogleData::create([
                    'place_id' => $place_id,
                    'title' => $result->getDataValue('title'),
                    'link' => $result->getDataValue('url'),
                    'description' => $result->getDataValue('description'),
                    'relevantOrder' => $result->getOnPagePosition(),
                ]);

                array_push($data, $newGoogleData);
                $count++;
            }
        }

        return $this->formatGoogleData($data);
    }

    public function getFacebookData(Requests\GetDataRequest $request)
    {

    }

    private function formatFacebookData($data)
    {

    }


    private function formatGoogleData($data)
    {
        $results = [];

        if(count($data) > 0) {

            foreach ($data as $item) {
                array_push($results, [
                        'id' => $item->id,
                        'title' => $item->title,
                        'link' => $item->link,
                        'description' => $item->description,
                        'relevantOrder' => $item->relevantOrder,
                    ]
                );

            }
        }

        return $results;
    }


}
