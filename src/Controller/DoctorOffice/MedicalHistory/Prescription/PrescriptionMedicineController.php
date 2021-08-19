<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Form\PatientMedicineType\PatientMedicineType;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Creator\DoctorOfficePrescriptionMedicineCreatorService;
use App\Services\EntityActions\Creator\PatientMedicineCreatorService;
use App\Services\EntityActions\Creator\PrescriptionMedicineCreatorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Form\PrescriptionMedicineType\PrescriptionMedicineType;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PatientMedicineTemplate;
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
 * Class AddingMedicationController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class PrescriptionMedicineController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/prescription_medicine/';

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
        $this->templateService = new PatientMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * New medicine prescription
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/prescription_medicine/new",
     *     name="prescription_patient_medicine_new",
     *     methods={"GET","POST"},
     *     requirements={"prescription"="\d+"}
     *     )
     * @param Request $request
     * @param Prescription $prescription
     * @param Patient $patient
     * @param DoctorOfficePrescriptionMedicineCreatorService $prescriptionMedicineCreatorService
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function new(
        Request $request,
        Prescription $prescription,
        Patient $patient,
        DoctorOfficePrescriptionMedicineCreatorService $prescriptionMedicineCreatorService,
        PatientMedicineCreatorService $patientMedicineCreatorService
    ): Response
    {
        $patientMedicine = $patientMedicineCreatorService->execute()->getEntity();
        $prescriptionMedicineCreatorService->before(
            [
                PrescriptionMedicineCreatorService::PRESCRIPTION_OPTION => $prescription,
                PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine
            ]
        );
        $this->redirectToAddPrescriptionPage($patient, $prescription);
        return $this->responseNewMultiFormWithActions(
            $request,
            [
                new CreatorEntityActionsBuilder(
                    $prescriptionMedicineCreatorService,
                    [],
                    function () use ($patientMedicine, $patient): array {
                        return [
                            PrescriptionMedicineCreatorService::STAFF_OPTION => $this->getStaff($patient),
                            PrescriptionMedicineCreatorService::PATIENT_MEDICINE_OPTION => $patientMedicine,
                        ];
                    }
                )
            ],
            [
                new FormData(PrescriptionMedicineType::class, $prescriptionMedicineCreatorService->getEntity()),
                new FormData(PatientMedicineType::class, $patientMedicine),
            ]
        );
    }

    /**
     * Show prescription testing
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/prescription_medicine/{prescriptionMedicine}/show/",
     *     name="show_prescription_medicine_by_doctor",
     *     )
     * @param PrescriptionMedicine $prescriptionMedicine
     * @return Response
     * @throws Exception
     */
    public function show(
        PrescriptionMedicine $prescriptionMedicine
    ): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $prescriptionMedicine,
            [
                'staffTitle' =>
                    AuthUserInfoService::getFIO($prescriptionMedicine->getStaff()->getAuthUser()),
            ]
        );
    }

    /**
     * Edit prescription medicine
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/prescription_medicine/{prescriptionMedicine}/edit/",
     *     name="edit_prescription_medicine_by_doctor",
     *     methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    public function edit(
        Request $request,
        PrescriptionMedicine $prescriptionMedicine
    ): Response
    {
        $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $prescriptionMedicine->getPrescription()->getMedicalHistory()->getPatient(),
                'prescription' => $prescriptionMedicine->getPrescription()
            ]
        );
        return $this->responseEditMultiForm(
            $request,
            $prescriptionMedicine,
            [
                new FormData(
                    PrescriptionMedicineType::class,
                    $prescriptionMedicine
                ),
                new FormData(
                    PatientMedicineType::class,
                    $prescriptionMedicine->getPatientMedicine()
                ),
            ]
        );
    }

    /**
     * Delete prescription appointment
     * @Route(
     *     "/patient/{patient}/prescription/{prescription}/prescription_medicine/{prescriptionMedicine}/delete",
     *     name="delete_prescription_medicine_by_doctor",
     *     methods={"DELETE"},
     *     )
     * @param Request $request
     * @param PrescriptionMedicine $prescriptionMedicine
     * @param Patient $patient
     * @param Prescription $prescription
     * @return Response
     * @throws Exception
     */
    public function delete(
        Request $request,
        PrescriptionMedicine $prescriptionMedicine,
        Patient $patient,
        Prescription $prescription
    ): Response
    {
        $this->redirectToAddPrescriptionPage($patient, $prescription);
        return $this->responseDelete($request, $prescriptionMedicine);
    }
}