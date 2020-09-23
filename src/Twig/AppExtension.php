<?php

namespace App\Twig;

use App\Entity\AuthUser;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AppExtension
 *
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    protected $entityManager;

    /**
     * AppExtension constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'fio', [
                    $this,
                    'getFio'
                ]
            ),
            new TwigFunction(
                'roleTitle', [
                    $this,
                    'getRoleTitle'
                ]
            ),
        ];
    }

    /**
     * Returns name of user
     *
     * @param AuthUser $authUser
     * @param bool $initials
     *
     * @return string
     */
    public function getFio(AuthUser $authUser, bool $initials = false): string
    {
        return (new AuthUserInfoService())->getFIO($authUser, $initials);
    }

    /**
     * Returns title of role
     *
     * @param AuthUser $authUser
     *
     * @return string
     */
    public function getRoleTitle(AuthUser $authUser): string
    {
        return (!in_array('ROLE_DEVELOPER', $authUser->getRoles())) ?
            (new AuthUserInfoService())->getRoleNames($this->entityManager->getRepository(AuthUser::class)->getRoles($authUser)) : '';
    }
}