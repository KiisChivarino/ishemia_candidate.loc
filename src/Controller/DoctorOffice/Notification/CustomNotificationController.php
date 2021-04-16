<?php

namespace App\Controller\DoctorOffice\Notification;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Notification;
use App\Entity\Patient;
use App\Form\Doctor\CustomNotificationType;
use App\Repository\MedicalHistoryRepository;
use App\Services\LoggerService\LogService;
use App\Services\Notification\NotificationData;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use App\Services\TemplateBuilders\DoctorOffice\CustomNotificationTemplate;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\DBAL\DBALException;
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
    /** @var string Путь к папке твиг шаблонов */
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
     * CustomNotificationController constructor.
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
     * Creates new custom notification for patient
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
        $entity = new Notification();
        $formName = self::RESPONSE_FORM_TYPE_EDIT;
        $form = $this->createForm(
            CustomNotificationType::class, $entity,
            array_merge([], [self::FORM_TEMPLATE_ITEM_OPTION_TITLE =>
                $this->templateService->edit()->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),])
        );
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.invalid_handle_request')
            );
            return $this->render(
                $this->templateService->getTemplateFullName(
                    $formName,
                    $this->getParameter('kernel.project_dir')),
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'filters' =>
                        $this->templateService
                            ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                            ->getFiltersViews(),
                ]
            );
        }
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $notificationServiceBuilder = $this->notificationServiceBuilder
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
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
                /** @noinspection PhpParamsInspection */
                (new LogService($entityManager))
                    ->setUser($this->getUser())
                    ->setDescription(
                        $this->translator->trans(
                            'log.new.entity',
                            [
                                '%entity%' => 'Уведомление',
                                '%id%' => $entity->getId(),
                            ]
                        )
                    )
                    ->logCreateEvent();
                $entityManager->flush();
            } catch (DBALException $e) {
                $this->addFlash('error', $this->translator->trans('app_controller.error.post_dbal_exception'));
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . $formName . '.html.twig',
                    [
                        'entity' => $entity,
                        'form' => $form->createView(),
                    ]
                );
            } catch (Exception $e) {
                $this->addFlash('error', $this->translator->trans('app_controller.error.exception'));
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . $formName . '.html.twig',
                    [
                        'entity' => $entity,
                        'form' => $form->createView(),
                    ]
                );
            }
            $this->addFlash('success', $this->translator->trans('app_controller.success.success_post'));
            return $this->redirectToRoute(
                'notifications_list',
                [
                    'id' => $patient->getId()
                ]
            );

        }
        return $this->render(
            $this->templateService->getTemplateFullName(
                $formName,
                $this->getParameter('kernel.project_dir')),
            [
                'entity' => $entity,
                'form' => $form->createView(),
                'filters' =>
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                        ->getFiltersViews(),
            ]
        );
    }
}
