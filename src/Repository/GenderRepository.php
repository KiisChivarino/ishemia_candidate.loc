<?php

namespace App\Repository;

use App\Entity\Gender;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GenderRepository
 * @method Gender|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gender|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gender[]    findAll()
 * @method Gender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenderRepository extends AppRepository
{
    /** @var int male id */
    public const MALE_GENDER_ID = 1;

    /** @var int female id */
    public const FEMALE_GENDER_ID = 2;

    /** @var int lack of gender id */
    public const LACK_OF_GENDER_ID = 3;

    /**
     * GenderRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gender::class);
    }

    /**
     * Adds gender from fixtures
     *
     * @param string $name
     * @param string $title
     * @param int    $id
     *
     * @return Gender|null
     * @throws ORMException
     */
    public function addGenderFromFixtures(string $name, string $title, int $id): ?Gender
    {
        $gender = (new Gender())
            ->setId($id)
            ->setName($name)
            ->setTitle($title);
        $this->_em->persist($gender);

        return $gender;
    }
}
