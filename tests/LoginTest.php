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
        // uncomment this for the first time, to create the first login
        /*
        factory(App\User::class)->create([
            'name' => 'kcrene',
            'email' => 'cwxorochi@gmail.com',
            'password' => bcrypt('kurtcwx1995')
        ]);
        */

        $this->visit('/login')
            ->type('cwxorochi@gmail.com', 'email')
            ->type('kurtcwx1995', 'password')
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
            ->type('secret', 'password')
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
