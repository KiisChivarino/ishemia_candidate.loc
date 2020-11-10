<?php

namespace App\Controller\DoctorOffice;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\AppAbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
}