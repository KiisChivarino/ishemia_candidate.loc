<?php

namespace App\Controller\DoctorOffice;

use App\Entity\Patient;
use App\Entity\Staff;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\DBAL\DBALException;
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
     * @param int $entityId
     */
    protected function setRedirectMedicalHistoryRoute(int $entityId): void
    {
        $this->templateService->setRedirectRoute(
            self::DOCTOR_MEDICAL_HISTORY_ROUTE,
            [self::DOCTOR_MEDICAL_HISTORY_ROUTE_ID_PARAMETER => $entityId]
        );
    }

    /**
     * Flush entity and redirect to Medical History if is Exception
     * @param Patient $patient
     * @return void|RedirectResponse
     */
    protected function flushToMedicalHistory(Patient $patient): RedirectResponse
    {
        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (DBALException $e) {
            $this->addFlash('error',
                $this->translator->trans(
                    'doctor_office_abstract_controller.error.dbal_exception'
                ));
            return $this->redirectToRoute(
                'doctor_medical_history', [
                    'id' => $patient->getId(),
                ]
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->translator->trans(
                'doctor_office_abstract_controller.error.exception'
            ));
            return $this->redirectToRoute(
                'doctor_medical_history', [
                    'id' => $patient->getId(),
                ]
            );
        }
        $this->addFlash('success',
            $this->translator->trans(
            'doctor_office_abstract_controller.success.add'
        ));
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
            if (AuthUserInfoService::isDoctor($staffUser))
            {
                return $entityManager->getRepository(Staff::class)->getStaff($staffUser);
            } else {
                throw new Exception('Не удалось добавить назначение.');
            }
        } catch (Exception $exception){
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute(
                'doctor_medical_history', [
                    'id' => $patient->getId(),
                ]
            );
        }
    }
}