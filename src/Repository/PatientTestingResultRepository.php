<?php

namespace App\Repository;

use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientTestingResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTestingResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTestingResult[]    findAll()
 * @method PatientTestingResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientTestingResultRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientTestingResult::class);
    }

    /**
     * Returns enabled patient testing results by testing
     * @param PatientTesting $testing
     * @return PatientTestingResult[]
     */
    public function getEnabledTestingResults(PatientTesting $testing)
    {
        return $this->findBy(['patientTesting' => $testing, 'enabled' => true]);
    }

    /**
     * Get not enabled patient testing results
     * @param PatientTesting $testing
     * @return PatientTestingResult[]
     */
    public function getNotEnabledTestingResults(PatientTesting $testing){
        return $this->findBy(['patientTesting' => $testing, 'enabled' => false]);
    }
}
