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
    private $default_user_agent = [
        "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
        "Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16"
    ];

    private $fb, $fbToken;

    public function __construct()
    {
        $this->fbToken = config('laravel-facebook-sdk.facebook_config.app_id').'|'.config('laravel-facebook-sdk.facebook_config.app_secret');
        $this->fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

    }

    public function scrapGoogle($keyword)
    {
        // Create a google client using the curl http client
        $googleClient = new GoogleClient(new CurlClient());

        // Tell the client to use a user agent
        $googleClient->request->setUserAgent(array_rand($this->default_user_agent, 1)[0]);

        // Create the url that will be parsed
        $googleUrl = new GoogleUrl();
        $googleUrl->setSearchTerm($keyword);

        $response = $googleClient->query($googleUrl);

        $results = $response->getNaturalResults();

        return $results;

    }

    /**
     * Scrap Facebook
     *
     * @param $keyword
     * @return null
     */
    public function scrapFacebook($keyword)
    {

        $keyword =  preg_replace('/[^\00-\255]+/u', '', $keyword); // remove non english word
        $keyword =   preg_replace("/\([^)]+\)/", "", $keyword ); // remove ()
        $keyword = preg_replace('/[^A-Za-z0-9\-]/', ' ', $keyword); // Removes special chars.

        try {
            return $this->fb->get('/search?q='.$keyword.'&type=place&access_token='.$this->fbToken)->getDecodedBody();

        } catch(FacebookSDKException $e) {
            dd($e->getMessage());
        }
        
        return null;
    }

    public function getFacebookData($placeID)
    {
        $fields = 'name,overall_star_rating,rating_count,about,category,checkins,price_range,website,were_here_count';

        try {
            return $this->fb->get(
                '/'.$placeID.'?access_token='.$this->fbToken.'&fields='.$fields
            )->getDecodedBody();
        } catch(FacebookSDKException $e)
        {
            dd($e->getMessage());
        }
        

    }

}
