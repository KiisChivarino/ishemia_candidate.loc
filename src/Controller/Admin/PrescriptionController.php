<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Prescription;
use App\Form\Admin\Prescription\PrescriptionEditType;
use App\Form\Admin\PrescriptionType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionTemplate;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
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

    /**
     * PrescriptionController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
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
     */
    public function new(Request $request): Response
    {
        $prescription = new Prescription();
        if ($request->query->get('medical_history_id')) {
            /** @var MedicalHistory $medicalHistory */
            $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)
                ->find($request->query->get('medical_history_id'));
            if ($this->getDoctrine()->getRepository(Prescription::class)
                ->findNotCompletedPrescription($medicalHistory)) {
                $this->addFlash(
                    'warning',
                    'Назначение не может быть добавлено: для данной истории болезни есть незавершенное назначение!'
                );
                return $this->redirectToRoute($this->templateService->getRoute('new'));
            }
            $prescription->setMedicalHistory($medicalHistory);
        }
        return $this->responseNew(
            $request, $prescription, PrescriptionType::class, null, [],
            function (EntityActions $actions) {
                $actions->getEntity()->setIsCompleted(false);
                $actions->getEntity()->setIsPatientConfirmed(false);
                $actions->getEntity()->setCreatedTime(new DateTime());
            }
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
     */
    public function show(Prescription $prescription, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescription, [
                'staff' => (new AuthUserInfoService())->getFIO($prescription->getStaff()->getAuthUser(), true),
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
                'medicalHistoryTitle' => (new MedicalHistoryInfoService())
                    ->getMedicalHistoryTitle($prescription->getMedicalHistory()),
                'medicalRecordTitle' => (new MedicalRecordInfoService())
                    ->getMedicalRecordTitle($prescription->getMedicalRecord()),
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
     */
    public function edit(Request $request, Prescription $prescription): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $prescription,
            [
                new FormData($prescription, PrescriptionType::class),
                new FormData($prescription, PrescriptionEditType::class),
            ],
            function (EntityActions $entityActions) {
                $this->isCompletedActions($entityActions);
            }
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
     */
    public function delete(Request $request, Prescription $prescription): Response
    {
        return $this->responseDelete($request, $prescription);
    }

    /**
     * Actions if flag isCompleted checked
     *
     * @param EntityActions $entityActions
     */
    private function isCompletedActions(EntityActions $entityActions)
    {
        /** @var Prescription $prescription */
        $prescription = $entityActions->getEntity();
        if ($prescription->getIsCompleted()) {
            $prescription->setCompletedTime(new DateTime());
            $prescription->setMedicalRecord($entityActions->getEntityManager()->getRepository(MedicalRecord::class)
                ->getMedicalRecord($prescription->getMedicalHistory()));
        }
    }
}
