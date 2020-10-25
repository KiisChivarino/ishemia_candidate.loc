<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientDischargeEpicrisis;
use App\Entity\Prescription;
use App\Form\Admin\MedicalHistory\EditMedicalHistoryType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\DischargeEpicrisisType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\MedicalHistoryDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\MedicalHistoryTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
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
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/medical_history/';

    /** @var string Name of collection of files from entity method */
    protected const FILES_COLLECTION_PROPERTY_NAME = 'dischargeEpicrisisFiles';

    /** @var string Name of form of patient discharge epicrisis */
    protected const PATIENT_DISCHARGE_EPICRISIS_FORM_NAME = 'patientDischargeEpicrisis';

    /** @var string Name of main disease form */
    protected const MAIN_DISEASE_FORM_NAME = 'mainDisease';

    /** @var string Name of medical history form */
    protected const MEDICAL_HISTORY_FORM_NAME = 'medicalHistory';

    /** @var string Name of date end medical history form */
    protected const DATE_END_MEDICAL_HISTORY_FORM_NAME = 'dateEndMedicalHistory';
    /**
     * CountryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * MedicalHistory list
     * @Route("/", name="medical_history_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param MedicalHistoryDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(Request $request, MedicalHistoryDataTableService $dataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['PATIENT'],]
            )
        );
    }

    /**
     * New MedicalHistory
     * @Route("/new", name="medical_history_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $template = $this->templateService->new();
        $medicalHistory = new MedicalHistory();
        $dischargeEpicrisis = (new PatientDischargeEpicrisis())->setMedicalHistory($medicalHistory);
        return $this->responseFormTemplate(
            $request,
            $medicalHistory,
            $this->createFormBuilder()
                ->setData(
                    [
                        self::MAIN_DISEASE_FORM_NAME => $medicalHistory,
                        self::MEDICAL_HISTORY_FORM_NAME => $medicalHistory,
                        self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME => $dischargeEpicrisis,
                    ]
                )
                ->add(
                    self::MAIN_DISEASE_FORM_NAME, MainDiseaseType::class, [
                        'label' => false,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->add(
                    self::MEDICAL_HISTORY_FORM_NAME, MedicalHistoryType::class, [
                        'label' => false,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->add(
                    self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME, DischargeEpicrisisType::class, [
                        'label' => false,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->getForm(),
            self::RESPONSE_FORM_TYPE_NEW,
            function (EntityActions $actions) use ($dischargeEpicrisis) {
                /** @var Patient $patient */
                $patient = $actions->getRequest()->query->get('id')
                    ? $this->getDoctrine()->getManager()->getRepository(Patient::class)->find($actions->getRequest()->query->get('id'))
                    : null;
                $actions->getEntity()->setPatient($patient);
                $this->prepareFiles($actions->getForm()->get(self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME)->get(self::FILES_COLLECTION_PROPERTY_NAME));
                $actions->getEntityManager()->persist($dischargeEpicrisis);
            }
        );
    }

    /**
     * Show medical history info
     * @Route("/{id}", name="medical_history_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param MedicalHistory $medicalHistory
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(MedicalHistory $medicalHistory, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $medicalHistory,
            [
                'patientFio' => (new AuthUserInfoService())->getFIO($medicalHistory->getPatient()->getAuthUser(), true),
                'medicalRecordFilterName' => $filterService->generateFilterName('medical_record_list', MedicalHistory::class),
                'patientTestingFilterName' => $filterService->generateFilterName('patient_testing_list', MedicalHistory::class),
                'prescriptionFilterName' => $filterService->generateFilterName('prescription_list', MedicalHistory::class),
                'patientAppointmentFilterName' => $filterService->generateFilterName('patient_appointment_list', MedicalHistory::class),
                'allPrescriptionsCompleted' => $this->getDoctrine()->getRepository(Prescription::class)->findNotCompletedPrescription($medicalHistory) ? false : true,
                'notificationFilterName' => $filterService->generateFilterName('notification_list', MedicalHistory::class),
            ]
        );
    }

    /**
     * Edit medical history
     * @Route("/{id}/edit", name="medical_history_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param MedicalHistory $medicalHistory
     *
     * @return Response
     */
    public function edit(Request $request, MedicalHistory $medicalHistory): Response
    {
        $template = $this->templateService->edit();
        $patientDischargeEpicrisis = $medicalHistory->getPatientDischargeEpicrisis() ? $medicalHistory->getPatientDischargeEpicrisis() : new PatientDischargeEpicrisis();
        $medicalHistory->setPatientDischargeEpicrisis($patientDischargeEpicrisis);
        $this->getDoctrine()->getManager()->persist($patientDischargeEpicrisis);
        $this->getDoctrine()->getManager()->flush();
        $form = $this->createFormBuilder()
            ->setData(
                [
                    self::MAIN_DISEASE_FORM_NAME => $medicalHistory,
                    self::MEDICAL_HISTORY_FORM_NAME => $medicalHistory,
                    self::DATE_END_MEDICAL_HISTORY_FORM_NAME => $medicalHistory,
                    self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME => $medicalHistory->getPatientDischargeEpicrisis() ? $medicalHistory->getPatientDischargeEpicrisis()
                        : new PatientDischargeEpicrisis(),
                ]
            )
            ->add(
                self::MAIN_DISEASE_FORM_NAME, MainDiseaseType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                self::MEDICAL_HISTORY_FORM_NAME, MedicalHistoryType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                self::DATE_END_MEDICAL_HISTORY_FORM_NAME, EditMedicalHistoryType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME, DischargeEpicrisisType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->prepareFiles($form->get(self::PATIENT_DISCHARGE_EPICRISIS_FORM_NAME)->get(self::FILES_COLLECTION_PROPERTY_NAME));
            $entityManager->flush();
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'edit.html.twig', [
                'entity' => $medicalHistory,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete medical history
     * @Route("/{id}", name="medical_history_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param MedicalHistory $medicalHistory
     *
     * @return Response
     */
    public function delete(Request $request, MedicalHistory $medicalHistory): Response
    {
        return $this->responseDelete($request, $medicalHistory);
    }
}
