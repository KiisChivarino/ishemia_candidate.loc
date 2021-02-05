<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Form\Admin\PrescriptionType;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientPrescriptionController
 * @Route("/doctor_office/prescription/")
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
        $this->templateService = new CreateNewPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * New prescription
     * @Route("{id}/new", name="prescription_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request
    ): Response
    {
        return $this->responseNewWithActions(
            $request,
            PrescriptionType::class,
            [
                'medicalHistory' => $this->getMedicalHistoryByParameter($request),
            ]
        );
    }

    /**
     * Show prescription
     * @Route("{id}/show", name="add_prescription_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Patient $patient
     * @return Response
     */
    public function show(Patient $patient){
        $this->templateService->show($patient);
        return $this->render(
            self::TEMPLATE_PATH . 'prescription_show.html.twig',
            [
                'patient' => $patient,
            ]
        );
    }
}