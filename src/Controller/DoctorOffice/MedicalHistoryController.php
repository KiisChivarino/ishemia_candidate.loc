<?php

namespace App\Controller\DoctorOffice;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientDischargeEpicrisis;
use App\Entity\PatientTesting;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\DischargeEpicrisisType;
use App\Form\Doctor\AuthUserPersonalDataType;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateBuilders\DoctorOffice\MedicalHistoryTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MedicalHistoryController
 * @Route("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class MedicalHistoryController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    private $authUserInfoService;

    private $patientInfoService;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * MedicalHistoryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param AuthUserInfoService $authUserInfoService
     * @param PatientInfoService $patientInfoService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        AuthUserInfoService $authUserInfoService,
        PatientInfoService $patientInfoService,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->authUserInfoService = $authUserInfoService;
        $this->patientInfoService = $patientInfoService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/{id}/medical_history", name="doctor_medical_history", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     *
     * @return Response
     */
    public function main(Patient $patient): Response
    {
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $firstAppointment = null;
        $firstTestings = [];
        $dischargeEpicrisis = null;
        if ($medicalHistory) {
            $firstAppointment = $this->getDoctrine()->getRepository(PatientAppointment::class)->getFirstAppointment($medicalHistory);
            $firstTestings = $this->getDoctrine()->getRepository(PatientTesting::class)->getFirstTestings($medicalHistory);
            $dischargeEpicrisis = $medicalHistory->getPatientDischargeEpicrisis();
        }
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'age' => $this->patientInfoService->getAge($patient),
                'medicalHistory' => $medicalHistory,
                'firstAppointment' => $firstAppointment,
                'firstTestings' => $firstTestings,
                'patientDischargeEpicrisis' => $dischargeEpicrisis,
            ]
        );
    }

    /**
     * @Route("/{id}/edit_personal_data", name="doctor_edit_personal_data", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Patient $patient
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     */
    public function editPersonalData(
        Request $request,
        Patient $patient,
        AuthUserInfoService $authUserInfoService
    ): Response
    {
        $template = $this->templateService->edit();
        $authUser = $patient->getAuthUser();
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'authUser' => $authUser,
                    'patient' => $patient,
                ]
            )
            ->add(
                'authUser', AuthUserPersonalDataType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'patient', PatientType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $oldPassword = $authUser->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editPassword($this->passwordEncoder, $authUser, $oldPassword);
            $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('doctor_medical_history', ['id' => $patient->getId()]);
        }
        return $this->render(
            self::TEMPLATE_PATH . 'edit_personal_data.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @Route("/{id}/edit_anamnestic_data", name="doctor_edit_anamnestic_data", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @return RedirectResponse|Response
     */
    public function editAnamnesticData(
        Request $request,
        Patient $patient
    )
    {
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $template = $this->templateService->edit();
        $this->templateService->setTemplatePath(self::TEMPLATE_PATH);
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'medicalHistory' => $medicalHistory,
                    'mainDisease' => $medicalHistory,
                ]
            )
            ->add(
                'mainDisease', MainDiseaseType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'medicalHistory', MedicalHistoryType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('doctor_medical_history', ['id' => $patient->getId()]);
        }
        return $this->render(
            self::TEMPLATE_PATH . 'edit_anamnestic_data.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @Route("/{id}/edit_objective_data", name="doctor_edit_objective_data", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @return RedirectResponse|Response
     */
    public function editObjectiveData(
        Request $request,
        Patient $patient
    )
    {
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $firstAppointment = $this->getDoctrine()->getRepository(PatientAppointment::class)->getFirstAppointment($medicalHistory);
        $template = $this->templateService->edit();
        $this->templateService->setTemplatePath(self::TEMPLATE_PATH);
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'firstAppointment' => $firstAppointment,
                ]
            )
            ->add(
                'firstAppointment', PatientAppointmentType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('doctor_medical_history', ['id' => $patient->getId()]);
        }
        return $this->render(
            self::TEMPLATE_PATH . 'edit_objective_data.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @param Request $request
     * @param PatientDischargeEpicrisis $dischargeEpicrisis
     * @return RedirectResponse|Response
     */
    public function editDischargeEpicrisis(Request $request, PatientDischargeEpicrisis $dischargeEpicrisis)
    {
        return $this->responseEdit($request, $dischargeEpicrisis, DischargeEpicrisisType::class);
    }
}