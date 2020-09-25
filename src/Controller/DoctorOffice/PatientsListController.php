<?php

namespace App\Controller\DoctorOffice;

use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientsListDataTableService;
use App\Services\TemplateBuilders\PatientListTemplate;
use App\Services\FilterService\FilterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class PatientsListController
 *
 * @package App\Controller\DoctorOffice
 */
class PatientsListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patients_list/';

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patients
     * @Route("/doctor_office/patients", name="patients_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsListDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(Request $request, PatientsListDataTableService $dataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['HOSPITAL'],]
            )
        );
    }
}