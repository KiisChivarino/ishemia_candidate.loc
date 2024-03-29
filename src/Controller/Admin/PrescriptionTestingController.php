<?php

namespace App\Controller\Admin;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\Admin\PrescriptionTesting\PrescriptionTestingInclusionTimeType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionTestingDataTableService;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\PatientTestingCreatorService;
use App\Services\EntityActions\Creator\PrescriptionTestingCreatorService;
use App\Services\EntityActions\Creator\SpecialPatientTestingCreatorService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionTestingTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\PrescriptionTestingType;
use App\Form\PatientTesting\PatientTestingRequiredType;

/**
 * Class PrescriptionTestingController
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PrescriptionTestingController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/prescription_testing/';

    /**
     * PrescriptionTestingController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of testing prescriptions
     * @Route("/prescription_testing/", name="admin_prescription_testing_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PrescriptionTestingDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        PrescriptionTestingDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['PRESCRIPTION'],
                ]
            )
        );
    }

    /**
     * New testing prescription
     * @Route(
     *     "/prescription/{prescription}/prescription_testing/new/",
     *     name="admin_prescription_testing_new",
     *     methods={"GET","POST"},
     *     requirements={"prescription"="\d+"}
     *     )
     *
     * @param Request $request
     * @param Prescription $prescription
     * @param PrescriptionTestingCreatorService $prescriptionTestingCreatorService
     * @param SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        PrescriptionTestingCreatorService $prescriptionTestingCreatorService,
        SpecialPatientTestingCreatorService $specialPatientTestingCreatorService
    ): Response
    {
        if ($prescription->getIsCompleted()) {
            return $this->redirectToRoute(
                'prescription_show',
                [
                    'prescription' => $prescription->getId(),
                ]
            );
        }

        /** @var PatientTesting $patientTesting */
        $patientTesting = $specialPatientTestingCreatorService->execute(
            [
                PatientTestingCreatorService::MEDICAL_HISTORY_OPTION => $prescription->getMedicalHistory(),
            ]
        )->getEntity();
        $prescriptionTestingCreatorService->before(
            [
                PrescriptionTestingCreatorService::PRESCRIPTION_OPTION => $prescription,
                PrescriptionTestingCreatorService::PATIENT_TESTING_OPTION => $patientTesting,
            ]
        );
        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder($prescriptionTestingCreatorService),
            ],
            [
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingStaff::class,
                    $prescriptionTestingCreatorService->getEntity()
                ),
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingPlannedDateType::class,
                    $prescriptionTestingCreatorService->getEntity()
                ),
                new FormData(PatientTestingRequiredType::class, $patientTesting),
            ]
        );
    }

    /**
     * Show testing prescription
     * @Route(
     *     "/prescription_testing/{prescriptionTesting}/",
     *     name="admin_prescription_testing_show",
     *     methods={"GET"},
     *     requirements={"id"="\d+"}
     *     )
     *
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     * @throws Exception
     */
    public function show(PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $prescriptionTesting,
            [
                'prescriptionTitle' =>
                    PrescriptionInfoService::getPrescriptionTitle($prescriptionTesting->getPrescription()),
                'patientTestingInfo' =>
                    PatientTestingInfoService::getPatientTestingInfoString($prescriptionTesting->getPatientTesting()),
                'staff' =>
                    AuthUserInfoService::getFIO($prescriptionTesting->getStaff()->getAuthUser(), true),
            ]
        );
    }

    /**
     * Edit testing prescription
     * @Route(
     *     "/prescription_testing/{prescriptionTesting}/edit/",
     *     name="admin_prescription_testing_edit",
     *     methods={"GET","POST"},
     *     requirements={"prescriptionTesting"="\d+"}
     *     )
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $prescriptionTesting,
            [
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingStaff::class,
                    $prescriptionTesting
                ),
                new FormData(
                    PatientTestingRequiredType::class,
                    $prescriptionTesting->getPatientTesting()
                ),
                new FormData(
                    PrescriptionTestingInclusionTimeType::class,
                    $prescriptionTesting
                ),
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingPlannedDateType::class,
                    $prescriptionTesting
                ),
                new FormData(
                    PrescriptionTestingType\PrescriptionTestingConfirmedEnableType::class,
                    $prescriptionTesting
                ),
            ]
        );
    }

    /**
     * Delete testing prescription
     * @Route("/prescription_testing/{id}/", name="admin_prescription_testing_delete", methods={"DELETE"},
     *     requirements={"prescriptionTesting"="\d+"})
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseDelete($request, $prescriptionTesting);
    }
}
