<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class RegionRepository
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class RegionRepository extends AppRepository
{
    /** @var int OKTMO_REGION_ID */
    public const OKTMO_REGION_ID = 38;

    /**
     * RegionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    /**
     * Get region Kursk
     * @return Region|null
     */
    public function getKurskRegion(): ?Region
    {
        return $this->findOneBy(['oktmoRegionId' => self::OKTMO_REGION_ID]);
    }
}
