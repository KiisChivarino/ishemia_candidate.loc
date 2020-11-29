<?php

namespace App\Twig;

use App\Entity\AnalysisRate;
use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AppExtension
 *
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    protected $entityManager;

    /**
     * AppExtension constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'fio', [
                    $this,
                    'getFio'
                ]
            ),
            new TwigFunction(
                'roleTitle', [
                    $this,
                    'getRoleTitle'
                ]
            ),
            new TwigFunction(
                'phone', [
                    $this,
                    'getPhone',
                ]
            ),
            new TwigFunction(
                'bodyMassIndex', [
                    $this,
                    'getBodyMassIndex',
                ]
            ),
            new TwigFunction(
                'patientTestingTitle', [
                    $this,
                    'getPatientTestingTitle',
                ]
            ),
            new TwigFunction(
                'analysisRate', [
                    $this,
                    'getAnalysisRate',
                ]
            ),
            new TwigFunction(
                'enabledTestingResults', [
                    $this,
                    'getEnabledTestingResults',
                ]
            )
        ];
    }

    /**
     * Returns name of user
     *
     * @param AuthUser|null $authUser
     * @param bool $initials
     *
     * @return string
     */
    public function getFio(?AuthUser $authUser, bool $initials = false): string
    {
        return $authUser ? (new AuthUserInfoService())->getFIO($authUser, $initials) : '';
    }

    /**
     * Returns title of role
     *
     * @param AuthUser|null $authUser
     *
     * @return string
     */
    public function getRoleTitle(?AuthUser $authUser): string
    {
        return ($authUser) ?
            (new AuthUserInfoService())->getRoleNames($this->entityManager->getRepository(AuthUser::class)
                ->getRoles($authUser)) : '';
    }

    /**
     * Returns phone of user
     *
     * @param AuthUser $authUser
     *
     * @return string
     */
    public function getPhone(AuthUser $authUser): string
    {
        return (new AuthUserInfoService())->getPhone($authUser);
    }

    /**
     * Returns body mass index
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getBodyMassIndex(Patient $patient): string
    {
        return (new PatientInfoService())->getBodyMassIndex($patient);
    }

    /**
     * Returns patient testing title
     * @param PatientTesting $patientTesting
     * @return string
     */
    public function getPatientTestingTitle(PatientTesting $patientTesting): string
    {
        return (new PatientTestingInfoService())->getPatientTestingInfoString($patientTesting);
    }

    /**
     * @param AnalysisRate $analysisRate
     * @return string
     */
    public function getAnalysisRate(AnalysisRate $analysisRate): string
    {
        return (new AnalysisRateInfoService())->getAnalysisRateInfoString($analysisRate);
    }

    /**
     * @param PatientTesting $testing
     * @return array
     */
    public function getEnabledTestingResults(PatientTesting $testing): array
    {
        return $this->entityManager->getRepository(PatientTestingResult::class)->getEnabledTestingResults($testing);
    }
}