<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\ConfirmedType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Form\Admin\PatientAppointmentType;
use App\Repository\MedicalHistoryRepository;
use App\Repository\MedicalRecordRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\DataTable\Admin\PatientAppointmentDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientAppointmentTemplate;
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

    /**
     * PatientAppointmentController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     * @param MedicalRecordRepository $medicalRecordRepository
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        MedicalRecordRepository $medicalRecordRepository,
        MedicalHistoryRepository $medicalHistoryRepository
): Response
    {
        if ($request->query->get(MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)) {
            $medicalHistory = $medicalHistoryRepository
                ->find($request->query->get(MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY));
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
            function (EntityActions $actions) use ($medicalHistory, $medicalRecordRepository) {
                /** @var PatientAppointment $patientAppointment */
                $patientAppointment = $actions->getEntity();
                $patientAppointment->setMedicalRecord(
                    $medicalRecordRepository->getMedicalRecord($medicalHistory)
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
     * @throws Exception
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
     * @param PatientAppointmentCreatorService $patientAppointmentCreator
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        PatientAppointment $patientAppointment,
        PatientAppointmentCreatorService $patientAppointmentCreator
    ): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $patientAppointment,
            [
                new FormData($patientAppointment, PatientAppointmentType::class),
                new FormData($patientAppointment, StaffType::class),
                new FormData($patientAppointment, AppointmentTypeType::class),
                new FormData($patientAppointment, ConfirmedType::class),
            ],
            function () use ($patientAppointment, $patientAppointmentCreator) {
                $patientAppointmentCreator->checkAndPersistRegularPatientAppointment($patientAppointment);
            }
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
     * @throws Exception
     */
    public function delete(Request $request, PatientAppointment $patientAppointment): Response
    {
        return $this->responseDelete($request, $patientAppointment);
    }
}
