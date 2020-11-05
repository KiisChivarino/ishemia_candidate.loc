<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\PatientAppointment;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\ConfirmedType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Form\Admin\PatientAppointmentType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientAppointmentDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientAppointmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientAppointmentController
 * @Route("admin/patient_appointment")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PatientAppointmentController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_appointment/';

    /** @var string Flash message stating that no medical history was found */
    private const FLASH_ERROR_MEDICAL_HISTORY_NOT_FOUND =
        'Прием пациента не может быть добавлен: история болезни не найдена!';
    /** @var string Route for redirect after error "Medical history not found" */
    private const FLASH_ERROR_REDIRECT_ROUTE = 'medical_history_list';
    /** @var string Get parameter name of medical history id */
    private const MEDICAL_HISTORY_ID_GET_PARAMETER = 'medical_history_id';

    /**
     * PatientAppointmentController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient appointments
     * @Route("/", name="patient_appointment_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientAppointmentDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        Request $request,
        PatientAppointmentDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['MEDICAL_HISTORY'],
                ]
            )
        );
    }

    /**
     * New patient appointment
     * @Route("/new", name="patient_appointment_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        if ($request->query->get(self::MEDICAL_HISTORY_ID_GET_PARAMETER)) {
            $medicalHistory = $this->getDoctrine()->getManager()->getRepository(MedicalHistory::class)
                ->find($request->query->get(self::MEDICAL_HISTORY_ID_GET_PARAMETER));
        }
        if (!isset($medicalHistory) || !is_a($medicalHistory, MedicalHistory::class)) {
            $this->addFlash('warning', self::FLASH_ERROR_MEDICAL_HISTORY_NOT_FOUND);
            return $this->redirectToRoute(self::FLASH_ERROR_REDIRECT_ROUTE);
        }
        $patientAppointment = (new PatientAppointment())
            ->setMedicalHistory($medicalHistory)->setIsConfirmed(true);
        return $this->responseNewMultiForm(
            $request,
            $patientAppointment,
            [
                new FormData($patientAppointment, PatientAppointmentType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
            ],
            function (EntityActions $actions) use ($medicalHistory) {
                /** @var PatientAppointment $patientAppointment */
                $patientAppointment = $actions->getEntity();
                $patientAppointment->setMedicalRecord(
                    $this
                        ->getDoctrine()
                        ->getRepository(MedicalRecord::class)
                        ->getMedicalRecord($medicalHistory)
                );
                $patientAppointment->setIsConfirmed(false);
            }
        );
    }

    /**
     * Show patient appointment
     * @Route("/{id}", name="patient_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function show(PatientAppointment $patientAppointment): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $patientAppointment, [
                'medicalHistoryTitle' =>
                    (new MedicalHistoryInfoService())->getMedicalHistoryTitle($patientAppointment->getMedicalHistory()),
                'medicalRecordTitle' =>
                    (new MedicalRecordInfoService())->getMedicalRecordTitle($patientAppointment->getMedicalRecord()),
                'staffFio' => $patientAppointment->getStaff()
                    ? (new AuthUserInfoService())->getFIO($patientAppointment->getStaff()->getAuthUser(), true)
                    : '',
            ]
        );
    }

    /**
     * Edit patient appointment
     * @Route("/{id}/edit", name="patient_appointment_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function edit(Request $request, PatientAppointment $patientAppointment): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $patientAppointment,
            [
                new FormData($patientAppointment, PatientAppointmentType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
                new FormData($patientAppointment, ConfirmedType::class),
            ]
        );
    }

    /**
     * Delete patient appointment
     * @Route("/{id}", name="patient_appointment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function delete(Request $request, PatientAppointment $patientAppointment): Response
    {
        return $this->responseDelete($request, $patientAppointment);
    }
}
