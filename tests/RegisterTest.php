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
            ->see('PolyReviews Register');
    }

    /**
     * Test: Submit register to server and redirect to main page
     */
    public function testPostRegister()
    {
        $this->visit('/register')
            ->type('Tester Name', 'name')
            ->type('tester@gmail.com', 'email')
            ->type('secret1234', 'password')
            ->type('secret1234', 'password_confirmation')
            ->press('Register')
            ->seePageIs('/');

        $tester = App\User::where('email', 'tester@gmail.com')->first();

        $tester->delete();

    }

    /* Validation testing */

    /**
     * Test: Incorrect name length return false
     */
    public function testIncorrectNameLength()
    {
        $this->visit('/register')
            ->type('ba', 'name')
            ->type('tester@gmail.com', 'email')
            ->type('secret1234', 'password')
            ->type('secret1234', 'password_confirmation')
            ->press('Register')
            ->see('The name must be at least 3 characters');
    }

    /**
     * Test: Incorrect password length return false
     */
    public function testIncorrectPasswordLength()
    {
        $this->visit('/register')
            ->type('blaa', 'name')
            ->type('tester@gmail.com', 'email')
            ->type('secret', 'password')
            ->type('secret', 'password_confirmation')
            ->press('Register')
            ->see('The password must be at least 8 characters');
    }

    /**
     * Test: Incorrect confirmation password return false
     */
    public function testIncorrectConfirmationPassword()
    {
        $this->visit('/register')
            ->type('blaa', 'name')
            ->type('tester@gmail.com', 'email')
            ->type('secret1234', 'password')
            ->type('secret4321', 'password_confirmation')
            ->press('Register')
            ->see('The password confirmation does not match.');
    }

    /**
     * Test: Email has been taken
     */
    public function testEmailHasTaken()
    {
        $this->visit('/register')
            ->type('blaa', 'name')
            ->type('homestead@gmail.com', 'email')
            ->type('secret1234', 'password')
            ->type('secret1234', 'password_confirmation')
            ->press('Register')
            ->see('The email has already been taken.');
    }
}
