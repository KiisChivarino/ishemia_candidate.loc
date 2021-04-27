<?php

namespace App\Controller\Admin;

use App\Entity\ClinicalDiagnosis;
use App\Form\Admin\ClinicalDiagnosis\ClinicalDiagnosisType;
use App\Form\Admin\ClinicalDiagnosis\DiseasesType;
use App\Services\DataTable\Admin\ClinicalDiagnosisDataTableService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\ClinicalDiagnosisTemplate;
use Exception;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class ClinicalDiagnosisController
 * @Route("admin/clinical_diagnosis")
 * @IsGranted("ROLE_MANAGER")
 * @package App\Controller\Admin
 */
class ClinicalDiagnosisController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/clinical_diagnosis/';

    /**
     * ClinicalDiagnosisController constructor.
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($translator);
        $this->templateService = new ClinicalDiagnosisTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of clinical diagnoses
     * @Route("/", name="clinical_diagnosis_list", methods={"GET", "POST"})
     * @param Request $request
     * @param ClinicalDiagnosisDataTableService $dataTableService
     * @return Response
     */
    public function list(Request $request, ClinicalDiagnosisDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New clinical diagnosis
     * @Route("/new", name="clinical_diagnosis_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $clinicalDiagnosis = new ClinicalDiagnosis();
        return $this->responseNewMultiForm(
            $request,
            $clinicalDiagnosis,
            [
                new FormData(ClinicalDiagnosisType::class, $clinicalDiagnosis),
                new FormData(DiseasesType::class, $clinicalDiagnosis),
            ]);
    }

    /**
     * Show clinical diagnosis info
     * @Route("/{id}", name="clinical_diagnosis_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param ClinicalDiagnosis $clinicalDiagnosi
     * @return Response
     * @throws Exception
     */
    public function show(ClinicalDiagnosis $clinicalDiagnosi): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $clinicalDiagnosi);
    }

    /**
     * Edit clinical diagnosis
     * @Route("/{id}/edit", name="clinical_diagnosis_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param ClinicalDiagnosis $clinicalDiagnosis
     * @return Response
     * @throws ReflectionException
     */
    public function edit(Request $request, ClinicalDiagnosis $clinicalDiagnosis): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $clinicalDiagnosis,
            [
                new FormData(ClinicalDiagnosisType::class, $clinicalDiagnosis),
                new FormData(DiseasesType::class, $clinicalDiagnosis),
            ]);
    }

    /**
     * Delete clinical diagnosis
     * @Route("/{id}", name="clinical_diagnosis_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param ClinicalDiagnosis $clinicalDiagnosis
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, ClinicalDiagnosis $clinicalDiagnosis): Response
    {
        return $this->responseDelete($request, $clinicalDiagnosis);
    }
}
