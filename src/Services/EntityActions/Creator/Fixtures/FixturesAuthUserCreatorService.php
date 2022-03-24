<?php

namespace App\Services\EntityActions\Creator\Fixtures;

use App\Entity\AuthUser;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class FixturesAuthUserCreatorService
 * @package App\Services\EntityActions\Creator
 */
class FixturesAuthUserCreatorService extends AuthUserCreatorService
{
    public const
        PASSWORD_OPTION = 'password',
        FIRST_NAME_OPTION = 'firstName',
        LAST_NAME_OPTION = 'lastName',
        PHONE_OPTION = 'phone';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * FixturesAuthUserCreatorService constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct($entityManager, $passwordEncoder);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Actions with entity before persist
     */
    protected function prepare(): void
    {
        /** @var AuthUser $authUser */
        $authUser = $this->getEntity();
        $authUser
            ->setPassword(
                $this->passwordEncoder->encodePassword(
                    $authUser,
                    $this->options[self::PASSWORD_OPTION]
                )
            )
            ->setFirstName($this->options[self::FIRST_NAME_OPTION])
            ->setLastName($this->options[self::LAST_NAME_OPTION])
            ->setPhone($this->options[self::PHONE_OPTION])
            ->setRoles($this->options[self::ROLE_OPTION])
            ->setEnabled(true);
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck('string', self::PASSWORD_OPTION);
        $this->addOptionCheck('string', self::FIRST_NAME_OPTION);
        $this->addOptionCheck('string', self::LAST_NAME_OPTION);
        $this->addOptionCheck('string', self::PHONE_OPTION);
    }
}