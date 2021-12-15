<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\AuthUser;
use App\Services\EntityActions\Core\AbstractCreatorService;
use App\Services\EntityActions\Core\EntityActionsInterface;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthUserCreatorService
 * @package App\Services\EntityActions\Creator
 */
class AuthUserCreatorService extends AbstractCreatorService
{
    /** @var string Роль */
    public const ROLE_OPTION = 'role';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * AuthUserCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($entityManager, AuthUser::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param array $options
     * @return EntityActionsInterface
     * @throws Exception
     */
    public function before(array $options = []): EntityActionsInterface
    {
        parent::before($options);
        $authUser = $this->getEntity();
        $authUser->setRoles($this->options[self::ROLE_OPTION]);
        return $this;
    }

    protected function prepare(): void
    {
        parent::prepare();
        $authUser = $this->getEntity();
        $authUser
            ->setPassword(
                $this->passwordEncoder->encodePassword(
                    $authUser,
                    $authUser->getPassword() ?: AuthUserInfoService::randomPassword()
                )
            )
            ->setRoles($this->options[self::ROLE_OPTION])
            ->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck('string', self::ROLE_OPTION);
    }
}
