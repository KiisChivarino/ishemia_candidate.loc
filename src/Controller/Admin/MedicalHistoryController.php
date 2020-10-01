<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Form\Admin\MedicalHistory\EditMedicalHistoryType;
use App\Form\Admin\MedicalHistoryType;
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
        return $this->responseNew(
            $request,
            (new MedicalHistory()),
            MedicalHistoryType::class,
            null,
            [],
            function (EntityActions $actions) {
                /** @var Patient $patient */
                $patient = $actions->getRequest()->query->get('id')
                    ? $this->getDoctrine()->getManager()->getRepository(Patient::class)->find($actions->getRequest()->query->get('id'))
                    : null;
                $actions->getEntity()->setPatient($patient);
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
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'medicalHistory' => $medicalHistory,
                    'dateEndMedicalHistory' => $medicalHistory,
                ]
            )
            ->add(
                'medicalHistory', MedicalHistoryType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'dateEndMedicalHistory', EditMedicalHistoryType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
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
