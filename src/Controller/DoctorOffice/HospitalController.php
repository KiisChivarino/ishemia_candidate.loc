<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Hospital;
use App\Services\DataTable\DoctorOffice\HospitalDataTableService;
use App\Services\TemplateBuilders\DoctorOffice\HospitalTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class HospitalController
 * Обработка роутов сущности Hospital для кабинета врача
 * @Route("/doctor_office/hospital")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class HospitalController extends DoctorOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'doctorOffice/hospital/';

    /**
     * HospitalController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new HospitalTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список больниц
     * @Route("/", name="doctor_office_hospital_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param HospitalDataTableService $dataTableService
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, HospitalDataTableService $dataTableService): Response
    {
        return $this->responseList(
            $request, $dataTableService
        );
    }

    /**
     * Информация о больнице
     * @Route("/{id}", name="doctor_office_hospital_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Hospital $hospital
     *
     * @return Response
     * @throws Exception
     */
    public function show(Hospital $hospital): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $hospital);
    }
}
