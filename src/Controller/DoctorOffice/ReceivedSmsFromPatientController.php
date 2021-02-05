<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Form\Admin\PatientSMSType;
use App\Repository\PatientSMSRepository;
use App\Services\DataTable\DoctorOffice\ReceivedSmsFromPatientDataTableService;
use App\Services\FilterService\FilterService;
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
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator, LogService $logService)
    {
        parent::__construct($translator);
        $this->templateService = new ReceivedSmsFromPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->logger = $logService;
    }

    /**
     * List of patients received sms
     * @Route("/patient/{id}/received_sms_from_patient", name="received_sms_from_patient_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Patient $patient
     * @param ReceivedSmsFromPatientDataTableService $dataTableService
     * @param FilterService $filterService
     * @return Response
     */
    public function list(
        Request $request,
        Patient $patient,
        ReceivedSmsFromPatientDataTableService $dataTableService,
        FilterService $filterService
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
     * Edit sms
     * @Route("/patient/{id}/received_sms_from_patient/edit", name="received_sms_from_patient_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     * @param Request $request
     * @param PatientSMSRepository $patientSMSRepository
     * @return Response
     * @throws \Exception
     */
    public function edit(Patient $patient, Request $request, PatientSMSRepository $patientSMSRepository): Response
    {
        $patientSMS= $patientSMSRepository->find($request->query->get('patientSmsId'));
        $this->templateService->setRedirectRoute(
            'received_sms_from_patient_list',
            ['id' => $patient->getId()]
        );
        return $this->responseEdit($request, $patientSMS, PatientSMSType::class);
    }

    /**
     * Edit sms
     * @Route("/process_sms/{id}/", name="process_sms_api", methods={"GET"}, requirements={"patientSmsId"="\d+"})
     * @param $id
     * @param PatientSMSRepository $patientSMSRepository
     * @return Response
     */
    public function processSMS($id, PatientSMSRepository $patientSMSRepository): Response
    {
        $patientSMS = $patientSMSRepository->find($id);
        if(is_null($patientSMS)) {
            return new JsonResponse(['code' => 400]);
        }
        if ($patientSMS->getIsProcessed()) {
            return new JsonResponse(['code' => 300]);
        }
        $em = $this->getDoctrine()->getManager();
        $patientSMS->setIsProcessed(true);
        $this->logger
            ->setUser($this->getUser())
            ->setDescription('Запись - СМС пациента id: '. $patientSMS->getId() .' обновлена.')
            ->logUpdateEvent();
        $em->flush();
        return new JsonResponse(['code' => 200]);
    }
}
