<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Services\DataTable\DataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionAppointmentDataTableService;
use App\Services\DataTable\DoctorOffice\PrescriptionTestingDataTableService;
use App\Services\EntityActions\Creator\PrescriptionCreatorService;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Omines\DataTablesBundle\DataTable;
use ReflectionClass;
use ReflectionException;
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

    /**
     * PatientPrescriptionController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->templateService = new AddPatientPrescriptionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
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
     * @return Response
     * @throws Exception
     */
    public function new(
        Patient $patient
    ): Response
    {
        $this->templateService->new();
        $entityManager = $this->getDoctrine()->getManager();
        $staff = $this->getStaff($patient);
        $medicalHistory = $entityManager->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $prescriptionCreatorService = new PrescriptionCreatorService($entityManager);
        $prescriptionCreatorService->execute(
            [
                PrescriptionCreatorService::MEDICAL_HISTORY_OPTION => $medicalHistory,
                PrescriptionCreatorService::STAFF_OPTION => $staff,
            ]
        );
        $this->flushToMedicalHistory($patient);
        $this->setLogCreate($prescriptionCreatorService->getEntity());
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
     *     requirements={"patient"="\d+"}
     *     )
     * @param Patient $patient
     * @param Prescription $prescription
     * @param Request $request
     * @param PrescriptionTestingDataTableService $prescriptionTestingDataTableService
     * @param PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService
     * @return Response
     * @throws Exception
     */
    public function show(
        Patient $patient,
        Prescription $prescription,
        Request $request,
        PrescriptionTestingDataTableService $prescriptionTestingDataTableService,
        PrescriptionAppointmentDataTableService $prescriptionAppointmentDataTableService
    ): Response
    {
        $this->templateService->show($patient);
        $prescriptionTestingTable = $this->generatePrescriptionTestingDataTable(
            $request,
            $prescriptionTestingDataTableService,
            $patient,
            $prescription
        );
        if (
        $prescriptionTestingTable->isCallback()
        ) {
            return $prescriptionTestingTable->getResponse();
        }
        $prescriptionAppointmentTable = $this->generatePrescriptionAppointmentDataTable(
            $request,
            $prescriptionAppointmentDataTableService,
            $patient,
            $prescription
        );
        if (
        $prescriptionAppointmentTable->isCallback()
        ) {
            return $prescriptionAppointmentTable->getResponse();
        }
        return $this->render(
            self::TEMPLATE_PATH . 'prescription_show.html.twig',
            [
                'prescriptionTestingTable' => $prescriptionTestingTable,
                'prescriptionAppointmentTable' => $prescriptionAppointmentTable,
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
     *     requirements={"patient"="\d+"}
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
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionTestingDataTableService,
            $patient,
            $prescription,
            $prescriptionTestingDataTableService::ENTITY_CLASS
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
        return $this->generateSpecialPrescriptionDatatable(
            $request,
            $prescriptionAppointmentDataTableService,
            $patient,
            $prescription,
            $prescriptionAppointmentDataTableService::ENTITY_CLASS
        );
    }

    /**
     * Generates and handles datatable of special prescription
     * @param Request $request
     * @param DataTableService $specialPrescriptionDatatableService
     * @param Patient $patient
     * @param Prescription $prescription
     * @param string $entityClassName
     * @return mixed
     * @throws ReflectionException
     */
    public function generateSpecialPrescriptionDatatable(
        Request $request,
        DataTableService $specialPrescriptionDatatableService,
        Patient $patient,
        Prescription $prescription,
        string $entityClassName
    )
    {
        return $specialPrescriptionDatatableService->getTable(
            function (
                string $id,
                $entity
            ) use ($prescription, $patient, $entityClassName) {
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . 'tableActions.html.twig',
                    [
                        'template' => $this->templateService,
                        'parameters' => [
                            'patient' => $patient->getId(),
                            'prescription' => $prescription->getId(),
                            $this->getShortClassName($entityClassName) => $entity,
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