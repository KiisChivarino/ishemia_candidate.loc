<?php

namespace App\Controller\DoctorOffice\MedicalHistory;

use App\Controller\DoctorOffice\DoctorOfficeAbstractController;
use App\Entity\Patient;
use App\Entity\PatientDischargeEpicrisis;
use App\Form\DischargeEpicrisisFileType;
use App\Form\DischargeEpicrisisType;
use App\Repository\MedicalHistoryRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\FileService\FileService;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\DoctorOffice\DischargeEpicrisisTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class DischargeEpicrisisController
 * Выписные эпикризы
 * @Route("/doctor_office/patient")
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 * @package App\Controller\DoctorOffice\MedicalHistory
 */
class DischargeEpicrisisController extends DoctorOfficeAbstractController
{
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    /** @var string Name of form template edit DISCHARGE_EPICRISIS data */
    private const EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME = 'edit_discharge_epicrisis';
    /** @var string Name of form template: new discharge epicrisis */
    private const NEW_DISCHARGE_EPICRISIS_TEMPLATE_NAME = 'new_discharge_epicrisis';

    /**
     * DischargeEpicrisisController constructor.
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
        $this->templateService = new DischargeEpicrisisTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * @Route(
     *     "/{id}/medical_history/new_discharge_epicrisis",
     *     name="doctor_new_discharge_epicrisis",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     * )
     * @param Request $request
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param FileService $fileService
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function new(
        Request $request,
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository,
        FileService $fileService
    )
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $this->setRedirectMedicalHistoryRoute($patient->getId());
        return $this->responseNew(
            $request,
            (new PatientDischargeEpicrisis())->setMedicalHistory($medicalHistory),
            DischargeEpicrisisType::class,
            null,
            [],
            function (EntityActions $actions) use ($fileService) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisFileType::class) . 's')
                );
            },
            self::NEW_DISCHARGE_EPICRISIS_TEMPLATE_NAME
        );
    }

    /**
     * Edit discharge epicrisis
     * @Route(
     *     "/{id}/edit_discharge_epicrisis",
     *     name="doctor_edit_discharge_epicrisis",
     *     methods={"GET","POST"},
     *     requirements={"id"="\d+"}
     *     )
     * @param Request $request
     * @param PatientDischargeEpicrisis $dischargeEpicrisis
     * @param FileService $fileService
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function edit(
        Request $request,
        PatientDischargeEpicrisis $dischargeEpicrisis,
        FileService $fileService
    )
    {
        $this->setRedirectMedicalHistoryRoute($dischargeEpicrisis->getMedicalHistory()->getPatient()->getId());
        return $this->responseEdit(
            $request,
            $dischargeEpicrisis,
            DischargeEpicrisisType::class,
            [],
            function (EntityActions $actions) use ($fileService) {
                $fileService->prepareFiles(
                    $actions->getForm()
                        ->get(MultiFormService::getFormName(DischargeEpicrisisFileType::class) . 's')
                );
            },
            self::EDIT_DISCHARGE_EPICRISIS_TEMPLATE_NAME
        );
    }
}