<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Services\ControllerGetters\EntityActions;
use Closure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        return $this->redirectToRoute('patient_show', ['id' => $patient->getId()]);
    }
}