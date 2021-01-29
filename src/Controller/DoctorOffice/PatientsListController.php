<?php

namespace App\Controller\DoctorOffice;

use App\Repository\StaffRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientsListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientsWithNoProcessedListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientsWithNoResultsListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientsWithOpenedPrescriptionsListDataTableService;
use App\Services\DataTable\DoctorOffice\PatientsWithProcessedResultsListDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\DoctorOffice\PatientListTemplate;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Exception;
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
class PatientsListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/patients_list/';

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
     * List of patients
     * @Route("/patients", name="patients_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsListDataTableService $dataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     */
    public function list(
        Request $request,
        PatientsListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->isDoctorHospital()) {
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
                if (in_array('ROLE_DOCTOR_HOSPITAL', $this->getUser()->getRoles())) {
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
                }
            }
        );
    }

    /**
     * List of patients with not processed testings
     * @Route("/patients_with_no_processed", name="patients_with_no_processed_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsWithNoProcessedListDataTableService $dataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     * @throws Exception
     */
    public function patientsWithNoProcessedList(
        Request $request,
        PatientsWithNoProcessedListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->isDoctorHospital()) {
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
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список пациентов с необработанными анализами');
                if (in_array('ROLE_DOCTOR_HOSPITAL', $this->getUser()->getRoles())) {
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
                }
            }
        );
    }

    /**
     * List of patients without analysis results
     * @Route("/patients_with_no_results", name="patients_with_no_results_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsWithNoResultsListDataTableService $dataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     * @throws Exception
     */
    public function patientsWithNoResultsList(
        Request $request,
        PatientsWithNoResultsListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->isDoctorHospital()) {
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
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список пациентов без результатов анализов');
                if (in_array('ROLE_DOCTOR_HOSPITAL', $this->getUser()->getRoles())) {
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
                }
            }
        );
    }

    /**
     * List of patients with opened prescriptions
     * @Route("/patients_with_opened_prescriptions", name="patients_with_opened_prescriptions_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientsWithOpenedPrescriptionsListDataTableService $dataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     * @throws Exception
     */
    public function patientsWithOpenedPrescriptionsList(
        Request $request,
        PatientsWithOpenedPrescriptionsListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->isDoctorHospital()) {
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
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список пациентов с незакрытыми назначениями');
                if (in_array('ROLE_DOCTOR_HOSPITAL', $this->getUser()->getRoles())) {
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
                }
            }
        );
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
    public function patientsWithProcessedResultsList(
        Request $request,
        PatientsWithProcessedResultsListDataTableService $dataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if ($this->isDoctorHospital()) {
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
                $this->templateService->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
                    ->setContent('title', 'Список обработанных пациентов');
                if (in_array('ROLE_DOCTOR_HOSPITAL', $this->getUser()->getRoles())) {
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setIsEnabled(false);
                }
            }
        );
    }
}
