<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Diagnosis;
use App\Repository\DiagnosisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class CreatorService
 * @package App\Services\EntityActions\Creator
 */
class DiagnosisCreatorService
{
    /** @var string The code of user diagnosis */
    protected const USER_DIAGNOSIS_CODE = 'userDiagnosis';

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /**
     * DiagnosisCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function createDiagnosis(): Diagnosis
    {
        return (new Diagnosis())
            ->setEnabled(true);
    }

    /**
     * Persists diagnosis by name
     * @param string $diagnosisName
     * @return Diagnosis
     * @throws NonUniqueResultException
     */
    public function persistDiagnosis(string $diagnosisName): Diagnosis
    {
        /** @var DiagnosisRepository $diagnosisRepository */
        $diagnosisRepository = $this->entityManager->getRepository(Diagnosis::class);
        $diagnosis = $diagnosisRepository->findDiagnosisByNameAndCode($diagnosisName, self::USER_DIAGNOSIS_CODE);
        if ($diagnosis === null) {
            $diagnosis = $this->createDiagnosis()
                ->setName($diagnosisName)
                ->setCode(self::USER_DIAGNOSIS_CODE);
            $this->entityManager->persist($diagnosis);
            $this->entityManager->flush();

        }
        return $diagnosis;
    }
}