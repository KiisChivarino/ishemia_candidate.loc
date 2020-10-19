<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanTesting;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientTestingRepository extends AppRepository
{
    /**
     * PatientTestingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientTesting::class);
    }

    /**
     * Возвращает тесты для пациента по плану
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return array
     * @throws ORMException
     */
    public function persistPatientTests(MedicalHistory $medicalHistory): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->_em->getRepository(PlanTesting::class)->getStandardPlanTesting() as $test) {
            if ($test->getEnabled() && $test->getTimeRangeCount() > 0) {
                $patientTest = new PatientTesting();
                $patientTest->setMedicalHistory($medicalHistory);
                $patientTest->setAnalysisGroup($test->getAnalysisGroup());
                $patientTest->setProcessed(false);
                $patientTest->setEnabled(true);
                $patientTest->setAnalysisDate(null);
                $patientTest->setPlannedDate($this->getPlannedDate($test));
                $this->_em->persist($patientTest);
                $patientTests[] = $patientTest;
                $this->_em->getRepository(PatientTestingResult::class)->persistTestingResultsForTesting($patientTest);
            }
        }
        return $patientTests;
    }

    /**
     * Get testing planned date
     *
     * @param PlanTesting $planTesting
     *
     * @return DateTimeInterface|null
     * @throws Exception
     */
    public function getPlannedDate(PlanTesting $planTesting): ?DateTimeInterface
    {
        $currDate = new DateTime();
        return $currDate->add(
            new DateInterval(
                'P'.
                (string)((int)$planTesting->getTimeRangeCount() * (int)$planTesting->getTimeRange()->getMultiplier()).
                $planTesting->getTimeRange()->getDateInterval()->getFormat()
            )
        );
    }
}
