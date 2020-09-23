<?php

namespace App\Repository;

use App\Entity\BlogItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogItem[]    findAll()
 * @method BlogItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogItem::class);
    }
}
