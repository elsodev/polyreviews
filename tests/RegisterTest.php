<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
    /**
     * Test: Get register page
     */
    public function testRegisterPage()
    {
        $this->visit('/register')
            ->see('Polyreview Register');
    }

    public function 
}
