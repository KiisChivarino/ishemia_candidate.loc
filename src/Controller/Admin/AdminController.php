<?php

namespace App\Controller\Admin;

use App\Repository\PatientRepository;
use App\Services\Notification\EmailNotificationService;
use App\Services\Notification\SMSNotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Michelf\Markdown;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
{
    /** @var KernelInterface  */
    private $appKernel;

    /**
     * @var SMSNotificationService
     */
    private $sms;

    /**
     * @var EmailNotificationService
     */
    private $email;

    /**
     * AdminController constructor.
     * @param KernelInterface $appKernel
     * @param SMSNotificationService $sms
     * @param EmailNotificationService $emailNotificationService
     */
    public function __construct(KernelInterface $appKernel, SMSNotificationService $sms, EmailNotificationService $emailNotificationService)
    {
        $this->sms = $sms;
        $this->appKernel = $appKernel;
        $this->email = $emailNotificationService;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function index()
    {
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => Markdown::defaultTransform(file_get_contents($this->appKernel->getProjectDir() . '/data/documents/changes.md'))
            ]
        );
    }

    /**
     * @Route("/admin/testemail", name="testemail")
     * @param PatientRepository $patientRepository
     * @return Response
     */
    public function testEmail(PatientRepository $patientRepository)
    {
        try {
            $this->email
                ->setPatient($patientRepository->findAll()[0])
                ->setHeader('Ура, а вот и вы!')
                ->setContent('Ну здраствуй...')
                ->setButtonText('Перейти на сайт')
                ->setButtonLink('http://shemia.test')
                ->sendDefaultEmail();
        } catch (\ErrorException $e) {
            // TODO: Написать кэтч
        } catch (LoaderError $e) {
            // TODO: Написать кэтч
        } catch (RuntimeError $e) {
            // TODO: Написать кэтч
        } catch (SyntaxError $e) {
            // TODO: Написать кэтч
        }

        return new Response(true);
    }

    /**
     * @Route("/admin/testsms", name="testsms")
     * @return Response
     */
    public function testSMS()
    {
        header("Content-Type: text/xml; charset=UTF-8");
        $sms = $this->sms
            ->setText('Тестовое СМС')
            ->setTarget('0000000000')
//            ->setTarget('9611672720')
            ->sendSMS();
//        $this->sms->checkSMS();
        return new Response(true);
    }
}