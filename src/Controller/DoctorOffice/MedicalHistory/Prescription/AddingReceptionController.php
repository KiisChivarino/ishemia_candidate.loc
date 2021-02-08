<?php

namespace App\Controller\DoctorOffice\MedicalHistory\Prescription;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Services\TemplateBuilders\DoctorOffice\AddPatientPrescriptionTemplate;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class AddingReceptionController
 * @package App\Controller\DoctorOffice\MedicalHistory\Prescription
 */
class AddingReceptionController extends DoctorOfficeAbstractController
{
    /** @var string Path to custom template directory */
    const TEMPLATE_PATH = 'doctor_office/common_template/';

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
     * @Route("doctor_office/patient/{id}/prescription/new/reception", name="adding_reception_by_doctor", methods={"GET","POST"})
     */
    public function new()
    {
        return $this->redirectToRoute($this->templateService->getRoute('show'));
    }
}