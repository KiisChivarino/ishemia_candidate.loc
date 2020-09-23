<?php

namespace App\Controller\PatientOffice;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PatientMainController
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