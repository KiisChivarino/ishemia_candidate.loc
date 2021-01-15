<?php

namespace App\Repository;

use App\Entity\ChannelType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChannelType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChannelType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChannelType[]    findAll()
 * @method ChannelType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelTypeRepository extends AppRepository
{
    /**
     * ChannelTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChannelType::class);
    }

    /**
     * Ищет тип канала по его имени
     * @param string $name
     * @return ChannelType|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?ChannelType
    {
        return $this->createQueryBuilder('ct')
            ->andWhere('ct.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
