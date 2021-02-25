<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Form\Admin\Patient\PatientOptionalType;
use App\Form\Admin\Patient\PatientRequiredType;
use App\Form\Doctor\AuthUserPersonalDataType;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\DoctorOffice\PersonalDataTemplate;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PersonalDataController
 * Личные данные
 * @Route("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory
 */
class PersonalDataController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    /** @var string Name of form template edit personal data */
    protected const EDIT_PERSONAL_DATA_TEMPLATE_NAME = 'edit_personal_data';

    /**
     * PersonalDataController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->templateService = new PersonalDataTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Edit personal data of patient medical history
     * @Route(
     *     "/{id}/medical_history/edit_personal_data",
     *     name="doctor_edit_personal_data",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param Patient $patient
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(
        Request $request,
        Patient $patient,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response
    {
        $authUser = $patient->getAuthUser();
        $oldPassword = $authUser->getPassword();
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        return $this->responseEditMultiForm(
            $request,
            $patient,
            [
                new FormData($authUser, AuthUserPersonalDataType::class),
                new FormData($patient, PatientRequiredType::class),
                new FormData($patient, PatientOptionalType::class),
            ],
            function () use ($authUser, $oldPassword, $passwordEncoder) {
                AuthUserCreatorService::updatePassword($passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
            },
            self::EDIT_PERSONAL_DATA_TEMPLATE_NAME
        );
    }
}