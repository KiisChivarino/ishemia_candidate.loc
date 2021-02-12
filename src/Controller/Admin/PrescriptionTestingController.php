<?php

namespace App\Controller\Admin;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Form\PatientTesting\PatientTestingRequiredType;
use App\Form\PrescriptionTestingType;
use App\Repository\PrescriptionRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PrescriptionTestingDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientTestingInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PrescriptionTestingTemplate;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PrescriptionTestingController
 * @Route("/admin/prescription_testing")
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
     * @Route("/", name="prescription_testing_list", methods={"GET","POST"})
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
     * @param PrescriptionRepository $prescriptionRepository
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, PrescriptionRepository $prescriptionRepository): Response
    {
        if ($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)) {
            /** @var Prescription $prescription */
            $prescription = $prescriptionRepository
                ->find($request->query->get(PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY));
            if (!$prescription || !is_a($prescription, Prescription::class)) {
                $this->addFlash(
                    'warning',
                    'Назначение на консультацию не может быть добавлено: назначение не найдено!'
                );
                return $this->redirectToRoute($this->templateService->getRoute('new'));
            } else {
                $patientTesting = (new PatientTesting())
                    ->setEnabled(true)
                    ->setIsProcessedByStaff(false)
                    ->setMedicalHistory($prescription->getMedicalHistory());
                $prescriptionTesting = (new PrescriptionTesting())
                    ->setPrescription($prescription)
                    ->setEnabled(true)
                    ->setPatientTesting($patientTesting)
                    ->setInclusionTime(new DateTime());
                return $this->responseNewMultiForm(
                    $request,
                    $prescriptionTesting,
                    [
                        new FormData($prescriptionTesting, PrescriptionTestingType::class),
                        new FormData($patientTesting, PatientTestingRequiredType::class),
                    ]
                );
            }
        } else {
            $this->addFlash(
                'warning',
                'Назначение на консультацию не может быть добавлено: необходим код назначения!'
            );
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
    }

    /**
     * Show testing prescription
     * @Route("/{id}", name="prescription_testing_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     * @throws Exception
     */
    public function show(PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $prescriptionTesting, [
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
     * @Route("/{id}/edit", name="prescription_testing_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     *
     * @return Response
     * @throws Exception
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
     * @throws Exception
     */
    public function delete(Request $request, PrescriptionTesting $prescriptionTesting): Response
    {
        return $this->responseDelete($request, $prescriptionTesting);
    }
}
