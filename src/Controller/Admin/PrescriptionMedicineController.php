<?php

namespace App\Controller\Admin;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Form\Admin\PrescriptionMedicineType;
use App\Repository\PrescriptionRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionMedicineDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateBuilders\Admin\PrescriptionMedicineTemplate;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PrescriptionMedicineController
 * @Route("/admin/prescription_medicine")
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
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PrescriptionMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * PrescriptionMedicine list
     * @Route("/", name="prescription_medicine_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PrescriptionMedicineDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        Request $request,
        PrescriptionMedicineDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['PRESCRIPTION'],
                ]
            )
        );
    }

    /**
     * New medicine prescription
     * @Route("/new", name="prescription_medicine_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @param PrescriptionRepository $prescriptionRepository
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, PrescriptionRepository $prescriptionRepository): Response
    {
        $prescriptionMedicine = new PrescriptionMedicine();
        if ($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)) {
            /** @var Prescription $prescription */
            $prescription = $prescriptionRepository
                ->find($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY));
            $prescriptionMedicine->setPrescription($prescription);
        }
        return $this->responseNew(
            $request, $prescriptionMedicine, PrescriptionMedicineType::class, null, [],
            function (EntityActions $actions) {
                $actions->getEntity()->setInclusionTime(new DateTime());
            }
        );
    }

    /**
     * Show medicine prescription info
     * @Route("/{id}", name="prescription_medicine_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
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
     * @Route("/{id}/edit", name="prescription_medicine_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PrescriptionMedicine $prescriptionMedicine): Response
    {
        return $this->responseEdit($request, $prescriptionMedicine, PrescriptionMedicineType::class);
    }

    /**
     * Delete prescription medicine
     * @Route("/{id}", name="prescription_medicine_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     */
    public function delete(Request $request, PrescriptionMedicine $prescriptionMedicine): Response
    {
        return $this->responseDelete($request, $prescriptionMedicine);
    }
}
