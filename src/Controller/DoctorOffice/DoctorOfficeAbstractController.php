<?php

namespace App\Controller\DoctorOffice;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\AppAbstractController;

/**
 * Class DoctorOfficeAbstractController
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller\DoctorOffice
 */
abstract class DoctorOfficeAbstractController extends AppAbstractController
{
}