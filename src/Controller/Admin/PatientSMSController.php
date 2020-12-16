<?php

namespace App\Controller\Admin;

use App\Entity\PatientSMS;
use App\Form\Admin\PatientSMSType;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientSMSDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\PatientSMSTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "PatientSMS"
 * @Route("/admin/sms")
 * @IsGranted("ROLE_ADMIN")
 */
class PatientSMSController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient-sms/';

    /**
     * Received SMS constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientSMSTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список полученных смс
     * @Route("/", name="patient_sms_list", methods={"GET", "POST"})
     * @param Request $request
     * @param PatientSMSDataTableService $patientSMSDataTableService
     * @param FilterService $filterService
     * @return Response
     */
    public function list(
        Request $request,
        PatientSMSDataTableService $patientSMSDataTableService,
        FilterService $filterService
    ): Response {
        return $this->responseList(
            $request, $patientSMSDataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['PATIENT'],]
            )
        );
    }

    /**
     * Edit sms
     * @Route("/{id}/edit", name="patient_sms_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PatientSMS $patientSMS
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PatientSMS $patientSMS): Response
    {
        return $this->responseEdit($request, $patientSMS, PatientSMSType::class);
    }

    /**
     * Delete sms
     * @Route("/{id}", name="patient_sms_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PatientSMS $patientSMS
     * @return Response
     */
    public function delete(Request $request, PatientSMS $patientSMS): Response
    {
        return $this->responseDelete($request, $patientSMS);
    }
}
