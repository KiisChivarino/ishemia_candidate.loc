<?php

namespace App\Repository;

use App\Entity\NotificationTemplate;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class NotificationTemplateRepository
 * @method NotificationTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTemplate[]    findAll()
 * @method NotificationTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class NotificationTemplateRepository extends AppRepository
{
    /**
     * NotificationTemplateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplate::class);
    }

    /**
     * Ищет шаблон по его имени
     * @param string $name
     * @return NotificationTemplate|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?NotificationTemplate {
        return $this->createQueryBuilder('nt')
            ->andWhere('nt.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
