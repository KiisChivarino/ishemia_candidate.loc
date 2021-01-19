<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Notification;
use App\Entity\Patient;
use App\Form\Doctor\CustomNotificationType;
use App\Repository\MedicalHistoryRepository;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\DoctorOffice\NotificationsListDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\Notification\NotificationData;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use App\Services\TemplateBuilders\DoctorOffice\CustomNotificationTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class CustomNotificationController
 * @route ("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class CustomNotificationController extends DoctorOfficeAbstractController
{
    /** @var string */
    private const EDIT_PERSONAL_DATA_TEMPLATE_NAME = 'create_notification';

    /** @var string */
    const TEMPLATE_PATH = 'doctorOffice/notification/';

    /**
     * @var NotifierService
     */
    private $notifier;

    /**
     * @var NotificationsServiceBuilder
     */
    private $notificationServiceBuilder;

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param NotifierService $notifier
     * @param NotificationsServiceBuilder $notificationServiceBuilder
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        NotifierService $notifier,
        NotificationsServiceBuilder $notificationServiceBuilder)
    {
        parent::__construct($translator);
        $this->templateService = new CustomNotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->notifier = $notifier;
        $this->notificationServiceBuilder = $notificationServiceBuilder;
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
        return $this->responseList(
            $request, $notificationsListDataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['NOTIFICATION'],]
            ),
            ['patient' => $patient]
        );
    }

    /**
     * Edit personal data of patient medical history
     * @Route(
     *     "/{id}/notifications/create_notification",
     *     name="doctor_create_notification",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return Response
     * @throws Exception
     */
    public function createNotification(
        Request $request,
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository
    ): Response
    {
        $notification = new Notification();
        return $this->responseEdit(
            $request,
            $notification,
            CustomNotificationType::class,
            [],
            function () use ($request, $patient, $medicalHistoryRepository) {
                $notificationService = $this->notificationServiceBuilder
                    ->makeCustomMessageNotification(
                        (
                        new NotificationData(
                            $this->getDoctrine()->getManager(),
                            $patient,
                            $medicalHistoryRepository->getCurrentMedicalHistory($patient)
                        )
                        ),
                        $request->request->get('custom_notification')['text']
                    );

                $this->notifier->notifyPatient(
                    $notificationService->getWebNotificationService(),
                    $notificationService->getSMSNotificationService(),
                    $notificationService->getEmailNotificationService()
                );
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Сообщение пациенту отправлено!');
                return $this->redirectToRoute('notifications_list', [
                    'id' => $patient->getId()
                ]);
            },
            self::EDIT_PERSONAL_DATA_TEMPLATE_NAME
        );
    }
}
