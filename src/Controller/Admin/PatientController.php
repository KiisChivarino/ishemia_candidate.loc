<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use App\Form\Admin\AuthUser\EditAuthUserType;
use App\Form\Admin\AuthUser\NewAuthUserType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Services\DataTable\Admin\PatientDataTableService;
use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Form\Admin\AuthUser\AuthUserType;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use App\Services\LoggerService\LogService;
use App\Services\TemplateBuilders\PatientTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Управление страницами пациентов
 * @Route("/admin/patient")
 * @IsGranted("ROLE_ADMIN")
 */
class PatientController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/patient/';

    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * PatientController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LogService $logger
     */
    public function __construct(Environment $twig, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, LogService $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new PatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
        $this->logger = $logger;
    }

    /**
     * Список пациентов
     * @Route("/", name="patient_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientDataTableService $dataTableService
     *
     * @param LogService $logService
     * @return Response
     */
    public function list(Request $request, PatientDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новый пациент
     * @Route("/new", name="patient_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, AuthUserInfoService $authUserInfoService): Response
    {
        $template = $this->templateService->new();
        $authUser = (new AuthUser())->setEnabled(true);
        $patient = (new Patient())->setAuthUser($authUser);
        $medicalHistory = (new MedicalHistory)->setPatient($patient);
        $patientAppointment = (new PatientAppointment())->setMedicalHistory($medicalHistory);
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'authUser' => $authUser,
                    'newAuthUser' => $authUser,
                    'patient' => $patient,
                    'medicalHistory' => $medicalHistory,
                    'patientAppointmentStaff' => $patientAppointment,
                    'patientAppointmentType' => $patientAppointment,
                ]
            )
            ->add(
                'authUser', AuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'newAuthUser', NewAuthUserType::class, [
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
            ->add(
                'medicalHistory', MainDiseaseType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'patientAppointmentStaff', StaffType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'patientAppointmentType', AppointmentTypeType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $authUser->setRoles(self::PATIENT_ROLE);
            $encodedPassword = $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword());
            $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
            $authUser->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->persist($authUser);
                $em->flush();
                $em->getRepository(Patient::class)->persistPatient($patient, $authUser, $medicalHistory, $patientAppointment);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
            $logger = $this->logger
                ->setUser($this->getUser())
                ->setDescription(
                    'Patient with id: ' . $patient->getId() .
                    ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                    ' successfully created!')
                ->logCreateEvent();
            if (!$logger) {
                $this->logger->getError();
                // TODO:  when creating log fails
            }
            $this->addFlash('success', 'post.created_successfully');
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'new.html.twig', [
                'patient' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Информация о пациенте
     * @Route("/{id}", name="patient_show", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Patient $patient
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(Patient $patient, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'bodyMassIndex' => (new PatientInfoService())->getBodyMassIndex($patient),
                'medicalHistoryFilterName' => $filterService->generateFilterName('medical_history_list', Patient::class),
            ]
        );
    }

    /**
     * Редактирование пациента
     * @Route("/{id}/edit", name="patient_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Patient $patient
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     */
    public function edit(
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
                    'editAuthUser' => $authUser,
                    'patient' => $patient,
                ]
            )
            ->add(
                'authUser', AuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'editAuthUser', EditAuthUserType::class, [
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
            $logger = $this->logger
                ->setUser($this->getUser())
                ->setDescription(
                    'Patient with id: ' . $patient->getId() .
                    ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                    ' successfully updated!')
                ->logUpdateEvent();
            if (!$logger) {
                $this->logger->getError();
                // TODO:  when creating log fails
            }
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'edit.html.twig', [
                'entity' => $patient,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Удаление пациента
     * @Route("/{id}", name="patient_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Patient $patient
     *
     * @return Response
     */
    public function delete(Request $request, Patient $patient): Response
    {
        $logger = $this->logger
            ->setUser($this->getUser())
            ->setDescription(
                'Patient with id: ' . $patient->getId() .
                ' and FIO: ' . (new AuthUserInfoService())->getFIO($patient->getAuthUser()).
                ' successfully updated!')
            ->logDeleteEvent();
        if (!$logger) {
            $this->logger->getError();
            // TODO:  when creating log fails
        }
        return $this->responseDelete($request, $patient);
    }
}
