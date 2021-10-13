<?php

namespace App\Tests;

use Exception;

/**
 * @method getModule(string $string)
 */
class LoginCest
{
    /**
     * Checking authorization of the administrator
     * @param \App\Tests\FunctionalTester $I
     * @throws Exception
     */
    public function authorizationOfAdministrator(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);

        $I->submitForm('#authForm', [
            'phone' => "8888888888",
            'password' => '111111',
        ], 'submitButton');

        $I->seeAuthentication();
        $I->amOnPage('/admin');
        $I->seeResponseCodeIs(200);
        $I->logout();
    }

    /**
     * Checking authorization of a doctor-consultant
     * @param \App\Tests\FunctionalTester $I
     * @throws Exception
     */
    public function authorizationOfAConsultantDoctor(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);

        $I->submitForm('#authForm', [
            'phone' => "0000000000",
            'password' => '111111',
        ], 'submitButton');
        $I->seeAuthentication();
        $I->amOnPage('/doctor_office/patients');
        $I->seeResponseCodeIs(200);
        $I->logout();
    }

    /**
     * Checking authorization of the LPU doctor
     * @param \App\Tests\FunctionalTester $I
     * @throws Exception
     */
    public function authorizationOfALpuDoctor(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);

        $I->submitForm('#authForm', [
            'phone' => "5555555555",
            'password' => '111111',
        ], 'submitButton');

        $I->seeAuthentication();
        $I->amOnPage('/doctor_office/patients');
        $I->seeResponseCodeIs(200);
        $I->logout();
    }

    /**
     * Checking authorization of the patient
     * @param \App\Tests\FunctionalTester $I
     * @throws Exception
     */
    public function authorizationOfAPatient(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);

        $I->submitForm('#authForm', [
            'phone' => "6666666666",
            'password' => '111111',
        ], 'submitButton');

        $I->seeAuthentication();
        $I->amOnPage('/patient_office');
        $I->seeResponseCodeIs(200);
        $I->logout();
    }
}
