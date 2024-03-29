<?php

namespace App\Controller\Admin;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Form\Admin\PrescriptionMedicine\PrescriptionMedicineInclusionTimeType;
use App\Form\PatientMedicineType\PatientMedicineType;
use App\Form\PrescriptionMedicineType\PrescriptionMedicineStaffType;
use App\Form\PrescriptionMedicineType\PrescriptionMedicineType;
use App\Form\PrescriptionMedicineType\PrescriptionMedicineTypeEnabled;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionMedicineDataTableService;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\PatientMedicineCreatorService;
use App\Services\EntityActions\Creator\PrescriptionMedicineCreatorService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionMedicineTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PrescriptionMedicineController
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PrescriptionMedicineController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/prescription_medicine/';

    /**
     * PrescriptionMedicineController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     *
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * PrescriptionMedicine list
     * @Route("/prescription_medicine/", name="prescription_medicine_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PrescriptionMedicineDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        PrescriptionMedicineDataTableService $dataTableService,
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
     * New medicine prescription
     * @Route(
     *     "/prescription/{prescription}/prescription_medicine/new/",
     *     name="prescription_medicine_new",
     *     methods={"GET","POST"},
     *     requirements={"prescription"="\d+"}
     *     )
     *
     * @param Request $request
     * @param Prescription $prescription
     * @param PrescriptionMedicineCreatorService $prescriptionMedicineCreatorService
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        PrescriptionMedicineCreatorService $prescriptionMedicineCreatorService,
        PatientMedicineCreatorService $patientMedicineCreatorService
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
        $patientMedicine = $patientMedicineCreatorService->execute()->getEntity();
        $prescriptionMedicine = $prescriptionMedicineCreatorService->before(
            [
                PrescriptionMedicineCreatorService::PRESCRIPTION_OPTION => $prescription,
                PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine
            ]
        )->getEntity();
        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder(
                    $prescriptionMedicineCreatorService,
                    [],
                    function () use ($patientMedicine): array {
                        return
                            [
                                PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine,
                            ];
                    }
                )
            ],
            [
                new FormData(PrescriptionMedicineType::class, $prescriptionMedicine),
                new FormData(PrescriptionMedicineStaffType::class, $prescriptionMedicine),
                new FormData(PatientMedicineType::class, $patientMedicine),
            ]
        );
    }

    /**
     * Show medicine prescription info
     * @Route(
     *     "/prescription_medicine/{prescriptionMedicine}/",
     *     name="prescription_medicine_show",
     *     methods={"GET"},
     *     requirements={"prescriptionMedicine"="\d+"}
     *     )
     *
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     * @throws Exception
     */
    public function show(PrescriptionMedicine $prescriptionMedicine): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescriptionMedicine, [
                'prescriptionTitle' =>
                    PrescriptionInfoService::getPrescriptionTitle($prescriptionMedicine->getPrescription()),
                'staffTitle' =>
                    AuthUserInfoService::getFIO($prescriptionMedicine->getStaff()->getAuthUser()),
            ]
        );
    }

    /**
     * Edit prescription medicine
     * @Route(
     *     "/prescription_medicine/{prescriptionMedicine}/edit/",
     *     name="prescription_medicine_edit",
     *     methods={"GET","POST"},
     *     requirements={"prescriptionMedicine"="\d+"}
     *     )
     *
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PrescriptionMedicine $prescriptionMedicine): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $prescriptionMedicine,
            [
                new FormData(PrescriptionMedicineStaffType::class, $prescriptionMedicine),
                new FormData(PrescriptionMedicineInclusionTimeType::class, $prescriptionMedicine),
                new FormData(PrescriptionMedicineType::class, $prescriptionMedicine),
                new FormData(PrescriptionMedicineTypeEnabled::class, $prescriptionMedicine),
                new FormData(PatientMedicineType::class, $prescriptionMedicine->getPatientMedicine()),
            ]
        );
    }

    /**
     * Delete prescription medicine
     * @Route("/prescription_medicine/{prescriptionMedicine}/", name="prescription_medicine_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, PrescriptionMedicine $prescriptionMedicine): Response
    {
        return $this->responseDelete($request, $prescriptionMedicine);
    }
}
