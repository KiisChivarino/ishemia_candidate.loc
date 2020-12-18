<?php

namespace App\Repository;

use App\Entity\OKSM;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class OKSMRepository
 * @method OKSM|null find($id, $lockMode = null, $lockVersion = null)
 * @method OKSM|null findOneBy(array $criteria, array $orderBy = null)
 * @method OKSM[]    findAll()
 * @method OKSM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class OKSMRepository extends AppRepository
{
    /**
     * OKSMRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OKSM::class);
    }

    /**
     * @return OKSM[]
     */
    public function getRussiaCountry(): array
    {
        return $this->findBy(['A3' => 'RUS']);
    }
}
