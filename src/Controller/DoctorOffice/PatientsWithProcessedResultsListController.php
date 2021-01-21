<?php

namespace App\Controller\DoctorOffice;

use App\Repository\StaffRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientsWithProcessedResultsListDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\DoctorOffice\PatientListTemplate;
use App\Services\TemplateItems\FilterTemplateItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientsListController
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class PatientsWithProcessedResultsListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patients_list/patientsWithProcessedResultsList/';

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patients with processed results
     * @Route("/patients_with_processed_results", name="patients_with_processed_results_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsWithProcessedResultsListDataTableService $dataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     */
    public function list(
        Request $request,
        PatientsWithProcessedResultsListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_DOCTOR_HOSPITAL')) {
            $options['hospital'] = $staffRepository->getStaff($this->getUser())->getHospital();
        }
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['HOSPITAL'],]
            ),
            $options ?? [],
            function () {
                $this->templateService
                    ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
            }
        );
    }
}
