<?php

namespace App\Controller\PatientOffice;

use App\Controller\AppAbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientOfficeAbstractController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 */
abstract class PatientOfficeAbstractController extends AppAbstractController
{
}