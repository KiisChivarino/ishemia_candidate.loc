<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Services\ControllerGetters\EntityActions;
use Closure;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppAbstractController
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
abstract class AdminAbstractController extends AppAbstractController
{
    /**
     * Set next id for entity
     *
     * @return Closure
     */
    public function setNextEntityIdFunction(): Closure
    {
        return function (EntityActions $actions) {
            $actions
                ->getEntity()
                ->setId(
                    $actions
                        ->getEntityManager()
                        ->getRepository(get_class($actions->getEntity()))
                        ->getNextEntityId()
                );
        };
    }


    /**
     * Returns MedicalHistory entity by GET parameter
     * @param Request $request
     * @return MedicalHistory|object|RedirectResponse
     * @throws Exception
     */
    protected function getMedicalHistoryByParameter(Request $request): MedicalHistory
    {
        return $this->getEntityById(
            MedicalHistory::class,
            $this->getGETParameter($request, MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)
        );
    }

    /**
     * Returns Prescription entity by GET parameter
     * @param Request $request
     * @return Prescription|object|RedirectResponse
     * @throws Exception
     */
    protected function getPrescriptionByParameter(Request $request): Prescription
    {
        /** @var Prescription $prescription */
        return $this->getEntityById(
            Prescription::class,
            $this->getGETParameter($request, PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)
        );
    }
}