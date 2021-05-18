<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\AuthUser;
use App\Services\EntityActions\Core\AbstractEditorService;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthUserEditorService
 * @package App\Services\EntityActions\Editor
 */
class AuthUserEditorService extends AbstractEditorService
{
    /** @var string Old password option for saving if empty input form password*/
    public const OLD_PASSWORD_OPTION = 'oldPassword';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * AuthUserEditorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param $entity
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        $entity,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        parent::__construct($entityManager, $entity);
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function prepare(): void
    {
        /** @var AuthUser $authUser */
        $authUser = $this->getEntity();
        self::updatePassword($this->passwordEncoder, $authUser, $this->options[self::OLD_PASSWORD_OPTION]);
        $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck('string', self::OLD_PASSWORD_OPTION);
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