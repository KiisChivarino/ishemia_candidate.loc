<?php

namespace App\Controller\DoctorOffice;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientTesting;
use App\Entity\Template;
use App\Entity\TemplateParameterText;
use App\Entity\TextByTemplate;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\MedicalHistoryType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointmentType;
use App\Form\Doctor\AuthUserPersonalDataType;
use App\Form\TextByTemplateType;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateTypeRepository;
use App\Repository\TextByTemplateRepository;
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
    ) {
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
        if($medicalHistory){
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
                'dischargeEpicrisis' => $dischargeEpicrisis,
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
    ): Response {
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
            self::TEMPLATE_PATH.'edit_personal_data.html.twig', [
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
    ) {
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
            self::TEMPLATE_PATH.'edit_anamnestic_data.html.twig', [
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
    ) {
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $firstAppointment = $this->getDoctrine()->getRepository(PatientAppointment::class)->getFirstAppointment($medicalHistory);
        $objectiveStatus = $firstAppointment->getObjectiveStatus();
//        dd($firstAppointment);
//        dd($firstAppointment->getObjectiveStatus());
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
            self::TEMPLATE_PATH.'edit_objective_data.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Patient $patient
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextByTemplateRepository $textByTemplateRepository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/{id}/edit_objective_data/objectiveStatus", name="doctor_edit_objective_data_objective_status", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     */
    public function editObjectiveDataObjectiveStatus(
        Request $request,
        Patient $patient,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextByTemplateRepository $textByTemplateRepository
    ) {
        $templateType = $templateTypeRepository->findOneBy([
            'id' => 3
        ]);
        $parameters = $templateParameterRepository->findBy([
            'templateType' => $templateType
        ]);
        $medicalHistory = $this->getDoctrine()->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient);
        $firstAppointment = $this->getDoctrine()->getRepository(PatientAppointment::class)->getFirstAppointment($medicalHistory);
//        dd($firstAppointment);
        $textBytemplate = new TextByTemplate();
        $textBytemplate->setTemplateType($templateType);
        $firstAppointment->setObjectiveStatus($textBytemplate);

        $template = $this->templateService->edit();
        $this->templateService->setTemplatePath(self::TEMPLATE_PATH);
        $form = $this->createForm(TextByTemplateType::class, $textBytemplate, ['parameters' => $parameters]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $data = $request->request->all();

            foreach($data['text_by_template'] as $key => $value){
                $exp_key = explode('-', $key);
                if($exp_key[0] == 'parameter'){
                    $arr_result[] = $value;
                }
            }
            $res = '';
            foreach ($arr_result as $item) {
                $parameter = $entityManager->getRepository(TemplateParameterText::class)->findOneBy([
                    'id' => $item
                ]);
                $res .= '<p><strong>'.$parameter->getTemplateParameter()->getName().'</strong>'. '. '. $parameter->getText() .'.</p>';
            }
            $textBytemplate->setText($res);

            $entityManager->persist($textBytemplate);
            $entityManager->flush();

            return $this->redirectToRoute('doctor_edit_objective_data', ['id' => $patient->getId()]);
        }

        return $this->render(
            self::TEMPLATE_PATH.'edit_objective_data_objective_status.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }
}