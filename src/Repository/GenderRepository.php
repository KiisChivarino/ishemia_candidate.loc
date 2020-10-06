<?php

namespace App\Repository;

use App\Entity\Gender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gender|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gender|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gender[]    findAll()
 * @method Gender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gender::class);
    }

    /**
     * Adds gender from fixtures
     *
     * @param string $name
     * @param string $title
     *
     * @return Gender|null
     * @throws ORMException
     */
    public function addGenderFromFixtures(string $name, string $title): ?Gender
    {
        $gender = (new Gender())
            ->setName($name)
            ->setTitle($title);
        $this->_em->persist($gender);
        return $gender;
    }
}
