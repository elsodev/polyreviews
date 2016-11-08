<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeTest extends TestCase
{
    //************************** Route Test *****************************//
    /**
     * Test: visit the home page with url('/')
     */
    public function testHomePage_GET1()
    {
        $this->visit('/')
            ->see('POLYREVIEWS');
    }

    /**
     * Test: visit the home page with url('/home')
     */
    public function testHomePage_GET2()
    {
        $this->visit('/home')
            ->see('POLYREVIEWS');
    }

    //************************** Start Pin Test *****************************//
    /**
     * Test: Get starting Pin
     */
    public function testGETStartingPin()
    {
        // foursquare api return meta with code 200 if the response is valid
        $this->get('/get/start')
            ->see("\"meta\":{\"code\":200")
            ->dontSee("\"meta\":{\"code\":400"); // parameter error return code 400
    }

    //************************** Location Test *****************************//
    /**
     * Test: Get location without passing latitude and longitude argument
     * - without any input request
     */
    public function testChangeLocationFail()
    {
        $response = $this->call('GET', '/get/loc');

        $this->assertEquals(302, $response->status()); // 302 - rejected
    }

    /**
     * Test: Get location with only latitude request field
     * - without longitude
     */
    public function testChangeLocationFail_withOnlyLAT()
    {
        $input = [
            'lat' => config('app.locations.default_center.lat')
        ];

        $response = $this->call('GET', '/get/loc', $input);

        $this->assertEquals(302, $response->status());
    }

    /**
     * Test: Get location with only longitude request field
     * - without latitude
     */
    public function testChangeLocationFail_withOnlyLNG()
    {
        $input = [
            'lng' => config('app.locations.default_center.lng')
        ];

        $response = $this->call('GET', '/get/loc', $input);

        $this->assertEquals(302, $response->status());
    }


    /**
     * Test: Get location with required request field
     */
    public function testChangeLocationSuccess()
    {
        $input = [
            'lat' => config('app.locations.default_center.lat'),
            'lng' => config('app.locations.default_center.lng')
        ];

        $response = $this->call('GET', '/get/loc', $input);

        $this->assertEquals(200, $response->status()); // 200 - success
    }

    //************************** Single Location Test *****************************//
    public function testSingleLocation_GET_withData()
    {

    }
}
