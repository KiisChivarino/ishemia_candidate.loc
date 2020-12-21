<?php

namespace App\Repository;

use App\Entity\LPU;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class LPURepository
 * @method LPU|null find($id, $lockMode = null, $lockVersion = null)
 * @method LPU|null findOneBy(array $criteria, array $orderBy = null)
 * @method LPU[]    findAll()
 * @method LPU[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class LPURepository extends AppRepository
{
    /**
     * LPURepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LPU::class);
    }

    /**
     * Get hospitals of Kursk region
     * @return LPU[]
     */
    public function getKurskRegionLPU(): array
    {
        return $this->findBy(['oktmoRegionId' => 38]);
    }
}
