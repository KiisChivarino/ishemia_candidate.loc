<?php

namespace App\Services\InfoService;

use App\Entity\AuthUser;
use App\Entity\Role;

/**
 * Class AuthUserInfoService
 * @package App\Services\InfoService
 */
class AuthUserInfoService
{

    /**
     * ФИО пользователя
     *
     * @param AuthUser $authUser
     * @param bool $initials
     *
     * @return string
     */
    static public function getFIO(AuthUser $authUser, bool $initials = false): string
    {
        $firstName = '';
        $patronymicName = '';
        $lastName = '';
        if ($authUser->getLastName()) {
            $lastName .= $authUser->getLastName() . ' ';
        }
        if ($authUser->getFirstName()) {
            $firstName .= ($initials ? mb_strtoupper(mb_substr($authUser->getFirstName(), 0, 1))
                    . '.' : $authUser->getFirstName()) . ' ';
        }
        if ($authUser->getPatronymicName()) {
            $patronymicName = $initials ? mb_strtoupper(mb_substr($authUser->getPatronymicName(), 0, 1))
                . '.' : $authUser->getPatronymicName();
        }
        return $lastName . $firstName . $patronymicName;
    }

    /**
     * Возвращает роли пользователя строкой через запятую
     *
     * @param Role[] $roles
     * @param bool $techName
     *
     * @return string
     */
    static public function getRoleNames(array $roles, bool $techName = false): string
    {
        $rolesNames = [];
        foreach ($roles as $role) {
            $rolesNames[] = $techName ? $role->getTechName() : $role->getName();
        }
        return implode($rolesNames, ',');
    }

    /**
     * Return user phone cleared from mask string
     *
     * @param string $phone
     *
     * @return string
     */
    static public function clearUserPhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', ltrim($phone, '+7'));
    }

    /**
     * Return user phone for layout
     *
     * @param AuthUser $authUser
     *
     * @return string
     */
    static public function getPhone(AuthUser $authUser): string
    {
        $phone = $authUser->getPhone();
        return
            '+7 (' . substr($phone, 0, 3) . ') '
            . substr($phone, 3, 3)
            . '-' . substr($phone, 6, 4);
    }

    /**
     * @return string
     */
    static public function randomPassword(): string
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789(_).,!$%^&*+-=";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Checks if user is ROLE_DOCTOR_CONSULTANT
     * @param AuthUser $authUser
     * @return bool
     */
    public function isDoctorConsultant(AuthUser $authUser): bool
    {
        return in_array('ROLE_DOCTOR_CONSULTANT', $authUser->getRoles());
    }


    /**
     * Checks if user is ROLE_DOCTOR_HOSPITAL
     * @param AuthUser $authUser
     * @return bool
     */
    public function isDoctorHospital(AuthUser $authUser): bool
    {
        return in_array('ROLE_DOCTOR_HOSPITAL', $authUser->getRoles());
    }

    /**
     * Check if user is doctor
     * @param AuthUser $authUser
     * @return bool
     */
    public static function isDoctor(AuthUser  $authUser): bool
    {
        return
            in_array('ROLE_DOCTOR_HOSPITAL', $authUser->getRoles())
            ||
            in_array('ROLE_DOCTOR_CONSULTANT', $authUser->getRoles());
    }
}