<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Services\ControllerGetters\EntityActions;
use Closure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
}