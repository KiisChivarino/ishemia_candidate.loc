<?php

namespace App\Controller\DoctorOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HelpController extends AbstractController
{
    /**
     * @Route("/doctor_office/help", name="doctor_office_help")
     * @IsGranted("ROLE_DOCTOR_HOSPITAL")
     */
    public function index()
    {
        return $this->render(
            'doctorOffice/doctor_office_help/list.html.twig', [
            'controller_name' => 'DoctorOfficeHelpController',
        ]
        );
    }
}
