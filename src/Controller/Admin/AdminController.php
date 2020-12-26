<?php

namespace App\Controller\Admin;

use App\Repository\PatientRepository;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Michelf\Markdown;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
{
    /** @var KernelInterface  */
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
     * @return Response
     */
    public function testNotification(PatientRepository $patientRepository): Response
    {
        $notificationService = $this->notificationServiceBuilder
            ->setPatient($patientRepository->findAll()[0])
            ->setMedicalHistory($patientRepository->findAll()[0]->getMedicalHistories()[0])
            ->setMedicalRecord($patientRepository->findAll()[0]->getMedicalHistories()[0]->getMedicalRecords()[0])
            ->makeConfirmMedicationNotification()
        ;
        $this->notifier->notifyPatient(
            $notificationService[0],
            $notificationService[1],
            $notificationService[2]
        );
        $this->getDoctrine()->getManager()->flush();
        return new Response(true);
    }
}