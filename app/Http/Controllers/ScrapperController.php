<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Serps\SearchEngine\Google\GoogleClient;
use Serps\HttpClient\CurlClient;
use Serps\SearchEngine\Google\GoogleUrl;
use Illuminate\Http\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook\Exceptions\FacebookSDKException;

class ScrapperController extends Controller
{
    // for scrapper to identify itself as a browser
    private $default_user_agent = "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36";

    private $fbToken;

    public function __construct()
    {
        $this->fbToken = config('laravel-facebook-sdk.facebook_config.app_id').'|'.config('laravel-facebook-sdk.facebook_config.app_secret');
    }

    public function scrapGoogle($keyword)
    {
        // Create a google client using the curl http client
        $googleClient = new GoogleClient(new CurlClient());

        // Tell the client to use a user agent
        $userAgent = $this->default_user_agent;
        $googleClient->request->setUserAgent($userAgent);

        // Create the url that will be parsed
        $googleUrl = new GoogleUrl();
        $googleUrl->setSearchTerm($keyword);

        $response = $googleClient->query($googleUrl);

        $results = $response->getNaturalResults();

        return $results;

    }

    public function scrapFacebook($keyword)
    {

        $fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

        try {
            $responses = $fb->get('/search?q='.$keyword.'&type=place&access_token='.$this->fbToken)->getDecodedBody();
            return $responses;

        } catch(FacebookSDKException $e) {
            dd($e->getMessage());
        }
        
        return null;
    }

    public function getFacebookData($places)
    {
        $fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

        // obtain all info about the location
        foreach($places as $place) {
            try{
                $response = $fb->get('/'.$place['id'].'?access_token='.$this->fbToken.'&fields=id,name,location')->getDecodedBody();
            } catch(FacebookSDKException $e) {
                dd($e->getMessage());
            }

        }
    }

}
