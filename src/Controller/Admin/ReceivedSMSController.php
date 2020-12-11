<?php

namespace App\Controller\Admin;

use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
use App\Entity\ReceivedSMS;
use App\Form\Admin\LogActionType;
use App\Form\Admin\ReceivedSMSType;
use App\Repository\ReceivedSMSRepository;
use App\Services\DataTable\Admin\LogDataTableService;
use App\Services\DataTable\Admin\SMSDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\LogTemplate;
use App\Services\TemplateBuilders\Admin\ReceivedSMSTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "ReceivedSMS"
 * @Route("/admin/sms")
 * @IsGranted("ROLE_ADMIN")
 */
class ReceivedSMSController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/sms/';

    /**
     * Received SMS constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new ReceivedSMSTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список полученных смс
     * @Route("/", name="sms_list", methods={"GET", "POST"})
     * @param Request $request
     * @param SMSDataTableService $sMSDataTableService
     * @return Response
     */
    public function list(Request $request, SMSDataTableService $sMSDataTableService): Response
    {
        return $this->responseList($request, $sMSDataTableService);
    }

    /**
     * Edit sms
     * @Route("/{id}/edit", name="sms_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param ReceivedSMS $receivedSMS
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, ReceivedSMS $receivedSMS): Response
    {
        return $this->responseEdit($request, $receivedSMS, ReceivedSMSType::class);
    }

    /**
     * Delete sms
     * @Route("/{id}", name="sms_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param ReceivedSMS $receivedSMS
     * @return Response
     */
    public function delete(Request $request, ReceivedSMS $receivedSMS): Response
    {
        return $this->responseDelete($request, $receivedSMS);
    }
}
