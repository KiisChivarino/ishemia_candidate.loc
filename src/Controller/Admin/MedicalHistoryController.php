<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Form\Admin\MedicalHistory\AnamnesOfLifeType;
use App\Form\Admin\MedicalHistory\DiseaseHistoryType;
use App\Form\Admin\MedicalHistory\EditMedicalHistoryType;
use App\Form\Admin\MedicalHistory\EnabledType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\Patient\PatientClinicalDiagnosisTextType;
use App\Form\Patient\PatientMKBCodeType;
use App\Form\DischargeEpicrisisType;
use App\Repository\PatientTestingFileRepository;
use App\Repository\PrescriptionRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\FileService\FileService;
use App\Services\MultiFormService\FormData;
use App\Services\DataTable\Admin\MedicalHistoryDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MedicalHistoryController
 * @Route("/admin/medical_history")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class MedicalHistoryController extends AdminAbstractController
{
    /** @var string путь к twig шаблонам контроллера */
    public const TEMPLATE_PATH = 'admin/medical_history/';

    /** @var string Key of the medical history parameter */
    public const MEDICAL_HISTORY_ID_PARAMETER_KEY = 'medical_history_id';

    /** @var string Name of collection of files from entity method */
    protected const FILES_COLLECTION_PROPERTY_NAME = 'dischargeEpicrisisFiles';

    /**
     * CountryController constructor.
     *
     * @param Environment         $twig
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * MedicalHistory list
     * @Route("/", name="medical_history_list", methods={"GET","POST"})
     *
     * @param Request                        $request
     * @param MedicalHistoryDataTableService $dataTableService
     * @param FilterService                  $filterService
     *
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        MedicalHistoryDataTableService $dataTableService,
        FilterService $filterService
    ): Response {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['PATIENT'],]
            )
        );
    }

//    /**
//     * New MedicalHistory
//     * @Route("/new", name="medical_history_new", methods={"GET","POST"})
//     *
//     * @param Request $request
//     *
//     * @return Response
//     */
//    public function new(Request $request): Response
//    {
//      todo: сделать добавление истории болезни на случай, если понадобится добавить пациенту вторую после завершения лечения
//    }

    /**
     * Show medical history info
     * @Route("/{id}", name="medical_history_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param MedicalHistory         $medicalHistory
     * @param FilterService          $filterService
     *
     * @param PrescriptionRepository $prescriptionRepository
     *
     * @return Response
     * @throws Exception
     */
    public function show(
        MedicalHistory $medicalHistory,
        FilterService $filterService,
        PrescriptionRepository $prescriptionRepository
    ): Response {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $medicalHistory,
            [
                'patientFio'                   => AuthUserInfoService::getFIO(
                    $medicalHistory->getPatient()->getAuthUser(),
                    true
                ),
                'medicalRecordFilterName'      =>
                    $filterService->generateFilterName(
                        'medical_record_list',
                        MedicalHistory::class
                    ),
                'patientTestingFilterName'     =>
                    $filterService->generateFilterName(
                        'patient_testing_list',
                        MedicalHistory::class
                    ),
                'prescriptionFilterName'       =>
                    $filterService->generateFilterName(
                        'prescription_list',
                        MedicalHistory::class
                    ),
                'patientAppointmentFilterName' =>
                    $filterService->generateFilterName(
                        'patient_appointment_list',
                        MedicalHistory::class
                    ),
                'allPrescriptionsCompleted'    =>
                    !$prescriptionRepository->findNotCompletedPrescription($medicalHistory),
                'notificationFilterName'       =>
                    $filterService->generateFilterName(
                        'notification_list',
                        MedicalHistory::class
                    ),
            ]
        );
    }

    /**
     * Edit medical history
     * @Route("/{id}/edit", name="medical_history_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request                      $request
     * @param MedicalHistory               $medicalHistory
     *
     * @param PatientTestingFileRepository $patientTestingFileRepository
     * @param FileService                  $fileService
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        MedicalHistory $medicalHistory,
        PatientTestingFileRepository $patientTestingFileRepository,
        FileService $fileService
    ): Response {
        $clinicalDiagnosis = $medicalHistory->getClinicalDiagnosis();

        return $this->responseEditMultiForm(
            $request,
            $medicalHistory,
            [
                new FormData(PatientClinicalDiagnosisTextType::class, $clinicalDiagnosis),
                new FormData(PatientMKBCodeType::class, $clinicalDiagnosis),
                new FormData(MedicalHistoryType::class, $medicalHistory),
                new FormData(AnamnesOfLifeType::class, $medicalHistory),
                new FormData(DiseaseHistoryType::class, $medicalHistory),
                new FormData(EditMedicalHistoryType::class, $medicalHistory),
                new FormData(DischargeEpicrisisType::class, $medicalHistory->getPatientDischargeEpicrisis()),
                new FormData(EnabledType::class, $medicalHistory),
            ],
            function (EntityActions $actions) use ($patientTestingFileRepository, $fileService) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisType::class))
                        ->get(self::FILES_COLLECTION_PROPERTY_NAME)
                );
            }
        );
    }

    /**
     * Delete medical history
     * @Route("/{id}", name="medical_history_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request        $request
     * @param MedicalHistory $medicalHistory
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, MedicalHistory $medicalHistory): Response
    {
        return $this->responseDelete($request, $medicalHistory);
    }
}
