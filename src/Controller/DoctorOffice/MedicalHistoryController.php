<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Repository\MedicalHistoryRepository;
use App\Repository\PatientAppointmentRepository;
use App\Repository\PatientTestingRepository;
use App\Services\InfoService\PatientInfoService;
use App\Services\TemplateBuilders\DoctorOffice\MedicalHistoryTemplate;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    /** @var string Path to directory with custom templates of controller */
    const TEMPLATE_PATH = 'doctorOffice/medical_history/';

    /**
     * MedicalHistoryController constructor.
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
        $this->templateService = new MedicalHistoryTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Shows medical history page for patient
     * @Route("/{id}/medical_history", name="doctor_medical_history", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Patient $patient
     *
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @param PatientAppointmentRepository $patientAppointmentRepository
     * @param PatientTestingRepository $patientTestingRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function main(
        Patient $patient,
        MedicalHistoryRepository $medicalHistoryRepository,
        PatientAppointmentRepository $patientAppointmentRepository,
        PatientTestingRepository $patientTestingRepository
    ): Response
    {
        $medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient);
        $firstAppointment = null;
        $firstTestings = [];
        $dischargeEpicrisis = null;
        if ($medicalHistory) {
            $firstAppointment = $patientAppointmentRepository->getFirstAppointment($medicalHistory);
            $firstTestings = $patientTestingRepository->getFirstTestings($medicalHistory);
            $dischargeEpicrisis = $medicalHistory->getPatientDischargeEpicrisis()
                ? $medicalHistory->getPatientDischargeEpicrisis()
                : null;
        }
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $patient,
            [
                'age' => PatientInfoService::getAge($patient),
                'medicalHistory' => $medicalHistory,
                'firstAppointment' => $firstAppointment,
                'firstTestings' => $firstTestings,
                'patientDischargeEpicrisis' => $dischargeEpicrisis,
            ]
        );
    }
}