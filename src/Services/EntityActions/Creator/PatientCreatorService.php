<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PatientCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientCreatorService extends AbstractCreatorService
{
    /** @var string Auth user option */
    public const AUTH_USER_OPTION = 'authUser';

    /**
     * PatientCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Patient::class);
    }

    protected function create(): void
    {
        parent::create();

        /** @var Patient $entity */
        $entity = $this->getEntity();
        $entity->setAuthUser($this->options[self::AUTH_USER_OPTION]);
    }

    protected function prepare(): void
    {
        $this->getEntity()
            ->setSmsInforming(true)
            ->setEmailInforming(true);
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(AuthUser::class, self::AUTH_USER_OPTION);
    }
}
