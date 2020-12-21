<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CountryRepository
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class CountryRepository extends AppRepository
{
    /**
     * CountryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @return Country|null
     */
    public function getRussiaCountry(): ?Country
    {
        return $this->findOneBy(['shortcode' => 'RUS']);
    }
}
