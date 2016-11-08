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
        $data = $this->getValidFoursquareData();

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
        $data = $this->getValidFoursquareData();

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input);

        $result = json_decode($this->response->getContent()); // get place_id from the method

        $name = $data["venue"]["name"];
        $address = $data["venue"]["location"]["formattedAddress"][0];

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
    public function testGoogleData_GET_withMissingData()
    {

        $data = $this->getValidFoursquareData();

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
     * [Internal Server Error] (code is not correct)
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

    /**
     * Test: GET facebook data with Valid data request
     *
     * Expected return: status code(200)
     * [OK]
     */
    public function testFacebookData_GET_withData()
    {

        $data = $this->getValidFoursquareData();

        if(gettype($data["venue"]["location"]["formattedAddress"][1]) == NULL){
            $getAddress = $data["venue"]["location"]["formattedAddress"][0];
        } else {
            $getAddress = $data["venue"]["location"]["formattedAddress"][1];
        }

        $query = $data["venue"]["name"] . ' ' . preg_replace('/[0-9]/', '', $getAddress);

        $input = [
            'fsq' => $data
        ];

        $this->json('POST','/sync', $input);

        $result = json_decode($this->response->getContent()); // get place_id from the method

        $place_id = $result->place_id;

        $request = [
            'place_id' => $place_id,
            'query' => $query
        ];

        $this->json('GET', '/get/facebook', $request);

        $this->assertEquals(200, $this->response->getStatusCode());

    }

    /**
     * Test: GET facebook data without any Data Request
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testFacebookData_GET_withoutData()
    {
        $this->json('GET', '/get/facebook');

        $this->assertEquals(422, $this->response->getStatusCode());
    }

    /**
     * Test: GET facebook data with one Missing Data Request
     *
     * Expected return: status code (422)
     * [Unprocessable Entity] (invalid data)
     */
    public function testFacebookData_GET_withMissingData()
    {
        $data = $this->getValidFoursquareData();

        if(gettype($data["venue"]["location"]["formattedAddress"][1]) == NULL){
            $getAddress = $data["venue"]["location"]["formattedAddress"][0];
        } else {
            $getAddress = $data["venue"]["location"]["formattedAddress"][1];
        }

        $query = $data["venue"]["name"] . ' ' . preg_replace('/[0-9]/', '', $getAddress);

        $request = [
            'query' => $query
        ];

        $this->json('GET', '/get/facebook', $request);

        $this->assertEquals(422, $this->response->getStatusCode());

    }

    /**
     * Test: GET facebook data with Invalid Data Request
     *
     * Expected return: empty array (string type)
     */
    public function testFacebookData_GET_invalidData()
    {
        $input = [
            'place_id' => 'string type invalid',
            'query' => 'some random query'
        ];

        $this->json('GET', '/get/facebook', $input);

        $this->assertTrue($this->response->getContent() == "[]");
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


        return $data;
    }

}
