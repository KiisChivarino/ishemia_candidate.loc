<?php

namespace App\Controller\Admin;

use App\Entity\PatientAppointment;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\ConfirmedType;
use App\Form\Admin\PatientAppointment\EnabledType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Form\Admin\PatientAppointmentType;
use App\Services\ControllerGetters\FilterLabels;
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
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use App\Form\PatientAppointment\PatientAppointmentConfirmedByStaffType;
use App\Form\PatientAppointment\PatientAppointmentIsMissedType;
use App\Services\EntityActions\Core\Builder\EditorEntityActionsBuilder;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Editor\PatientAppointmentEditorService;

use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * PatientAppointmentController constructor.
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
        $this->templateService = new PatientAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient appointments
     *
     * @Route("/", name="patient_appointment_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientAppointmentDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     *
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
     * Show patient appointment
     *
     * @Route("/{patientAppointment}", name="patient_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     *
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
     * @Route("/{patientAppointment}/edit", name="patient_appointment_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     *
     * @return Response
     *
     * @throws ReflectionException
     *
     * @throws Exception
     */
    public function edit(
        Request $request,
        PatientAppointment $patientAppointment,
        MedicalRecordCreatorService $medicalRecordCreatorService
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $this->responseEditMultiFormWithActions(
            $request,
            [
                new EditorEntityActionsBuilder(
                    new PatientAppointmentEditorService(
                        $entityManager, $patientAppointment
                    ),
                    [],
                    function () use ($medicalRecordCreatorService): array {
                        return
                            [
                                PatientAppointmentEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME =>
                                    $medicalRecordCreatorService,
                            ];
                    }
                ),
            ],
            [
                new FormData(PatientAppointmentType::class, $patientAppointment),
                new FormData(StaffType::class, $patientAppointment),
                new FormData(AppointmentTypeType::class, $patientAppointment),
                new FormData(ConfirmedType::class, $patientAppointment),
                new FormData(PatientAppointmentConfirmedByStaffType::class, $patientAppointment),
                new FormData(PatientAppointmentIsMissedType::class, $patientAppointment),
                new FormData(EnabledType::class, $patientAppointment),
            ]
        );
    }

    /**
     * Delete patient appointment
     *
     * @Route("/{id}", name="patient_appointment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     *
     * @throws Exception
     */
    public function delete(Request $request, PatientAppointment $patientAppointment): Response
    {
        return $this->responseDelete($request, $patientAppointment);
    }
}
