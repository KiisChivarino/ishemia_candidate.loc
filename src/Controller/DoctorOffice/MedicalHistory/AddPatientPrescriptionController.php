<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Services\ControllerGetters\EntityActions;
use App\Services\EntityActions\Creator\PrescriptionCreatorService;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use Exception;
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
class AddPatientPrescriptionController extends DoctorOfficeAbstractController
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
     * @Route("patient/{id}/prescription/new", name="adding_prescriprion_by_doctor", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param PrescriptionCreatorService $prescriptionCreatorService
     * @return Response
     * @throws Exception
     */
    public function new(
        Patient $patient,
        PrescriptionCreatorService $prescriptionCreatorService
    ): Response
    {
        $this->templateService->new();
        $entityManager = $this->getDoctrine()->getManager();
        $staff = $this->getStaff($patient);
        $medicalHistory = $entityManager->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $prescriptionCreatorService->before(
            [
                PrescriptionCreatorService::MEDICAL_HISTORY_OPTION => $medicalHistory,
                PrescriptionCreatorService::STAFF_OPTION => $staff,
            ]
        );
        $prescriptionCreatorService->after(
            new EntityActions(
                $prescriptionCreatorService->getEntity(),
                null,
                $entityManager
            )
        );
        $this->flushToMedicalHistory($patient);
        $this->setLogCreate($prescriptionCreatorService->getEntity());
        return $this->redirectToRoute(
            'add_prescription_show', [
                'id' => $patient->getId(),
            ]
        );
    }

    /**
     * Show prescription
     * @Route("patient/{id}/prescription/show", name="add_prescription_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Patient $patient
     * @return Response
     */
    public function show(Patient $patient): Response
    {
        $this->templateService->show($patient);
        return $this->render(
            self::TEMPLATE_PATH . 'prescription_show.html.twig',
            [
                'patient' => $patient,
            ]
        );
    }
}