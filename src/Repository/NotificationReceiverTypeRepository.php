<?php

namespace App\Repository;

use App\Entity\NotificationReceiverType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationReceiverType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationReceiverType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationReceiverType[]    findAll()
 * @method NotificationReceiverType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationReceiverTypeRepository extends AppRepository
{
    /**
     * NotificationReceiverTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationReceiverType::class);
    }

    /**
     * Ищет тип получателя по его имени
     * @param string $name
     * @return NotificationReceiverType|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?NotificationReceiverType {
        return $this->createQueryBuilder('n')
            ->andWhere('n.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
