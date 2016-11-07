<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataTest extends TestCase
{
    //************************** Sync Test *****************************//

    /**
     * Test: POST sync with foursquare data
     *
     * Expected return: json ('success' => true)
     */
    public function testSync_POST_withData()
    {
        $responses = app('App\Http\Controllers\HomeController')->getStartingPins();
        $responses = json_decode($responses, true);

        $data = $responses["response"]["groups"][0]["items"][0]; // get foursquare first item search result

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input)
            ->seeJsonContains([
               'success' => true
            ]);
    }

    /**
     * Test: POST sync without foursquare data
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testSync__POST_withoutData()
    {
        $this->json('POST', '/sync');

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    /**
     * Test: POST sync with Invalid data
     *
     * Expected return: status code (500)
     * [Internal Server Error] - Trying to access an invalid object
     */
    public function testSync_POST_withInvalidData()
    {
        $input = [
            'fsq' => 'bla'
        ];

        $this->json('POST', '/sync', $input);

        $this->assertEquals(500, $this->response->getStatusCode());
    }

    //************************** Google Get Test *****************************//
    /**
     * Test: GET google data with Valid Data
     *
     * Expected return: status code (200)
     * [OK]
     */
    public function testGoogleData_GET_withData()
    {
        $responses = app('App\Http\Controllers\HomeController')->getStartingPins();
        $responses = json_decode($responses, true);

        $data = $responses["response"]["groups"][0]["items"][0]; // get foursquare first item search result

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input);

        $result = json_decode($this->response->getContent()); // get place_id from the method

        $name = $responses["response"]["groups"][0]["items"][0]["venue"]["name"];
        $address = $responses["response"]["groups"][0]["items"][0]["venue"]["location"]["formattedAddress"][0];

        $query = $name . ' ' . $address . ' review';
        $place_id = $result->place_id;

        $request = [
            'place_id' => $place_id,
            'query' => $query
        ];

        $this->json('GET', '/get/google', $request);

        $this->assertEquals(200, $this->response->getStatusCode());

    }

    /**
     * Test: GET google data without any Data
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testGoogleData_GET_withoutData()
    {
        $this->json('GET', '/get/google');

        $this->assertEquals(422, $this->response->getStatusCode());

    }

    /**
     * Test: GET google data with one Missing Data
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testGoogleData_GET_missingData()
    {
        $responses = app('App\Http\Controllers\HomeController')->getStartingPins();
        $responses = json_decode($responses, true);

        $data = $responses["response"]["groups"][0]["items"][0]; // get foursquare first item search result

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input);

        $result = json_decode($this->response->getContent()); // get place_id from the method

        $place_id = $result->place_id;

        $request = [
            'place_id' => $place_id
        ];

        $this->json('GET', '/get/google', $request);

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    /**
     * Test: GET google data with Invalid Data
     *
     * Expected return: status code (500)
     * [Unprocessable Entity] (invalid data)
     */
    public function testGoogleData_GET_withInvalidData()
    {
        $request = [
            'place_id' => 'bla',
            'query' => 'some random string'
        ];

        $this->json('GET', '/get/google', $request);

        $this->assertEquals(500, $this->response->getStatusCode());
    }

    //************************** Facebook Get Test *****************************//

    public function testFacebookData_GET_withData()
    {
        /*
         * var getAddress;
                    if(typeof data.venue.location.formattedAddress[1] == 'undefined') {
                        getAddress = data.venue.location.formattedAddress[0];
                    } else {
                        getAddress =  data.venue.location.formattedAddress[1];
                    }

                    // generate query
                    query = data.venue.name + ' ' + getAddress.replace(/[0-9]/g, '');
         */

        $data = $this->getValidFoursquareData();

        typeof($data);

    }

    //************************** Vote Test *****************************//
    /**
     * Test: Unauthorized POST to route vote with Invalid Request
     *
     * Expected return: status code (401)
     * [unauthorized error]
     */
    public function testPOSTvote_unauthorized_invalidRequest()
    {
        $this->json('POST', '/vote');

        $this->assertEquals(401, $this->response->getStatusCode());
    }

    /**
     * Test: Unauthorized POST to route vote with Valid Request
     *
     * Expected return: status code (401)
     * [unauthorized error]
     */
    public function testPOSTvote_unauthorized_validRequest()
    {
        $input = [
            'id' => 1,
            'type' => 'facebook',
            'vote_type' => 1
        ];

        $this->json('POST', '/vote', $input);

        $this->assertEquals(401, $this->response->getStatusCode());
    }

    /**
     * Test: Authorized POST to route vote with Invalid Request
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testPOSTvote_authorized_invalidRequest()
    {
        $user = \App\User::find(1);

        $this->actingAs($user)->json('POST', '/vote');

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    /**
     * Test: Authorized POST to route vote with Valid Request
     * - also test the correctness of the code
     */
    public function testPOSTvote_authorized_validRequest()
    {
        $user = \App\User::find(1);

        $input = [
            'id' => 1,
            'type' => 'facebook',
            'vote_type' => 1
        ];

        $this->actingAs($user)
            ->json('POST', '/vote', $input)
            ->seeJsonContains([
                'success' => true
            ]);

    }

    /**
     * Helper Function: GET dummy data for testing
     *
     * @return mixed
     */
    private function getValidFoursquareData()
    {
        $responses = app('App\Http\Controllers\HomeController')->getStartingPins();
        $responses = json_decode($responses, true);

        $data = $responses["response"]["groups"][0]["items"][0]; // get foursquare first item search result

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input);

        $result = json_decode($this->response->getContent()); // get place_id from the method

        return $result;
    }

}
