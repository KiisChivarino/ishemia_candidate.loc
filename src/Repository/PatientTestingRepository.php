<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanTesting;
use App\Services\InfoService\MedicalHistoryInfoService;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class PatientTestingRepository
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientTestingRepository extends AppRepository
{
    /** @var MedicalHistoryInfoService $medicalHistoryInfoService */
    private $medicalHistoryInfoService;

    /**
     * PatientTestingRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param MedicalHistoryInfoService $medicalHistoryInfoService
     */
    public function __construct(ManagerRegistry $registry, MedicalHistoryInfoService $medicalHistoryInfoService)
    {
        parent::__construct($registry, PatientTesting::class);
        $this->medicalHistoryInfoService = $medicalHistoryInfoService;
    }

    /**
     * Возвращает тесты для пациента по плану
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return array
     * @throws ORMException
     */
    public function persistPatientTestsByPlan(MedicalHistory $medicalHistory): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->_em->getRepository(PlanTesting::class)->getStandardPlanTesting() as $test) {
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
        return $this->medicalHistoryInfoService->getPlannedDate(
            new DateTime(),
            (int)$planTesting->getTimeRangeCount(),
            (int)$planTesting->getTimeRange()->getMultiplier(),
            $planTesting->getTimeRange()->getDateInterval()->getFormat()
        );
    }
}
