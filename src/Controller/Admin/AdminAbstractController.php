<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
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
     * Redirect to show patient page
     * @param Patient $patient
     * @return RedirectResponse
     */
    protected function redirectToPatient(Patient $patient): RedirectResponse
    {
        return $this->redirectToRoute('patient_show', ['patient' => $patient->getId()]);
    }

    /**
     * Returns MedicalHistory entity by GET parameter
     * @param Request $request
     * @return MedicalHistory|bool
     * @throws Exception
     * @todo сделать нормальные рауты в админке и убрать этот дебильный метод!!!
     */
    protected function getMedicalHistoryByParameter(Request $request)
    {
        if (!$medicalHistory =
            $this->getEntityById(
                MedicalHistory::class,
                $this->getGETParameter($request, MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)
            )
        ) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.parameter_not_found')
            );
            return false;
        }
        return $medicalHistory;
    }
}