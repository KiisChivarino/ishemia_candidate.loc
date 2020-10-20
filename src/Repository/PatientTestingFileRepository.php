<?php

namespace App\Repository;

use App\Entity\PatientTestingFile;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientTestingFileRepository
 * @method PatientTestingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTestingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTestingFile[]    findAll()
 * @method PatientTestingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientTestingFileRepository extends AppRepository
{
    /**
     * PatientTestingFileRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientTestingFile::class);
    }
}
