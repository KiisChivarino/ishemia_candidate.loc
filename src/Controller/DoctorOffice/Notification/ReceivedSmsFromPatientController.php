<?php

namespace App\Controller\DoctorOffice\Notification;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Repository\PatientSMSRepository;
use App\Services\DataTable\DoctorOffice\ReceivedSmsFromPatientDataTableService;
use App\Services\LoggerService\LogService;
use App\Services\TemplateBuilders\DoctorOffice\ReceivedSmsFromPatientTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class ReceivedSmsFromPatientController
 * @route ("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class ReceivedSmsFromPatientController extends DoctorOfficeAbstractController
{
    /** @var string Путь к twig шаблонам */
    const TEMPLATE_PATH = 'doctorOffice/received_sms_from_patient/';

    /**
     * @var LogService
     */
    private $logger;

    /**
     * ReceivedSmsFromPatientController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param LogService $logService
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        LogService $logService
    )
    {
        parent::__construct($translator);
        $this->templateService = new ReceivedSmsFromPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->logger = $logService;
        $this->translator = $translator;
    }

    /**
     * List of patients received sms
     * @Route("/patient/{id}/received_sms_from_patient", name="received_sms_from_patient_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Patient $patient
     * @param ReceivedSmsFromPatientDataTableService $dataTableService
     * @return Response
     */
    public function list(
        Request $request,
        Patient $patient,
        ReceivedSmsFromPatientDataTableService $dataTableService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            null,
            ['patient' => $patient]
        );
    }

    /**
     * Process sms vie ajax
     * @Route("/process_sms/{patientSmsId}/", name="process_sms_api", methods={"GET"})
     * @param $patientSmsId
     * @param PatientSMSRepository $patientSMSRepository
     * @return Response
     */
    public function processSMS($patientSmsId, PatientSMSRepository $patientSMSRepository): Response
    {
        $patientSMS = $patientSMSRepository->find((int) $patientSmsId);
        if (is_null($patientSMS)) {
            return new JsonResponse(['code' => 400]);
        }
        if ($patientSMS->getIsProcessed()) {
            return new JsonResponse(['code' => 300]);
        }
        $em = $this->getDoctrine()->getManager();
        $patientSMS->setIsProcessed(true);
        $this->logger
            ->setUser($this->getUser())
            ->setDescription(
                $this->translator->trans(
                    'log.update.entity',
                    [
                        '%entity%' => 'СМС пациента',
                        '%id%' => $patientSMS->getId(),
                    ]
                )
            )
            ->logUpdateEvent();
        $em->flush();
        return new JsonResponse(['code' => 200]);
    }
}
