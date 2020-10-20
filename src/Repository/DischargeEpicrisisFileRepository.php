<?php

namespace App\Repository;

use App\Entity\DischargeEpicrisisFile;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class DischargeEpicrisisFileRepository
 * @method DischargeEpicrisisFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method DischargeEpicrisisFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method DischargeEpicrisisFile[]    findAll()
 * @method DischargeEpicrisisFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class DischargeEpicrisisFileRepository extends AppRepository
{
    /**
     * DischargeEpicrisisFileRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DischargeEpicrisisFile::class);
    }
}
