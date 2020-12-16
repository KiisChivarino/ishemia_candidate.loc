<?php

namespace App\Services\InfoService;

use App\Entity\AuthUser;
use App\Entity\Role;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * Устанавливает новый пароль пользователю, если он введен
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AuthUser $authUser
     * @param string $oldPassword
     */
    static public function updatePassword(
        UserPasswordEncoderInterface $passwordEncoder,
        AuthUser $authUser,
        string $oldPassword
    ): void
    {
        $newPassword = $authUser->getPassword();
        $authUser->setPassword($oldPassword);
        if ($newPassword) {

            // See https://symfony.com/doc/current/security.html#c-encoding-passwords
            $encodedPassword = $passwordEncoder->encodePassword($authUser, $newPassword);
            if ($encodedPassword !== $oldPassword) {
                $authUser->setPassword($newPassword);
            }
        }
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
    static public function clearUserPhone(string $phone)
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
    static public function getPhone(AuthUser $authUser)
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
    static public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789(_).,!$%^&*+-=";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}