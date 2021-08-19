<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Template\TemplateService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\AppAbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DoctorOfficeAbstractController
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
abstract class DoctorOfficeAbstractController extends AppAbstractController
{
    /** @var string Name of rout to patient medical history in doctor office */
    public const DOCTOR_MEDICAL_HISTORY_ROUTE = 'doctor_medical_history';

    /** @var string Id parameter of medical history view route in doctor office */
    public const DOCTOR_MEDICAL_HISTORY_ROUTE_ID_PARAMETER = 'id';

    /**
     * Set redirect to "doctor_medical_history" when form controller successfully finishes work
     * @param Patient $patient
     * @throws Exception
     */
    protected function setRedirectMedicalHistoryRoute(Patient $patient): void
    {
        $this->templateService->setRedirectRoute(
            self::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [self::DOCTOR_MEDICAL_HISTORY_ROUTE_ID_PARAMETER => $patient]
        );
    }

    /**
     * Returns staff or redirects to MedicalHistory page with error
     * @param Patient $patient
     * @return Staff|RedirectResponse
     */
    protected function getStaff(Patient $patient)
    {
        $staffUser = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        try {
            if (AuthUserInfoService::isDoctor($staffUser)) {
                return $entityManager->getRepository(Staff::class)->getStaff($staffUser);
            } else {
                throw new Exception('Не удалось добавить назначение.');
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToMedicalHistory($patient);
        }
    }

    /**
     * Redirects to medical history of patient
     * @param Patient $patient
     * @return RedirectResponse
     */
    protected function redirectToMedicalHistory(Patient $patient): RedirectResponse
    {
        return $this->redirectToRoute(
            MedicalHistoryController::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [
                'id' => $patient->getId(),
            ]
        );
    }

    /**
     * Redirect to add prescription page
     * @param Patient $patient
     * @param Prescription $prescription
     * @return TemplateService
     * @throws Exception
     */
    protected function redirectToAddPrescriptionPage(Patient $patient, Prescription $prescription): TemplateService
    {
        return $this->templateService->setRedirectRoute(
            'add_prescription_show',
            [
                'patient' => $patient,
                'prescription' => $prescription
            ]
        );
    }
}