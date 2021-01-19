<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\NotificationsListDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\DoctorOffice\NotificationsListTemplate;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class NotificationListController
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class NotificationListController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/notifications_list/';

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
        $this->templateService = new NotificationsListTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patients
     * @Route("/patient/{id}/notifications/", name="notifications_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param NotificationsListDataTableService $notificationsListDataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(
        Patient $patient,
        Request $request,
        NotificationsListDataTableService $notificationsListDataTableService,
        FilterService $filterService
    ): Response
    {
        $filterLabels = (new FilterLabels($filterService))->setFilterLabelsArray(
            [self::FILTER_LABELS['NOTIFICATION'],]
        );
        $template = $this->templateService->list($filterLabels ? $filterLabels->getFilterService() : null);
        if ($filterLabels) {
            $filters = $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray());
        }
        $table = $notificationsListDataTableService->getTable(
            $this->renderTableActions(),
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME),
            $filters ?? null,
            ['patient' => $patient]
        );
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render(
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->getPath() . 'list.html.twig',
            [
                'datatable' => $table,
                'filters' => $template
                    ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getFiltersViews(),
                'patientId' => $patient->getId()
            ]
        );
    }
}
