<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\PatientAppointment;
use App\Form\Admin\PatientAppointment\EditPatientAppointmentType;
use App\Form\Admin\PatientAppointmentType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\PatientAppointmentDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\InfoService\MedicalRecordInfoService;
use App\Services\TemplateBuilders\PatientAppointmentTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientAppointmentController
 * @Route("admin/patient_appointment")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PatientAppointmentController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_appointment/';

    /**
     * PatientAppointmentController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new PatientAppointmentTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of patient appointments
     * @Route("/", name="patient_appointment_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientAppointmentDataTableService $dataTableService
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function list(Request $request, PatientAppointmentDataTableService $dataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['MEDICAL_HISTORY'],
                ]
            )
        );
    }

    /**
     * New patient appointment
     * @Route("/new", name="patient_appointment_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        if ($request->query->get('medical_history_id')) {
            $medicalHistory = $this->getDoctrine()->getManager()->getRepository(MedicalHistory::class)->find($request->query->get('medical_history_id'));
        }
        if (!isset($medicalHistory) || !is_a($medicalHistory, MedicalHistory::class)) {
            $this->addFlash('warning', 'Прием пациента не может быть добавлен: история болезни не найдена!');
            return $this->redirectToRoute('medical_history_list');
        }
        return $this->responseNew(
            $request, (new PatientAppointment())->setMedicalHistory($medicalHistory)->setIsConfirmed(true), PatientAppointmentType::class, null, [],
            function (EntityActions $actions) use ($medicalHistory) {
                /** @var PatientAppointment $patientAppointment */
                $patientAppointment = $actions->getEntity();
                $patientAppointment->setMedicalRecord($this->getDoctrine()->getRepository(MedicalRecord::class)->getMedicalRecord($medicalHistory));
            }
        );
    }

    /**
     * Show patient appointment
     * @Route("/{id}", name="patient_appointment_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function show(PatientAppointment $patientAppointment): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $patientAppointment, [
                'medicalHistoryTitle' => (new MedicalHistoryInfoService())->getMedicalHistoryTitle($patientAppointment->getMedicalHistory()),
                'medicalRecordTitle' => (new MedicalRecordInfoService())->getMedicalRecordTitle($patientAppointment->getMedicalRecord()),
                'staffFio' => (new AuthUserInfoService())->getFIO($patientAppointment->getStaff()->getAuthUser(), true),
            ]
        );
    }

    /**
     * Edit patient appointment
     * @Route("/{id}/edit", name="patient_appointment_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function edit(Request $request, PatientAppointment $patientAppointment): Response
    {
        $template = $this->templateService->edit();
        return $this->responseFormTemplate(
            $request, $patientAppointment,
            $this->createFormBuilder()
                ->setData(
                    [
                        'patientAppointment' => $patientAppointment,
                        'editPatientAppointment' => $patientAppointment,
                    ]
                )
                ->add(
                    'patientAppointment', PatientAppointmentType::class, [
                        'label' => false,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->add(
                    'editPatientAppointment', EditPatientAppointmentType::class, [
                        'label' => false,
                        self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    ]
                )
                ->getForm(),
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Delete patient appointment
     * @Route("/{id}", name="patient_appointment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientAppointment $patientAppointment
     *
     * @return Response
     */
    public function delete(Request $request, PatientAppointment $patientAppointment): Response
    {
        return $this->responseDelete($request, $patientAppointment);
    }
}
