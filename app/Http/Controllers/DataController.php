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

            // check category exists or not
            foreach($data['venue']['location']['categories'] as $category)
            {
                $dbCategory = Category::where('name', $category);

            }
        } else {

        }
        $data['venue']['id'];
    }


}
