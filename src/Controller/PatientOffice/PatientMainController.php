<?php

namespace App\Controller\PatientOffice;

use App\Repository\PatientRepository;
use App\Services\TemplateBuilders\PatientOffice\PatientMainTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientMainController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office")
 */
class PatientMainController extends PatientOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'patientOffice/main/';

    /**
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientMainTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Main page of patient office
     * @Route("/", name="patient_office_main")
     * @throws Exception
     */
    public function index(PatientRepository $patientRepository): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patientRepository->getPatientByAuthUser($this->getUser())
        );
    }
}