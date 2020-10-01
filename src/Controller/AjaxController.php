<?php


namespace App\Controller;


use App\Entity\Complaint;
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
        return new Response(
            json_encode(
                $this->getAjaxResultArray($diagnoses)
            )
        );
    }

    /**
     * @Route("/find_complaint_ajax", name="find_complaint_ajax", methods={"GET"})
     * @param Request $request
     *
     * @return Response
     */
    public function findComplaintAjax(Request $request)
    {
        $string = $request->query->get('q');
        $entityManager = $this->getDoctrine()->getManager();
        $complaints = $entityManager->getRepository(Complaint::class)->findComplaints($string);
        return new Response(
            json_encode(
                $this->getAjaxResultArray($complaints)
            )
        );
    }

    /**
     * Returns result array for ajax
     *
     * @param $entities
     * @param null $textFieldName
     *
     * @return array
     */
    private function getAjaxResultArray($entities, $textFieldName = null): array
    {
        $textMethodName = 'get'.ucfirst($textFieldName);
        $resultArray = [];
        foreach ($entities as $entity) {
            $resultArray[] = [
                'id' => $entity->getId(),
                'text' => $textFieldName ? $entity->$textMethodName() : $entity->getName(),
            ];
        }
        return $resultArray;
    }
}