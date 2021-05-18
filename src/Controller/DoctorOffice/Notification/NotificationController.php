<?php

namespace App\Controller\DoctorOffice\Notification;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\ChannelType;
use App\Entity\Hospital;
use App\Entity\Notification;
use App\Entity\Patient;
use App\Repository\StaffRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\NotificationDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\DoctorOffice\NotificationTemplate;
use App\Services\TemplateItems\FilterTemplateItem;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Контроллеры сущности "Уведомление для кабинета врача"
 * @Route("/doctor_office/notification")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 */
class NotificationController extends DoctorOfficeAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'doctorOffice/patient_notification/';

    /**
     * NotificationController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new NotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список уведомлений
     * @Route("/", name="doctor_office_notification_list", methods={"GET", "POST"})
     * @param Request $request
     * @param NotificationDataTableService $notificationDataTableService
     * @param FilterService $filterService
     * @param StaffRepository $staffRepository
     * @return Response
     */
    public function list(
        Request $request,
        NotificationDataTableService $notificationDataTableService,
        FilterService $filterService,
        StaffRepository $staffRepository
    ): Response
    {
        if (AuthUserInfoService::isDoctorHospital($this->getUser())) {
            $options['hospital'] = $staffRepository->getStaff($this->getUser())->getHospital();
        }
        return $this->responseList(
            $request, $notificationDataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['CHANNEL_TYPE'], self::FILTER_LABELS['HOSPITAL']]
            ),
            $options ?? [],
            function () {
                $this->templateService
                    ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->setPath(self::TEMPLATE_PATH);
            },
            [
                'hospitalFilter' => $filterService->generateFilterName(
                'doctor_office_notification_list',
                Hospital::class
                ),
                'channelTypeFilter' => $filterService->generateFilterName(
                    'doctor_office_notification_list',
                    ChannelType::class
                ),
            ]
        );
    }

    /**
     * Notification info
     * @Route("/{id}", name="doctor_office_notification_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Notification $notification
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function show(Notification $notification, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $notification,
            [
                'templateParameterFilterName' => $filterService->generateFilterName(
                    'patient',
                    Patient::class
                )
            ]
        );
    }
}
