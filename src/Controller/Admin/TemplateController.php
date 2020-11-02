<?php

namespace App\Controller\Admin;

use App\Entity\Template;
use App\Entity\TemplateManyToManyTemplateParameterText;
use App\Entity\TemplateParameterText;
use App\Form\Admin\TemplateEditType;
use App\Form\Admin\TemplateNewType;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateTypeRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\DataTable\Admin\TemplateDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\TemplateTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Контроллеры сущности "Параметр шаблона"
 * @Route("/admin/template")
 * @IsGranted("ROLE_ADMIN")
 */
class TemplateController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/template/';

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new TemplateTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Новый параметр шаблона
     * @Route("/new/{type}", name="template_new", methods={"GET","POST"})
     *
     * @param $type
     * @param Request $request
     *
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @return Response
     */
    public function new($type, Request $request, TemplateParameterRepository $templateParameterRepository, TemplateTypeRepository $templateTypeRepository): Response
    {
        switch ($type) {
            case 'disease_analysis':
                $templateType = $templateTypeRepository->findOneBy([
                    'id' => 2
                ]);
                break;
            case 'objective_status':
                $templateType = $templateTypeRepository->findOneBy([
                    'id' => 3
                ]);
                break;
            case 'therapy':
                $templateType = $templateTypeRepository->findOneBy([
                    'id' => 4
                ]);
                break;
            case 'life_analysis':
                $templateType = $templateTypeRepository->findOneBy([
                    'id' => 1
                ]);
                break;
            default:
                $templateType = null;
                break;
        }
        $parameters = $templateParameterRepository->findBy([
            'templateType' => $templateType
                ]);
        $template = new Template();
        $template->setTemplateType($templateType);
        return $this->responseNew(
            $request,
            $template,
            TemplateNewType::class,
            null,
            ['parameters' => $parameters],
            function (EntityActions $actions) {
                $entityManager = $this->getDoctrine()->getManager();
//                /** @var MedicalHistory $medicalHistory */
                $template = $actions->getEntity();
                $data = $actions->getRequest()->request->all();

                foreach($data['template_new'] as $key => $value){
                    $exp_key = explode('-', $key);
                    if($exp_key[0] == 'parameter'){
                        $arr_result[] = $value;
                    }
                }
                foreach ($arr_result as $item) {
                    $templateManyToManyParameterText = new TemplateManyToManyTemplateParameterText();
                    $templateManyToManyParameterText->setTemplate($template);
                    $templateManyToManyParameterText->setTemplateParameterText(
                        $entityManager->getRepository(TemplateParameterText::class)->findOneBy([
                            'id' => $item
                        ])
                    );
                    $entityManager->persist($templateManyToManyParameterText);
                }
                $entityManager->flush();
//                    ? $entityManager->getRepository(MedicalHistory::class)->find($actions->getRequest()->query->get('medical_history_id'))
//                    : null;
//                $actions->getEntity()->setMedicalHistory($medicalHistory);
//                $this->prepareFiles($actions->getForm()->get(self::FILES_COLLECTION_PROPERTY_NAME));
//                $entityManager->getRepository(PatientTestingResult::class)->persistTestingResultsForTesting($actions->getEntity());
            }
        );
    }

    /**
     * Список параметров шаблонов
     * @Route("/", name="template_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TemplateDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, TemplateDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="template_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Template $template
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(Template $template, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH,
            $template,
            [
                'templateFilterName' => $filterService->generateFilterName('template', Template::class)
            ]
        );
    }

    /**
     * Edit template parameter
     * @Route("/{id}/edit", name="template_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Template $template
     * @param TemplateParameterRepository $templateParameterRepository
     * @return Response
     */
    public function edit(Request $request, Template $template, TemplateParameterRepository $templateParameterRepository): Response
    {
//        dd($template->getTemplateManyToManyTemplateParameterTexts()[1]->getTemplateParameterText()->getText());
        $parameters = $templateParameterRepository->findBy([
            'templateType' => $template->getTemplateType()
        ]);
//        foreach ($parameters as $parameter) {
//            foreach ($template->getTemplateManyToManyTemplateParameterTexts() as $text) {
//                if ($text->getTemplateParameterText()->getTemplateParameter()->getId() == $parameter->getId()) {
//
//                }
//            }
//        }
        return $this->responseEdit(
            $request,
            $template,
            TemplateEditType::class,
            [
                'parameters' => $parameters,
                'template' => $template,
            ],
            function (EntityActions $actions) {
                $entityManager = $this->getDoctrine()->getManager();
//                /** @var MedicalHistory $medicalHistory */
                $template = $actions->getEntity();
                $data = $actions->getRequest()->request->all();
//                dd($data);
                foreach($data['template_edit'] as $key => $value){
                    $exp_key = explode('-', $key);
                    if($exp_key[0] == 'parameter'){
                        $arr_result[] = ['id' => $exp_key[2], 'value' => $value];
                    }
                }
//                dd($arr_result);
                foreach ($arr_result as $item) {
                    if ($item['id'] == 'new') {
                        $templateManyToManyParameterText = new TemplateManyToManyTemplateParameterText();
                        $templateManyToManyParameterText->setTemplate($template);
                    } else {
                        $templateManyToManyParameterText = $entityManager
                            ->getRepository(TemplateManyToManyTemplateParameterText::class)
                            ->findOneBy([
                                'template' => $template,
                                'id' => $item['id']
                            ])
                        ;
                    }
//                    dd($templateManyToManyParameterText);
                    $templateManyToManyParameterText->setTemplateParameterText(
                        $entityManager->getRepository(TemplateParameterText::class)->findOneBy([
                            'id' => $item['value']
                        ])
                    );
                    $entityManager->persist($templateManyToManyParameterText);
                }
                $entityManager->flush();
//                    ? $entityManager->getRepository(MedicalHistory::class)->find($actions->getRequest()->query->get('medical_history_id'))
//                    : null;
//                $actions->getEntity()->setMedicalHistory($medicalHistory);
//                $this->prepareFiles($actions->getForm()->get(self::FILES_COLLECTION_PROPERTY_NAME));
//                $entityManager->getRepository(PatientTestingResult::class)->persistTestingResultsForTesting($actions->getEntity());
            }
        );
    }

    /**
     * Delete template type
     * @Route("/{id}", name="template_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Template $template
     * @return Response
     */
    public function delete(Request $request, Template $template): Response
    {
        return $this->responseDelete($request, $template);
    }
}
