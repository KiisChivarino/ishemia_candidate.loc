<?php

namespace App\Repository;

use App\Entity\ChannelType;
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
}
