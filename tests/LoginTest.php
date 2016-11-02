<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    /**
     * Test: Get page
     */
    public function testLoginPage()
    {
        $this->visit('/login')
            ->see('Polyreview Login');
    }

    /**
     * Test: User type in correct credential and press log in
     */
    public function testPostLoginSuccess()
    {
        $this->visit('/login')
            ->type('homestead@gmail.com', 'email')
            ->type('secret', 'password')
            ->check('remember')
            ->press('Login')
            ->seePageIs('/');
    }

    /**
     * Test: User type in wrong credential and press log in
     */
    public function testPostLoginFail()
    {
        $this->visit('/login')
            ->type('bla@gmail.com', 'email')
            ->type('bla', 'password')
            ->press('Login')
            ->seePageIs('/login')
            ->see('warning message');
    }

    /**
     * Test: User click register link
     */
    public function testClickRegister()
    {
        $this->visit('/login')
            ->click('Register an Account')
            ->seePageIs('/register');
    }
}
