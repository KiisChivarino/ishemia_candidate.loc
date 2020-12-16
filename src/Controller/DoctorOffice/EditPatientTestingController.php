<?php

namespace App\Controller\DoctorOffice;

use App\Entity\PatientTesting;
use App\Form\Admin\PatientTesting\PatientTestingNotRequiredType;
use App\Form\Admin\PatientTestingResultType;
use App\Form\PatientTestingFileType;
use App\Repository\PatientTestingResultRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\FileService\FileService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\EditPatientTestingTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
/**
 * Class EditPatientTestingController
 * @package App\Controller\DoctorOffice
 */
class EditPatientTestingController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/edit_patient_testing/';

    /**
     * EditPatientTestingController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router
    )
    {
        $this->templateService = new EditPatientTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * @Route(
     *     "/{id}/edit_patient_testing",
     *     name="doctor_edit_patient_testing",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request \
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        $this->setRedirectMedicalHistoryRoute($patientTesting->getMedicalHistory()->getPatient()->getId());
        $this->templateService->setCommonTemplatePath(self::TEMPLATE_PATH);
        $enabledTestingResults = $patientTestingResultRepository->getEnabledTestingResults($patientTesting);
        $patientTestingResultsFormData = [];
        foreach ($enabledTestingResults as $key => $patientTestingResult) {
            $patientTestingResultsFormData[] = new FormData(
                $patientTestingResult,
                PatientTestingResultType::class,
                [
                    'analysis' => $patientTestingResult->getAnalysis()
                ],
                true,
                $key
            );
        }
        return $this->responseEditMultiForm(
            $request,
            $patientTesting,
            array_merge(
                [
                    new FormData($patientTesting, PatientTestingNotRequiredType::class),
                ],
                $patientTestingResultsFormData
            ),
            function (EntityActions $actions) use ($fileService, $patientTesting) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(PatientTestingNotRequiredType::class))
                        ->get(MultiFormService::getFormName(PatientTestingFileType::class) . 's')
                );
                $patientTesting->setEnabled(true);
                $patientTesting->setProcessed(true);
            }
        );
    }
}