<?php

namespace App\Controller\Admin;

use App\Entity\PrescriptionMedicine;
use App\Repository\PatientRepository;
use App\Repository\PrescriptionMedicineRepository;
use App\Services\Notification\NotificationData;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use Michelf\Markdown;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
{
    /** @var KernelInterface */
    private $appKernel;

    /** @var NotifierService */
    private $notifier;

    /** @var NotificationsServiceBuilder */
    private $notificationServiceBuilder;

    /**
     * AdminController constructor.
     * @param KernelInterface $appKernel
     * @param NotifierService $notifier
     * @param TranslatorInterface $translator
     * @param NotificationsServiceBuilder $notificationServiceBuilder
     */
    public function __construct(
        KernelInterface $appKernel,
        NotifierService $notifier,
        TranslatorInterface $translator,
        NotificationsServiceBuilder $notificationServiceBuilder
    )
    {
        parent::__construct($translator);
        $this->appKernel = $appKernel;
        $this->notifier = $notifier;
        $this->notificationServiceBuilder = $notificationServiceBuilder;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => Markdown::defaultTransform(file_get_contents($this->appKernel->getProjectDir() .
                    '/data/documents/changes.md'))
            ]
        );
    }

    /**
     * @Route("/testNotification", name="testNotification")
     * @param PatientRepository $patientRepository
     * @param PrescriptionMedicineRepository $prescriptionMedicineRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testNotification(PatientRepository $patientRepository, PrescriptionMedicineRepository $prescriptionMedicineRepository): Response
    {
        $notificationService = $this->notificationServiceBuilder
            ->setPrescriptionMedicine($prescriptionMedicineRepository->find(1))
            ->makeConfirmMedicationNotification(
                (
                new NotificationData(
                    $this->getDoctrine()->getManager(),
                    $patientRepository->findAll()[0],
                    $patientRepository->findAll()[0]->getMedicalHistories()[0],
                    $patientRepository->findAll()[0]->getMedicalHistories()[0]->getMedicalRecords()[0])
                ),
                'Конфеты "Каракум" 2 раза в день по 2 штуки'
            );

        $this->notifier->notifyPatient(
            $notificationService->getWebNotificationService(),
            $notificationService->getSMSNotificationService(),
            $notificationService->getEmailNotificationService()
        );
        $this->getDoctrine()->getManager()->flush();
        return new Response(true);
    }
}