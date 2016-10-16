<?php

namespace App\Http\Controllers;

use Serps\SearchEngine\Google\GoogleClient;
use Serps\HttpClient\CurlClient;
use Serps\SearchEngine\Google\GoogleUrl;
use Illuminate\Http\Request;

class ScrapperController extends Controller
{
    // for scrapper to identify itself as a browser
    private $default_user_agent = "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36";

    public function __construct()
    {

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

    }

}
