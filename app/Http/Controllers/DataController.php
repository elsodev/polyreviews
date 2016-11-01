<?php

namespace App\Http\Controllers;

use App\Category;
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

        }

        

        return response()->json([
            'success' => true,
            'googleData' => $googleData,
            'facebookData' => $facebookData
        ]);
    }


}
