<?php

namespace App\Controller\Admin;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\Admin\PatientTesting\PatientTestingNewType;
use App\Form\Admin\PrescriptionTestingType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionTestingDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateBuilders\PrescriptionTestingTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PrescriptionTestingController
 * @Route("/admin/prescription_testing")
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
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PrescriptionTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of testing prescriptions
     * @Route("/", name="prescription_testing_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PrescriptionTestingDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(Request $request, PrescriptionTestingDataTableService $dataTableService, FilterService $filterService): Response
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
     * New testing prescription
     * @Route("/new", name="prescription_testing_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        if ($request->query->get('prescription_id')) {
            /** @var Prescription $prescription */
            $prescription = $this->getDoctrine()->getManager()->getRepository(Prescription::class)->find($request->query->get('prescription_id'));
            if (!$prescription || !is_a($prescription, Prescription::class)) {
                $this->addFlash('warning', 'Назначение на консультацию не может быть добавлено: назначение не найдено!');
                return $this->redirectToRoute($this->templateService->getRoute('new'));
            }
        }
        $template = $this->templateService->new();
        $patientTesting = (new PatientTesting())
            ->setEnabled(true)
            ->setProcessed(false)
            ->setMedicalHistory($prescription->getMedicalHistory());
        $prescriptionTesting = (new PrescriptionTesting())
            ->setPrescription($prescription)
            ->setEnabled(true)
            ->setPatientTesting($patientTesting)
            ->setInclusionTime(new DateTime());
        return $this->responseFormTemplate(
            $request, $prescriptionTesting,
            $this->createFormBuilder()
                ->setData(
                    [
                        'prescriptionTesting' => $prescriptionTesting,
                        'patientTesting' => $patientTesting
                    ]
                )
                ->add(
                    'prescriptionTesting', PrescriptionTestingType::class, [
                        'label' => 'Назначение обследования',
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->add(
                    'patientTesting', PatientTestingNewType::class, [
                        'label' => 'Новое обследование',
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->getForm(),
            self::RESPONSE_FORM_TYPE_NEW
        );
    }

    /**
     * Show testing prescription
     * @Route("/{id}", name="prescription_testing_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     */
    public function show(PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescriptionTesting, [
            'prescriptionTitle' => (new PrescriptionInfoService())->getPrescriptionTitle($prescriptionTesting->getPrescription()),
            'patientTestingInfo' => (new PatientTestingInfoService())->getPatientTestingInfoString($prescriptionTesting->getPatientTesting()),
            'staffFio' => (new AuthUserInfoService())->getFIO($prescriptionTesting->getStaff()->getAuthUser(), true),
        ]
        );
    }

    /**
     * Edit testing prescription
     * @Route("/{id}/edit", name="prescription_testing_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     */
    public function edit(Request $request, PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseEdit($request, $prescriptionTesting, PrescriptionTestingType::class);
    }

    /**
     * Delete testing prescription
     * @Route("/{id}", name="prescription_testing_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     */
    public function delete(Request $request, PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseDelete($request, $prescriptionTesting);
    }
}
