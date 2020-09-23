<?php

namespace App\Controller\Admin;

use App\Entity\Diagnosis;
use App\Form\Admin\DiagnosisType;
use App\Services\DataTable\Admin\DiagnosisDataTableService;
use App\Services\TemplateBuilders\DiagnosisTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @Route("admin/diagnosis")
 */
class DiagnosisController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/diagnosis/';

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new DiagnosisTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Diagnoses list
     * @Route("/", name="diagnosis_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param DiagnosisDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, DiagnosisDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New diagnosis
     * @Route("/new", name="diagnosis_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Diagnosis()), DiagnosisType::class);
    }

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

    /**
     * Show diagnosis info
     * @Route("/{id}", name="diagnosis_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Diagnosis $diagnosis
     *
     * @return Response
     */
    public function show(Diagnosis $diagnosis): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $diagnosis);
    }

    /**
     * Edit diagnosis
     * @Route("/{id}/edit", name="diagnosis_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Diagnosis $diagnosis
     *
     * @return Response
     */
    public function edit(Request $request, Diagnosis $diagnosis): Response
    {
        return $this->responseEdit($request, $diagnosis, DiagnosisType::class);
    }

    /**
     * Delete diagnosis
     * @Route("/{id}", name="diagnosis_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Diagnosis $diagnosis
     *
     * @return Response
     */
    public function delete(Request $request, Diagnosis $diagnosis): Response
    {
        return $this->responseDelete($request, $diagnosis);
    }
}
