<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class AddingMedicationController
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class AddingMedicationController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctorOffice/common_template/';

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
     * New medication
     * @Route("doctor_office/patient/{id}/prescription/new/medication", name="adding_medication_by_doctor", methods={"GET","POST"})
     * @param Patient $patient
     */
    public function new(Patient $patient)
    {
        return $this->redirectToRoute($this->templateService->getRoute('show'));
    }
}