<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateBuilders\DoctorOffice\MedicalHistoryTemplate;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MedicalHistoryController
 *
 * @package App\Controller\DoctorOffice
 */
class MedicalHistoryController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    private $authUserInfoService;

    private $patientInfoService;

    /**
     * MedicalHistoryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param AuthUserInfoService $authUserInfoService
     * @param PatientInfoService $patientInfoService
     */
    public function __construct(Environment $twig, RouterInterface $router, AuthUserInfoService $authUserInfoService, PatientInfoService $patientInfoService)
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->authUserInfoService = $authUserInfoService;
        $this->patientInfoService = $patientInfoService;
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
            [
                'age' => $this->patientInfoService->getAge($patient),
            ]
        );
    }
}