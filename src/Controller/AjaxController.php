<?php


namespace App\Controller;


use App\Entity\Diagnosis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxController
 * @IsGranted("ROLE_DOCTOR_HOSPITAL")
 *
 * @package App\Controller
 */
class AjaxController extends AbstractController
{
    /**
     * Find diagnoses
     * @Route("/find_diagnosis_ajax", name="find_diagnosis_ajax", methods={"GET"})
     *
     * @param Request $request
     *
     * @return false|string
     */
    public function findDiagnosisAjax(Request $request)
    {
        $string = $request->query->get('q');
        $entityManager = $this->getDoctrine()->getManager();
        $diagnoses = $entityManager->getRepository(Diagnosis::class)->findDiagnoses($string);
        $resultArray = [];
        /** @var Diagnosis $diagnosis */
        foreach ($diagnoses as $diagnosis) {
            $resultArray[] = [
                'id' => $diagnosis->getId(),
                'text' => $diagnosis->getName()
            ];
        }
        return new Response(
            json_encode(
                $resultArray
            )
        );
    }
}