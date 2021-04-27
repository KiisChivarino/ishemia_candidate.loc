<?php

namespace App\Controller\Admin;

use App\Entity\PatientMedicine;
use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Form\Admin\PatientMedicineType;
use App\Form\Admin\PrescriptionMedicineType;
use App\Form\Admin\PrescriptionMedicineTypeEnabled;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionMedicineDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionMedicineTemplate;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionMedicineTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * PrescriptionMedicine list
     * @Route("/prescription_medicine", name="prescription_medicine_list", methods={"GET","POST"})
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
     * @Route(
     *     "/prescription/{prescription}/prescription_medicine/new",
     *     name="prescription_medicine_new",
     *     methods={"GET","POST"},
     *      requirements={"prescription"="\d+"}
     *     )
     *
     * @param Request $request
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, Prescription $prescription): Response
    {
        $prescriptionMedicine = new PrescriptionMedicine();
        $prescriptionMedicine->setPrescription($prescription);
        $patientMedicine = new PatientMedicine();
        $patientMedicine->setEnabled(true);
        $prescriptionMedicine->setPatientMedicine($patientMedicine);

        return $this->responseNewMultiForm(
            $request,
            $prescriptionMedicine,
            [
                new FormData($prescriptionMedicine, PrescriptionMedicineType::class),
                new FormData($patientMedicine, PatientMedicineType::class),
            ],
            function (EntityActions $actions) {
                $actions->getEntity()->setInclusionTime(new DateTime());
                // TODO: AppAbstractController не позволяет получить id prescriptionMedicine до персиста
                $actions->getEntityManager()->persist($actions->getEntity()); // <-- Костыль
                $this->templateService->setRedirectRoute(
                    $this->templateService->getRedirectRouteName(),
                    [
                        'prescriptionMedicine'=>$actions->getEntity()->getId(),
                        'prescription'=>$actions->getEntity()->getPrescription()->getId()
                    ]
                );
            }
        );

    }

    /**
     * Show medicine prescription info
     * @Route(
     *     "/prescription/{prescription}/prescription_medicine/{prescriptionMedicine}",
     *     name="prescription_medicine_show",
     *     methods={"GET"},
     *      requirements={"prescriptionMedicine"="\d+","prescription"="\d+"}
     *     )
     *
     * @param PrescriptionMedicine $prescriptionMedicine
     *
     * @return Response
     * @throws Exception
     */
    public function show(PrescriptionMedicine $prescriptionMedicine): Response
    {
        $this->templateService->setRedirectRoute(
            $this->templateService->getRedirectRouteName(),
            ['prescriptionMedicine'=>$prescriptionMedicine->getId()]
        );
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
     *     "/prescription/{prescription}/prescription_medicine/{prescriptionMedicine}/edit",
     *     name="prescription_medicine_edit",
     *     methods={"GET","POST"},
     *      requirements={"prescriptionMedicine"="\d+", "prescription"="\d+"}
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
                new FormData($prescriptionMedicine, PrescriptionMedicineType::class),
                new FormData($prescriptionMedicine->getPatientMedicine(), PatientMedicineType::class),
                new FormData($prescriptionMedicine, PrescriptionMedicineTypeEnabled::class),
            ]
        );
    }

    /**
     * Delete prescription medicine
     * @Route("/prescription_medicine/{id}", name="prescription_medicine_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
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
