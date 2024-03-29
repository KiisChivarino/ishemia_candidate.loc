<?php

namespace App\Controller\DoctorOffice\Notification;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Notification;
use App\Entity\Patient;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\PatientNotificationListDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\DoctorOffice\PatientNotificationsTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientNotificationController
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class PatientNotificationController extends DoctorOfficeAbstractController
{
    /** @var string Путь к папке twig шаблонов */
    const TEMPLATE_PATH = 'doctorOffice/notification/';

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
        $this->templateService = new PatientNotificationsTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient notifications
     * @Route("/patient/{id}/notification/list", name="notifications_list", methods={"GET","POST"})
     *
     * @param Patient $patient
     * @param Request $request
     * @param PatientNotificationListDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Patient $patient,
        Request $request,
        PatientNotificationListDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['NOTIFICATION'],]
            ),
            ['patient' => $patient]
        );
    }

    /**
     * Notification info
     * @Route("/patient/{patient}/notification/{notification}/show", name="doctor_office_patient_notification_show", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @param Notification $notification
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function show(Notification $notification, FilterService $filterService): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $notification);
    }
}
