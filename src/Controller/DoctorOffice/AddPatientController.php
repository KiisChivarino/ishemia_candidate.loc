<?php


namespace App\Controller\DoctorOffice;


use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Form\Admin\AuthUser\AuthUserType;
use App\Form\Admin\AuthUser\NewAuthUserType;
use App\Form\Admin\MedicalHistory\MainDiseaseType;
use App\Form\Admin\Patient\PatientType;
use App\Form\Admin\PatientAppointment\AppointmentTypeType;
use App\Form\Admin\PatientAppointment\StaffType;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\DoctorOffice\CreateNewPatientTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Twig\Environment;
use Exception;

/**
 * Class MedicalHistoryController
 * @Route("/doctor_office")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
class AddPatientController extends DoctorOfficeAbstractController
{
    const TEMPLATE_PATH = 'doctorOffice/create_patient/';

    /** @var string Роль пациента */
    private const PATIENT_ROLE = 'ROLE_PATIENT';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * PatientsListController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new CreateNewPatientTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Add newPatient
     * @Route("/create_patient", name="create_patients", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AuthUserInfoService $authUserInfoService
     * @return Response
     * @throws Exception
     */
    public function createNew(Request $request, AuthUserInfoService $authUserInfoService): Response
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
            $this->addFlash('success', 'post.created_successfully');
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            self::TEMPLATE_PATH.'new.html.twig', [
                'patient' => new Patient(),
                'form' => $form->createView(),
            ]
        );
    }

}