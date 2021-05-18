<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Repository\MedicalHistoryRepository;
use App\Services\DataTable\DataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionAppointmentDataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionMedicineDataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionTestingDataTableService;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionService;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Creator\PrescriptionCreatorService;
use App\Services\EntityActions\Editor\PrescriptionEditorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\Notification\NotificationData;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use App\Services\TemplateItems\ShowTemplateItem;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Omines\DataTablesBundle\DataTable;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientPrescriptionController
 * @Route("/doctor_office/")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class PrescriptionController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/patient_prescription/';

    /** @var string Route name for edit prescription appointment */
    const EDIT_PRESCRIPTION_APPOINTMENT_ROUTE_NAME = 'edit_prescription_appointment_by_doctor';

    /** @var string Route name for edit prescription testing */
    const EDIT_PRESCRIPTION_TESTING_ROUTE_NAME = 'edit_prescription_testing_by_doctor';

    /** @var string Route name for edit prescription appointment */
    const EDIT_PRESCRIPTION_MEDICINE_ROUTE_NAME = 'edit_prescription_medicine_by_doctor';

    /**
     * @var NotifierService
     */
    private $notifier;

    /**
     * @var NotificationsServiceBuilder
     */
    private $notificationServiceBuilder;

    /**
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param NotifierService $notifier
     * @param NotificationsServiceBuilder $notificationServiceBuilder
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        NotifierService $notifier,
        NotificationsServiceBuilder $notificationServiceBuilder
    )
    {
        parent::__construct($translator);
        $this->templateService = new AddPatientPrescriptionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->notifier = $notifier;
        $this->notificationServiceBuilder = $notificationServiceBuilder;
    }

    /**
     * New prescription
     * @Route(
     *     "patient/{id}/prescription/new",
     *     name="adding_prescriprion_by_doctor",
     *     methods={"GET","POST"}
     *     )
     *
     * @param Patient $patient
     * @param DoctorOfficePrescriptionService $prescriptionCreatorService
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return Response
     * @throws Exception
     */
    public function new(
        Patient $patient,
        DoctorOfficePrescriptionService $prescriptionCreatorService,
        MedicalHistoryRepository $medicalHistoryRepository
    ): Response
    {
        $this->templateService->new();
        if (!$medicalHistory = $this->getCurrentMedicalHistory($patient, $medicalHistoryRepository)) {
            return $this->redirectToMedicalHistory($patient);
        }
        $prescriptionCreatorService->execute(
            [
                PrescriptionCreatorService::MEDICAL_HISTORY_OPTION => $medicalHistory,
                PrescriptionCreatorService::STAFF_OPTION => $this->getStaff($patient),
            ]
        );
        $this->setLogCreate($prescriptionCreatorService->getEntity());
        if (!$this->flush()) {
            $this->redirectToMedicalHistory($patient);
        }
        return $this->redirectToRoute(
            'add_prescription_show', [
                'patient' => $patient->getId(),
                'prescription' => $prescriptionCreatorService->getEntity()->getId()
            ]
        );
    }

    /**
     * Show prescription
     * @Route(
     *     "patient/{patient}/prescription/{prescription}/show",
     *     name="add_prescription_show",
     *     methods={"GET", "POST"},
     *     requirements={"patient"="\d+", "prescription"="\d+"}
     *     )
     * @param Patient $patient
     * @param Prescription $prescription
     * @param Request $request
     * @param PrescriptionTestingDataTableService $prescriptionTestingDataTableService
     * @param PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService
     * @param PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function show(
        Patient $patient,
        Prescription $prescription,
        Request $request,
        PrescriptionTestingDataTableService $prescriptionTestingDataTableService,
        PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService,
        PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService
    ): Response
    {
        if ($prescription->getIsCompleted()) {
            return $this->redirectToMedicalHistory($patient);
        }
        $this->templateService->show($patient);
        $prescriptionTestingTable = $this->generatePrescriptionTestingDataTable(
            $request,
            $prescriptionTestingDataTableService,
            $patient,
            $prescription
        );
        if ($prescriptionTestingTable->isCallback()) {
            return $prescriptionTestingTable->getResponse();
        }
        $prescriptionAppointmentTable = $this->generatePrescriptionAppointmentDataTable(
            $request,
            $prescriptionAppointmentDataTableService,
            $patient,
            $prescription
        );
        if ($prescriptionAppointmentTable->isCallback()) {
            return $prescriptionAppointmentTable->getResponse();
        }

        $prescriptionMedicineTable = $this->generatePrescriptionMedicineDataTable(
            $request,
            $prescriptionMedicineDataTableService,
            $patient,
            $prescription
        );

        if ($prescriptionMedicineTable->isCallback()) {
            return $prescriptionMedicineTable->getResponse();
        }

        return $this->render(
            self::TEMPLATE_PATH . 'prescription_show.html.twig',
            [
                'prescriptionTestingTable' => $prescriptionTestingTable,
                'prescriptionAppointmentTable' => $prescriptionAppointmentTable,
                'prescriptionMedicineTable' => $prescriptionMedicineTable,
                'patient' => $patient,
                'prescription' => $prescription
            ]
        );
    }

    /**
     * Delete testing prescription
     * @Route(
     *     "patient/{patient}/prescription/{prescription}/prescription_testing/{prescriptionTesting}/delete",
     *     name="prescription_testing_delete",
     *     methods={"DELETE"},
     *     requirements={"patient"="\d+", "prescription"="\d+"}
     *     )
     *
     * @param Request $request
     * @param PrescriptionTesting $prescriptionTesting
     * @param Patient $patient
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function delete(
        Request $request,
        Patient $patient,
        Prescription $prescription,
        PrescriptionTesting $prescriptionTesting
    ): Response
    {
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $patient->getId(),
                'prescription' => $prescription->getId()
            ]
        );
        return $this->responseDelete($request, $prescriptionTesting);
    }

    /**
     * Sets prescription completed and redirects to medical history page
     * @Route(
     *     "patient/{patient}/prescription/{prescription}/complete",
     *     name="complete_prescription",
     *     methods={"GET"},
     *     requirements={"patient"="\d+", "prescription"="\d+"}
     * )
     * @param Prescription $prescription
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     * @return RedirectResponse
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function completePrescription(
        Prescription $prescription,
        MedicalRecordCreatorService $medicalRecordCreatorService
    ): RedirectResponse
    {
        if (PrescriptionInfoService::isSpecialPrescriptionsExists($prescription) && !$prescription->getIsCompleted()) {
            $entityManager = $this->getDoctrine()->getManager();
            (new PrescriptionEditorService($entityManager, $prescription))->before()->completePrescription()->after(
                [
                    PrescriptionEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME => $medicalRecordCreatorService
                ]
            );
            foreach ($prescription->getPrescriptionTestings() as $prescriptionTesting) {
                $notificationServiceBuilder = $this->notificationServiceBuilder
                    ->makeTestingAppointmentNotification(
                        new NotificationData(
                            $this->getDoctrine()->getManager(),
                            $prescription->getMedicalHistory()->getPatient(),
                            $prescription->getMedicalHistory(),
                            $prescription->getMedicalRecord()
                        ),
                        $prescriptionTesting->getPatientTesting()->getAnalysisGroup()->getName(),
                        $prescriptionTesting->getPlannedDate()->format('d.m.Y')
                    );
                $this->notifier->notifyPatient(
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
                $entityManager->flush();
            }
            foreach ($prescription->getPrescriptionAppointments() as $prescriptionAppointment) {
                $notificationServiceBuilder = $this->notificationServiceBuilder
                    ->makeDoctorAppointmentNotification(
                        new NotificationData(
                            $this->getDoctrine()->getManager(),
                            $prescription->getMedicalHistory()->getPatient(),
                            $prescription->getMedicalHistory(),
                            $prescription->getMedicalRecord()
                        ),
                        AuthUserInfoService::getFIO(
                            $prescriptionAppointment->getPatientAppointment()->getStaff()->getAuthUser(),
                            true
                        ),
                        $prescriptionAppointment->getPlannedDateTime()->format('d.m.Y i:s')
                    );
                $this->notifier->notifyPatient(
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
                $entityManager->flush();
            }
        }
        return $this->redirectToMedicalHistory($prescription->getMedicalHistory()->getPatient());
    }

    /**
     * Generates and handles datatable of prescription testing list
     * @param Request $request
     * @param PrescriptionTestingDataTableService $prescriptionTestingDataTableService
     * @param Prescription $prescription
     * @param Patient $patient
     * @return DataTable
     * @throws Exception
     */
    public function generatePrescriptionTestingDataTable(
        Request $request,
        PrescriptionTestingDataTableService $prescriptionTestingDataTableService,
        Patient $patient,
        Prescription $prescription
    ): DataTable
    {
        $this->templateService
            ->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->setIsEnabled(false);
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionTestingDataTableService,
            $patient,
            $prescription,
            $prescriptionTestingDataTableService::ENTITY_CLASS,
            self::EDIT_PRESCRIPTION_TESTING_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of one prescription appointment
     * @param Request $request
     * @param PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService
     * @param Patient $patient
     * @param Prescription $prescription
     * @return DataTable
     * @throws Exception
     */
    public function generatePrescriptionAppointmentDataTable(
        Request $request,
        PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService,
        Patient $patient,
        Prescription $prescription
    ): DataTable
    {
        $this->templateService
            ->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->setIsEnabled(false);
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionAppointmentDataTableService,
            $patient,
            $prescription,
            $prescriptionAppointmentDataTableService::ENTITY_CLASS,
            self::EDIT_PRESCRIPTION_APPOINTMENT_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of one prescription medicine
     * @param Request $request
     * @param PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService
     * @param Patient $patient
     * @param Prescription $prescription
     * @return DataTable
     * @throws ReflectionException
     */
    public function generatePrescriptionMedicineDataTable(
        Request $request,
        PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService,
        Patient $patient,
        Prescription $prescription
    ): DataTable
    {
        $this->templateService
            ->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->setIsEnabled(false);
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionMedicineDataTableService,
            $patient,
            $prescription,
            $prescriptionMedicineDataTableService::ENTITY_CLASS,
            self::EDIT_PRESCRIPTION_MEDICINE_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of special prescription
     * @param Request $request
     * @param DataTableService $specialPrescriptionDatatableService
     * @param Patient $patient
     * @param Prescription $prescription
     * @param string $entityClassName
     * @param string|null $editRouteName
     * @return mixed
     * @throws ReflectionException
     */
    public function generateSpecialPrescriptionDatatable(
        Request $request,
        DataTableService $specialPrescriptionDatatableService,
        Patient $patient,
        Prescription $prescription,
        string $entityClassName,
        string $editRouteName = null
    )
    {
        return $specialPrescriptionDatatableService->getTable(
            function (
                string $id,
                $entity
            ) use ($editRouteName, $prescription, $patient, $entityClassName) {
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . 'tableActions.html.twig',
                    [
                        'template' => $this->templateService,
                        'route' => $editRouteName,
                        'parameters' => [
                            'patient' => $patient->getId(),
                            'prescription' => $prescription->getId(),
                            $this->getShortClassName($entityClassName) => $entity->getId(),
                        ]
                    ]
                )->getContent();
            },
            $this->templateService->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME),
            $prescription
        )->handleRequest($request);
    }

    /**
     * Returns short name of class with lower case first letter
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    public function getShortClassName(string $className): string
    {
        return lcfirst((new ReflectionClass($className))->getShortName());
    }
}