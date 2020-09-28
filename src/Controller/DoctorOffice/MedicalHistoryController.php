<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Services\TemplateBuilders\PatientOffice\MedicalHistoryTemplate;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicalHistoryController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * @Route("/{id}/medical_history", name="medical_history_show", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     *
     * @return Response
     */
    public function main(Patient $patient): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            []
        );
    }
}