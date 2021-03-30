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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EditPatientTestingController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
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
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->templateService = new EditPatientTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Edit patient testing from all list
     * @Route(
     *     "/patient/{id}/patient_testing/{patientTesting}/edit",
     *     name="doctor_edit_patient_testing",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "patientTesting"="\d+"}
     *     )
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFromAll(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        return $this->edit($request, $patientTesting, $fileService, $patientTestingResultRepository);
    }

    /**
     * Edit patient testing from processed
     * @Route(
     *     "/patient/{id}/patient_testing_not_processed/{patientTesting}/edit",
     *     name="doctor_edit_patient_testing_from_not_processed",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "patientTesting"="\d+"}
     *     )
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFromNotProcessed(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        return $this->edit($request, $patientTesting, $fileService, $patientTestingResultRepository);
    }

    /**
     * Edit patient testing from planned
     * @Route(
     *     "/patient/{id}/patient_testing_planned/{patientTesting}/edit",
     *     name="doctor_edit_patient_testing_from_planned",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "patientTesting"="\d+"}
     *     )
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFromPlanned(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        return $this->edit($request, $patientTesting, $fileService, $patientTestingResultRepository);
    }

    /**
     * Edit patient testing from overdue
     * @Route(
     *     "/patient/{id}/patient_testing_overdue/{patientTesting}/edit",
     *     name="doctor_edit_patient_testing_from_overdue",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+", "patientTesting"="\d+"}
     *     )
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFromOverdue(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        return $this->edit($request, $patientTesting, $fileService, $patientTestingResultRepository);
    }

    /**
     * Edit patient testing from history
     * @Route(
     *     "/patient/{id}/patient_testing_history/{patientTesting}/edit",
     *     name="doctor_edit_patient_testing_from_history",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+", "patientTesting"="\d+"}
     *     )
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFromHistory(
        Request $request,
        PatientTesting $patientTesting,
        FileService $fileService,
        PatientTestingResultRepository $patientTestingResultRepository
    )
    {
        return $this->edit($request, $patientTesting, $fileService, $patientTestingResultRepository);
    }


    /**
     * Edit patient testing
     * @param Request $request
     * @param PatientTesting $patientTesting
     * @param FileService $fileService
     * @param PatientTestingResultRepository $patientTestingResultRepository
     * @return RedirectResponse|Response
     * @throws ReflectionException
     */
    private function edit(
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
                $patientTesting->setIsProcessedByStaff(true);
            }
        );
    }
}