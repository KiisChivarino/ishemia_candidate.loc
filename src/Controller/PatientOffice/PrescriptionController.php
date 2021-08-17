<?php

namespace App\Controller\PatientOffice;

use App\Services\TemplateBuilders\PatientOffice\PrescriptionTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientPrescriptionController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office/prescription")
 */
class PrescriptionController extends PatientOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'patientOffice/prescription/';

    /**
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PrescriptionTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * News patient prescription
     * @Route("/news", name="patient_office_prescription")
     * @throws Exception
     */
    public function news(): Response
    {
        return $this->responseNewsList(self::TEMPLATE_PATH);
    }

    /**
     * history patient prescription
     * @Route("/history", name="patient_office_prescription_history")
     * @throws Exception
     */
    public function history(): Response
    {
        return $this->responseHistoryList(self::TEMPLATE_PATH);
    }
}