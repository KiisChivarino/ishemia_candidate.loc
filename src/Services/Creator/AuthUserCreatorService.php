<?php

namespace App\Services\Creator;

use App\Entity\AuthUser;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthUserCreatorService
 * @package App\Services\Creator
 */
class AuthUserCreatorService
{

    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';

    /** @var string Роль менеджера */
    private const MANAGER_ROLE = 'ROLE_MANAGER';

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * AuthUserCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param AuthUser $authUser
     * @return AuthUser
     */
    public function persistNewPatientAuthUser(AuthUser $authUser): AuthUser
    {
        $this->prepareNewPatientAuthUser($authUser);
        $this->entityManager->persist($authUser);
        return $authUser;
    }

    /**
     * @param AuthUser $authUser
     * @return AuthUser
     */
    public function persistNewManagerAuthUser(AuthUser $authUser): AuthUser
    {
        $this->prepareNewManagerAuthUser($authUser);
        $this->entityManager->persist($authUser);
        return $authUser;
    }

    /**
     * @return AuthUser
     */
    public function createAuthUser(): AuthUser
    {
        return (new AuthUser())->setEnabled(true);
    }

    /**
     * @param AuthUser $authUser
     * @return AuthUser
     */
    public function prepareNewPatientAuthUser(AuthUser $authUser): AuthUser
    {
        return $this->prepareAuthUser($authUser, self::PATIENT_ROLE);
    }

    /**
     * @param AuthUser $authUser
     * @return AuthUser
     */
    public function prepareNewManagerAuthUser(AuthUser $authUser): AuthUser
    {
        return $this->prepareAuthUser($authUser, self::MANAGER_ROLE);
    }

    /**
     * @param AuthUser $authUser
     * @param string $role
     * @return AuthUser
     */
    public function prepareAuthUser(AuthUser $authUser, string $role): AuthUser
    {
        return $authUser
            ->setEnabled(true)
            ->setPassword(
                $authUser->getPassword() ?
                    $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword()) :
                    $this->passwordEncoder->encodePassword($authUser, AuthUserInfoService::randomPassword())
            )
            ->setRoles($role)
            ->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
    }


    /**
     * Редактирует пользователя
     * @param AuthUser $authUser
     * @param string $oldPassword
     * @return AuthUser
     */
    public function updateAuthUser(AuthUser $authUser, string $oldPassword): AuthUser
    {
        $this->updatePassword($this->passwordEncoder, $authUser, $oldPassword);
        $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
        $authUser->setRoles($authUser->getRoles()[0]);
        $this->entityManager->persist($authUser);
        return $authUser;
    }

    /**
     * Устанавливает новый пароль пользователю, если он введен
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AuthUser $authUser
     * @param string $oldPassword
     */
    public static function updatePassword(
        UserPasswordEncoderInterface $passwordEncoder,
        AuthUser $authUser,
        string $oldPassword
    ): void
    {
        $newPassword = $authUser->getPassword();
        $authUser->setPassword($oldPassword);
        if ($newPassword !== null) {
            $encodedPassword = $passwordEncoder->encodePassword($authUser, $newPassword);
            if ($encodedPassword !== $oldPassword) {
                $authUser->setPassword($encodedPassword);
            }
        }
    }
}