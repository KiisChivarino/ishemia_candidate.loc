<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Entity\PrescriptionMedicine;
use App\Entity\PrescriptionTesting;
use App\Repository\MedicalHistoryRepository;
use App\Services\CompletePrescription\CompletePrescriptionService;
use App\Services\DataTable\DataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionAppointmentDataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionMedicineDataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionTestingDataTableService;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionService;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Creator\PrescriptionCreatorService;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
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

    /** @var string Route name for delete prescription appointment */
    const DELETE_PRESCRIPTION_APPOINTMENT_ROUTE_NAME = 'delete_prescription_appointment_by_doctor';

    /** @var string Route name for delete prescription testing */
    const DELETE_PRESCRIPTION_TESTING_ROUTE_NAME = 'delete_prescription_testing_by_doctor';

    /** @var string Route name for delete prescription appointment */
    const DELETE_PRESCRIPTION_MEDICINE_ROUTE_NAME = 'delete_prescription_medicine_by_doctor';

    /** @var string Route name for show prescription appointment */
    const SHOW_PRESCRIPTION_APPOINTMENT_ROUTE_NAME = 'show_prescription_appointment_by_doctor';

    /** @var string Route name for show prescription testing */
    const SHOW_PRESCRIPTION_TESTING_ROUTE_NAME = 'show_prescription_testing_by_doctor';

    /** @var string Route name for show prescription appointment */
    const SHOW_PRESCRIPTION_MEDICINE_ROUTE_NAME = 'show_prescription_medicine_by_doctor';

    /**
     * @var CompletePrescriptionService
     */
    private $completePrescriptionService;

    /**
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param CompletePrescriptionService $completePrescriptionService
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        CompletePrescriptionService $completePrescriptionService
    )
    {
        parent::__construct($translator);
        $this->templateService = new AddPatientPrescriptionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->completePrescriptionService = $completePrescriptionService;
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
        $prescriptionEntity = $prescriptionCreatorService->getEntity();
        $this->setLogCreate($prescriptionEntity);
        if (!$this->flush()) {
            $this->redirectToMedicalHistory($patient);
        }

        return $this->redirectToRoute(
            'add_prescription_show', [
                'patient' => $patient->getId(),
                'prescription' => $prescriptionEntity->getId()
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
        $this->templateService->show($prescription);
        $prescriptionTestingTable = $this->generatePrescriptionTestingDataTable(
            $request,
            $prescriptionTestingDataTableService,
            $prescription
        );
        if ($prescriptionTestingTable->isCallback()) {
            return $prescriptionTestingTable->getResponse();
        }
        $prescriptionAppointmentTable = $this->generatePrescriptionAppointmentDataTable(
            $request,
            $prescriptionAppointmentDataTableService,
            $prescription
        );
        if ($prescriptionAppointmentTable->isCallback()) {
            return $prescriptionAppointmentTable->getResponse();
        }

        $prescriptionMedicineTable = $this->generatePrescriptionMedicineDataTable(
            $request,
            $prescriptionMedicineDataTableService,
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
     * Generates and handles datatable of prescription testing list
     * @param Request $request
     * @param PrescriptionTestingDataTableService $prescriptionTestingDataTableService
     * @param Prescription $prescription
     * @return DataTable
     * @throws ReflectionException
     */
    public function generatePrescriptionTestingDataTable(
        Request $request,
        PrescriptionTestingDataTableService $prescriptionTestingDataTableService,
        Prescription $prescription
    ): DataTable
    {
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionTestingDataTableService,
            $prescription,
            PrescriptionTesting::class,
            self::SHOW_PRESCRIPTION_TESTING_ROUTE_NAME,
            self::EDIT_PRESCRIPTION_TESTING_ROUTE_NAME,
            self::DELETE_PRESCRIPTION_TESTING_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of one prescription appointment
     * @param Request $request
     * @param PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService
     * @param Prescription $prescription
     * @return DataTable
     * @throws ReflectionException
     */
    public function generatePrescriptionAppointmentDataTable(
        Request $request,
        PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService,
        Prescription $prescription
    ): DataTable
    {
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionAppointmentDataTableService,
            $prescription,
            PrescriptionAppointment::class,
            self::SHOW_PRESCRIPTION_APPOINTMENT_ROUTE_NAME,
            self::EDIT_PRESCRIPTION_APPOINTMENT_ROUTE_NAME,
            self::DELETE_PRESCRIPTION_APPOINTMENT_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of one prescription medicine
     * @param Request $request
     * @param PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService
     * @param Prescription $prescription
     * @return DataTable
     * @throws ReflectionException
     */
    public function generatePrescriptionMedicineDataTable(
        Request $request,
        PrescriptionMedicineDataTableService $prescriptionMedicineDataTableService,
        Prescription $prescription
    ): DataTable
    {
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionMedicineDataTableService,
            $prescription,
            PrescriptionMedicine::class,
            self::SHOW_PRESCRIPTION_MEDICINE_ROUTE_NAME,
            self::EDIT_PRESCRIPTION_MEDICINE_ROUTE_NAME,
            self::DELETE_PRESCRIPTION_MEDICINE_ROUTE_NAME
        );
    }

    /**
     * Generates and handles datatable of special prescription
     * @param Request $request
     * @param DataTableService $specialPrescriptionDatatableService
     * @param Prescription $prescription
     * @param string $entityClassName
     * @param string $showRouteName
     * @param string $editRouteName
     * @param string $deleteRouteName
     * @return DataTable
     * @throws ReflectionException
     */
    public function generateSpecialPrescriptionDatatable(
        Request $request,
        DataTableService $specialPrescriptionDatatableService,
        Prescription $prescription,
        string $entityClassName,
        string $showRouteName,
        string $editRouteName,
        string $deleteRouteName
    ): DataTable
    {
         return $specialPrescriptionDatatableService->getTable(
            function (string $id, $entity)
            use ($entityClassName, $prescription, $showRouteName, $editRouteName, $deleteRouteName)
            {
                $routeParams = [
                    'patient' => $prescription->getMedicalHistory()->getPatient()->getId(),
                    'prescription' => $prescription->getId(),
                    $this->getShortClassName($entityClassName) => $entity->getId()
                ];
                $this->templateService->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->getTemplateItemRoute()
                    ->setRouteName($showRouteName)
                    ->setRouteParams($routeParams);
                $this->templateService->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)->getTemplateItemRoute()
                    ->setRouteName($editRouteName)
                    ->setRouteParams($routeParams);
                $this->templateService->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->getTemplateItemRoute()
                    ->setRouteName($deleteRouteName)
                    ->setRouteParams($routeParams);
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . 'tableActions.html.twig',
                    [
                        'template' => $this->templateService,
                        'id' => $id,
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
        $this->completePrescriptionService->completePrescription($prescription, $medicalRecordCreatorService);
        return $this->redirectToMedicalHistory($prescription->getMedicalHistory()->getPatient());
    }
}