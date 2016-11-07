<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataTest extends TestCase
{
    /**
     * Test: Unauthorized POST to route vote.
     */
    public function testPOSTvote_unauthorized()
    {
        $response = $this->call('POST', '/vote');

        $this->assertEquals(302, $response->status()); // return 302 as rejected
    }

    public function testPOSTvote_authorized()
    {
        $user = \App\User::where('email', 'homestead@gmail.com')->first();

        $response = $this->actingAs($user)->call('POST', '/vote');

        $this->assertEquals(200, $response->status());

    }
}
