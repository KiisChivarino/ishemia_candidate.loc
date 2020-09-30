<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Notification;
use App\Form\Admin\NotificationType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\NotificationDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\TemplateBuilders\NotificationTemplate;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class NotificationController
 * @Route("/admin/notification")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class NotificationController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/notification/';

    /**
     * NotificationController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new NotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of notifications
     * @Route("/", name="notification_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param NotificationDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(Request $request, NotificationDataTableService $dataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['MEDICAL_HISTORY'],
                ]
            )
        );
    }

    /**
     * New notification
     * @Route("/new", name="notification_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        if ($request->query->get('medical_history_id')) {
            $medicalHistory = $this->getDoctrine()->getManager()->getRepository(MedicalHistory::class)->find($request->query->get('medical_history_id'));
        }
        if (!isset($medicalHistory) || !is_a($medicalHistory, MedicalHistory::class)) {
            $this->addFlash('warning', 'Прием пациента не может быть добавлен: история болезни не найдена!');
            return $this->redirectToRoute('medical_history_list');
        }
        return $this->responseNew(
            $request, (new Notification())->setMedicalHistory($medicalHistory), NotificationType::class, null, [],
            function (EntityActions $actions) use ($medicalHistory) {
                /** @var Notification $notification */
                $notification = $actions->getEntity();
                $notification->setMedicalRecord($this->getDoctrine()->getRepository(MedicalRecord::class)->getMedicalRecord($medicalHistory));
                $notification->setNotificationTime(new DateTime());
            }
        );
    }

    /**
     * Show notification
     * @Route("/{id}", name="notification_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Notification $notification
     *
     * @return Response
     */
    public function show(Notification $notification): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $notification, [
                'medicalHistoryTitle' => (new MedicalHistoryInfoService())->getMedicalHistoryTitle($notification->getMedicalHistory()),
                'medicalRecordTitle' => (new MedicalRecordInfoService())->getMedicalRecordTitle($notification->getMedicalRecord()),
                'staffFio' => (new AuthUserInfoService())->getFIO($notification->getStaff()->getAuthUser(), true),
            ]
        );
    }

    /**
     * Edit notification
     * @Route("/{id}/edit", name="notification_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Notification $notification
     *
     * @return Response
     */
    public function edit(Request $request, Notification $notification): Response
    {
        return $this->responseEdit($request, $notification, NotificationType::class);
    }

    /**
     * Delete notification
     * @Route("/{id}", name="notification_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Notification $notification
     *
     * @return Response
     */
    public function delete(Request $request, Notification $notification): Response
    {
        return $this->responseDelete($request, $notification);
    }
}
