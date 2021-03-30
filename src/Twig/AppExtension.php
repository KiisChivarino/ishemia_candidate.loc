<?php

namespace App\Twig;

use App\Entity\AnalysisRate;
use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanTesting;
use App\Entity\PrescriptionTesting;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\InfoService\PlanTestingInfoService;
use App\Services\InfoService\PrescriptionTestingInfoService;
use App\Services\MultiFormService\MultiFormService;
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
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var */
    private $defaultTimeFormats;

    /** @var array */
    private $projectInfo;

    /**
     * AppExtension constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param $projectInfo
     * @param $defaultTimeFormats
     */
    public function __construct(EntityManagerInterface $entityManager, $projectInfo, $defaultTimeFormats)
    {
        $this->entityManager = $entityManager;
        $this->defaultTimeFormats = $defaultTimeFormats;
        $this->projectInfo = $projectInfo;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
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
            ),
            new TwigFunction(
                'planTestingTitle', [
                    $this,
                    'getPlanTestingTitle',
                ]
            ),
            new TwigFunction(
                'prescriptionTestingTitle', [
                    $this,
                    'getPrescriptionTestingTitle'
                ]
            ),
            new TwigFunction(
                'analysisRateTitle', [
                    $this,
                    'getAnalysisRateTitle'
                ]
            ),
            new TwigFunction(
                'isEmptyPatientTestingResults', [
                    $this,
                    'isEmptyPatientTestingResults'
                ]
            ),
            new TwigFunction(
                'isPatientTestingResultsExists', [
                    $this,
                    'isPatientTestingResultsExists'
                ]
            ),
             new TwigFunction(
                 'globals', [
                     $this,
                     'globals'
                 ]
             ),
            new TwigFunction(
                'formName', [
                    $this,
                    'getFormName'
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
     *
     * @param PatientTesting $patientTesting
     *
     * @return string
     */
    public function getPatientTestingTitle(PatientTesting $patientTesting): string
    {
        return (new PatientTestingInfoService())->getPatientTestingInfoString($patientTesting);
    }

    /**
     * Returns a string describing the unit of reference values
     *
     * @param AnalysisRate $analysisRate
     *
     * @return string
     */
    public function getAnalysisRate(AnalysisRate $analysisRate): string
    {
        return (new AnalysisRateInfoService())->getAnalysisRateInfoString($analysisRate);
    }

    /**
     * Returns enabled patient testing results by testing
     *
     * @param PatientTesting $testing
     *
     * @return array
     */
    public function getEnabledTestingResults(PatientTesting $testing): array
    {
        return $this->entityManager->getRepository(PatientTestingResult::class)->getEnabledTestingResults($testing);
    }

    /**
     * Returns info string of plan testing
     *
     * @param PlanTesting $planTesting
     *
     * @return string
     */
    public function getPlanTestingTitle(PlanTesting $planTesting): string
    {
        return PlanTestingInfoService::getPlanTestingInfoString($planTesting);
    }

    /**
     * Get prescription testing info string
     *
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return string
     */
    public function getPrescriptionTestingTitle(PrescriptionTesting $prescriptionTesting): string
    {
        return PrescriptionTestingInfoService::getPrescriptionTestingTitle($prescriptionTesting);
    }

    /**
     * Get analysis rate info string
     *
     * @param AnalysisRate $analysisRate
     *
     * @return string
     */
    public function getAnalysisRateTitle(AnalysisRate $analysisRate): string
    {
        return AnalysisRateInfoService::getAnalysisRateInfoString($analysisRate);
    }

    /**
     * Check for empty all patient testing results
     *
     * @param PatientTesting $patientTesting
     *
     * @return bool
     */
    public function isEmptyPatientTestingResults(PatientTesting $patientTesting): bool
    {
        return PatientTestingInfoService::isEmptyPatientTestingResults($patientTesting);
    }

    /**
     * Check for patient testing results exists
     * @param PatientTesting $patientTesting
     * @return bool
     */
    public function isPatientTestingResultsExists(PatientTesting $patientTesting): bool
    {
        return PatientTestingInfoService::isPatientTestingResultsExists($patientTesting);
    }

    /**
     * Returns Global parameters
     *
     * @return array
     */
    public function globals(): array
    {
        return [
            'default_time_formats' => $this->defaultTimeFormats,
            'project_info' => $this->projectInfo
        ];
    }

    /**
     * Returns form name by class name
     *
     * @param string $formClassName
     *
     * @return string|string[]
     *
     * @throws \ReflectionException
     */
    public function getFormName(string $formClassName): string{
        return MultiFormService::getFormName($formClassName);
    }
}