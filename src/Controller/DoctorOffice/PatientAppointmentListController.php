<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\PatientAppointment\PatientAppointmentConfirmedByStaffType;
use App\Services\DataTable\DoctorOffice\PatientAppointment\PatientAppointmentHistoryDataTable;
use App\Services\DataTable\DoctorOffice\PatientAppointment\PatientAppointmentNoProcessedDataTable;
use App\Services\EntityActions\Core\Builder\EditorEntityActionsBuilder;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Editor\PatientAppointmentEditorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientAppointmentListTemplate;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PatientAppointmentListController
 *
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class PatientAppointmentListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patient_appointment_list/';

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientAppointmentListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * patient appointment no processed
     * @Route(
     *     "/patient/{patient}/patient_appointment_no_processed",
     *     name="doctor_patient_appointment_no_processed",
     *     methods={"GET","POST"},
     *     requirements={"patient"="\d+"}
     *     )
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientAppointmentNoProcessedDataTable $dataTableService
     *
     * @return Response
     */
    public function patientAppointmentNoProcessed(
        Patient $patient,
        Request $request,
        PatientAppointmentNoProcessedDataTable $dataTableService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            null,
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_appointment_no_processed'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список необработанных приемов');
                $this->templateService->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
                    ->setIsEnabled(false);
            }
        );
    }

    /**
     * List of patient appointment history
     * @Route(
     *     "/patient/{patient}/patient_appointment_history",
     *     name="doctor_patient_appointment_history",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientAppointmentHistoryDataTable $dataTableService
     *
     * @return Response
     */
    public function patientAppointmentHistory(
        Patient $patient,
        Request $request,
        PatientAppointmentHistoryDataTable $dataTableService
    ): Response
    {

        return $this->responseList(
            $request,
            $dataTableService,
            null,
            [
                'patientId' => $patient->getId(),
                'route' => 'doctor_edit_patient_appointment_history'
            ],
            function () {
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'История приёмов');
                $this->templateService->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
                    ->setIsEnabled(false);
            }
        );
    }

    /**
     * Show patient appointment
     *
     * @Route(
     *     "/patient/{patient}/patient_appointment/{patientAppointment}/edit",
     *     name="doctor_patient_appointment_show",
     *     methods={"GET"},
     *     requirements={"patient"="\d+"})
     *
     * @param Patient $patient
     * @param PatientAppointment $patientAppointment
     * @return Response
     */
    public function show(
        Patient $patient,
        PatientAppointment $patientAppointment
    ): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patientAppointment
        );
    }

    /**
     * Edit patient appointment
     *
     * @Route(
     *     "/patient/{patient}/patient_appointment_not_processed/{patientAppointment}/edit",
     *     name="doctor_edit_patient_appointment_no_processed",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+", "patientAppointment"="\d+"}
     *     )
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        patientAppointment $patientAppointment,
        MedicalRecordCreatorService $medicalRecordCreatorService
    )
    {
        if ($patientAppointment->getIsMissed() or $patientAppointment->getIsProcessedByStaff()) {
            return $this->redirectToRoute(
                'doctor_patient_appointment_no_processed',
                [
                    'patient' => $patientAppointment->getMedicalHistory()->getPatient()->getId()
                ]
            );
        }
        $this->templateService->setRedirectRoute(
            'doctor_patient_appointment_no_processed',
            ['patient' => $patientAppointment->getMedicalHistory()->getPatient()->getId()]
        );
        $entityManager = $this->getDoctrine()->getManager();
        return $this->responseEditMultiFormWithActions(
            $request,
            [
                new EditorEntityActionsBuilder(new PatientAppointmentEditorService(
                    $entityManager, $patientAppointment
                ),
                    [],
                    function () use ($medicalRecordCreatorService): array {
                        return
                            [
                                PatientAppointmentEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME =>
                                    $medicalRecordCreatorService,
                            ];
                    }),
            ],
            [
                new FormData(PatientAppointmentType::class, $patientAppointment),
                new FormData(AppointmentTypeType::class, $patientAppointment),
                new FormData(PatientAppointmentConfirmedByStaffType::class, $patientAppointment),
            ]
        );
    }


    /**
     * Is missed patient appointment
     *
     * @Route(
     *     "/patient/{id}/patient_appointment_not_processed/{patientAppointment}/missing",
     *     name="doctor_edit_patient_appointment_missing",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+", "patientAppointment"="\d+"}
     *     )
     * @param Patient $patient
     * @param PatientAppointment $patientAppointment
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     * @return RedirectResponse
     * @throws Exception
     */
    public function isMissedPatientAppointment(
        Patient $patient,
        PatientAppointment $patientAppointment,
        MedicalRecordCreatorService $medicalRecordCreatorService
    ): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        (new PatientAppointmentEditorService($entityManager, $patientAppointment))
            ->before(
                [
                    PatientAppointmentEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME => $medicalRecordCreatorService,
                ]
            )
            ->missingPatientAppointment()
            ->after();
        $entityManager->flush();
        return $this->redirectToRoute(
            'doctor_patient_appointment_no_processed',
            [
                'patient' => $patient->getId()
            ]
        );
    }
}