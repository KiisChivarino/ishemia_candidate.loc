<?php

namespace App\Controller\PatientOffice;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientMainController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office")
 */
class PatientMainController extends PatientOfficeAbstractController
{
    /**
     * Main page of patient office
     * @Route("/main", name="patient_office_main")
     */
    public function main()
    {
        return $this->render(
            'patientOffice/main/main.html.twig'
        );
    }
}