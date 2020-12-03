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
    public function persistAuthUser(AuthUser $authUser): AuthUser
    {
        $this->prepareAuthUser($authUser);
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
    public function prepareAuthUser(AuthUser $authUser): AuthUser
    {
        return $authUser
            ->setEnabled(true)
            ->setPassword(
                $authUser->getPassword() ?
                    $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword()) :
                    $this->passwordEncoder->encodePassword($authUser, AuthUserInfoService::randomPassword())
            )
            ->setRoles(self::PATIENT_ROLE)
            ->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
    }
}