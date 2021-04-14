<?php

namespace App\Controller\Admin;

use App\Entity\Prescription;
use App\Form\Admin\Prescription\PrescriptionEditType;
use App\Form\Admin\PrescriptionType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionDataTableService;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Builder\EditorEntityActionsBuilder;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Creator\PrescriptionCreatorService;
use App\Services\EntityActions\Editor\PrescriptionEditorService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PrescriptionController
 * @Route("/admin/prescription")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PrescriptionController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/prescription/';

    /** @var string The key of parameter with prescription id */
    public const PRESCRIPTION_ID_PARAMETER_KEY = 'prescription_id';

    /**
     * PrescriptionController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Prescription list
     * @Route("/", name="prescription_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param FilterService $filterService
     * @param PrescriptionDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        FilterService $filterService,
        PrescriptionDataTableService $dataTableService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['MEDICAL_HISTORY'],
                    self::FILTER_LABELS['STAFF'],
                ]
            )
        );
    }

    /**
     * New prescription
     * @Route("/new", name="prescription_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request
    ): Response
    {
        $prescriptionCreatorService = new PrescriptionCreatorService($this->getDoctrine()->getManager());
        return $this->responseNewWithActions(
            $request,
            new CreatorEntityActionsBuilder(
                $prescriptionCreatorService,
                [
                    PrescriptionCreatorService::MEDICAL_HISTORY_OPTION => $this->getMedicalHistoryByParameter($request),
                ]//,
//                function (PrescriptionCreatorService $prescriptionCreatorService): array {
//                    return [
//                        PrescriptionCreatorService::STAFF_OPTION => $prescriptionCreatorService->getEntity()->getStaff(),
//                    ];
//                }
            ),
            new FormData(PrescriptionType::class)
        );
    }

    /**
     * Show prescription
     * @Route("/{id}", name="prescription_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Prescription $prescription
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function show(
        Prescription $prescription,
        FilterService $filterService
    ): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescription, [
                'staff' => AuthUserInfoService::getFIO($prescription->getStaff()->getAuthUser(), true),
                'prescriptionMedicineFilterName' =>
                    $filterService->generateFilterName(
                        'prescription_medicine_list',
                        Prescription::class
                    ),
                'prescriptionTestingFilterName' =>
                    $filterService->generateFilterName(
                        'prescription_testing_list',
                        Prescription::class
                    ),
                'prescriptionAppointmentFilterName' =>
                    $filterService->generateFilterName(
                        'prescription_appointment_list',
                        Prescription::class
                    ),
                'medicalHistoryTitle' =>
                    MedicalHistoryInfoService::getMedicalHistoryTitle($prescription->getMedicalHistory()),
                'medicalRecordTitle' =>
                    MedicalRecordInfoService::getMedicalRecordTitle($prescription->getMedicalRecord()),
            ]
        );
    }

    /**
     * Edit prescription
     * @Route("/{id}/edit", name="prescription_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Prescription $prescription
     *
     * @return Response
     * @throws Exception
     */
    public function edit(
        Request $request,
        Prescription $prescription
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $prescriptionEditorService = (new PrescriptionEditorService($entityManager, $prescription))->before();
        return $this->responseEditMultiformWithActions(
            $request,
            [
                new EditorEntityActionsBuilder(
                    $prescriptionEditorService,
                    [],
                    function (PrescriptionEditorService $pescriptionEditorService) use ($entityManager): array {
                        return
                            [
                                PrescriptionEditorService::MEDICAL_RECORD_OPTION_NAME =>
                                    (new MedicalRecordCreatorService($entityManager))->execute(
                                        [
                                            MedicalRecordCreatorService::MEDICAL_HISTORY_OPTION_NAME =>
                                                $pescriptionEditorService->getEntity()->getMedicalHistory(),
                                        ]
                                    )->getEntity()
                            ];
                    }
                ),
            ],
            [
                new FormData(PrescriptionType::class, $prescription),
                new FormData(PrescriptionEditType::class, $prescription),
            ]
        );
    }

    /**
     * Delete prescription
     * @Route("/{id}", name="prescription_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Prescription $prescription
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Prescription $prescription): Response
    {
        return $this->responseDelete($request, $prescription);
    }
}
