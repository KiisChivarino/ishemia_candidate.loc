<?php

namespace App\Controller;

use App\Repository\AnalysisGroupRepository;
use App\Repository\CityRepository;
use App\Repository\ComplaintRepository;
use App\Repository\DiagnosisRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public const JSON_PARAMETER_KEY = 'q';

    public const AJAX_INIT_CSS_CLASS = 'js-ajax-init';

    /**
     * Find diagnoses
     * @Route("/find_diagnosis_ajax", name="find_diagnosis_ajax", methods={"GET"})
     *
     * @param Request $request
     *
     * @param DiagnosisRepository $diagnosisRepository
     * @return false|string
     */
    public function findDiagnosisAjax(
        Request $request,
        DiagnosisRepository $diagnosisRepository
    )
    {
        return $this->responseAjaxResult(
            $diagnosisRepository->findDiagnoses(
                $request->query->get(self::JSON_PARAMETER_KEY)
            )
        );
    }

    /**
     * Find diagnoses MKBCode
     * @Route("/find_diagnosis_mkbcode_ajax", name="find_diagnosis_mkbcode_ajax", methods={"GET"})
     *
     * @param Request $request
     *
     * @param DiagnosisRepository $diagnosisRepository
     * @return false|string
     */
    public function findMKBCodeAjax(
        Request $request,
        DiagnosisRepository $diagnosisRepository
    )
    {
        return $this->responseAjaxResult(
            $diagnosisRepository->findDiagnoses(
                $request->query->get(self::JSON_PARAMETER_KEY)
            ),
            "Code"
        );
    }

    /**
     * Return complaints using ajax
     * @Route("/find_complaint_ajax", name="find_complaint_ajax", methods={"GET"})
     * @param Request $request
     *
     * @param ComplaintRepository $complaintRepository
     * @return Response
     */
    public function findComplaintAjax(Request $request, ComplaintRepository $complaintRepository)
    {
        return $this->responseAjaxResult(
            $complaintRepository->findComplaints(
                $request->query->get(self::JSON_PARAMETER_KEY)
            )
        );
    }

    /**
     * Find cities
     * @Route("/find_city_ajax", name="find_city_ajax", methods={"GET"})
     * @param Request $request
     *
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function findCityAjax(Request $request, CityRepository $cityRepository): Response
    {
        return $this->responseAjaxResult(
            $cityRepository->findCities(
                $request->query->get(self::JSON_PARAMETER_KEY)
            )
        );
    }

    /**
     * Ищет аяксом больницы
     * @Route("/find_hospital_ajax", name="find_hospital_ajax", methods={"GET"})
     *
     * @param Request $request
     *
     * @param HospitalRepository $hospitalRepository
     * @return false|string
     */
    public function findHospitalAjax(Request $request, HospitalRepository $hospitalRepository)
    {
        $city = $request->get('city');
        return $this->responseAjaxResult(
            $hospitalRepository->findHospitals(
                $request->query->get(self::JSON_PARAMETER_KEY), 
                $city
            )
        );
    }

    /**
     * Find analysis group by ajax
     * @Route("/find_analysis_group_ajax", name="find_analysis_group_ajax", methods={"GET"})
     *
     * @param Request $request
     * @param AnalysisGroupRepository $analysisGroupRepository
     * @return JsonResponse
     */
    public function findAnalysisGroupAjax(Request $request, AnalysisGroupRepository $analysisGroupRepository): JsonResponse
    {
        return $this->responseAjaxResult(
            $analysisGroupRepository->findAnalysisGroups(
                $request->query->get(self::JSON_PARAMETER_KEY)
            )
        );
    }

    /**
     * Returns result array for ajax
     *
     * @param $entities
     * @param null $textFieldName
     *
     * @return JsonResponse
     */
    private function responseAjaxResult($entities, $textFieldName = null): JsonResponse
    {
        $textMethodName = 'get' . ucfirst($textFieldName);
        $resultArray = [];
        foreach ($entities as $entity) {
            $resultArray[] = [
                'id' => $entity->getId(),
                'text' => $textFieldName ? $entity->$textMethodName() : $entity->getName(),
            ];
        }
        return new JsonResponse(
            $resultArray
        );
    }
}