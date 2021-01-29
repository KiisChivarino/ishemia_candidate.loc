<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class NotificationRepository
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class NotificationRepository extends AppRepository
{
    /**
     * NotificationRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Finds last group notification
     * @return Notification|null
     * @throws NonUniqueResultException
     */
    public function findLastGroup(): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.groupId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
