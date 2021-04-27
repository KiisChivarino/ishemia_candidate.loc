<?php

namespace App\Controller\Admin;

use App\Entity\Template;
use App\Entity\TemplateType;
use App\Form\Admin\TemplateEditType;
use App\Form\Admin\TemplateNewType;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateTypeRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\TemplateDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\TemplateTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TextTemplateService\TextTemplateService;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Template entity controllers (Контроллеры сущности "Шаблон")
 * @Route("/admin/template")
 * @IsGranted("ROLE_MANAGER")
 */
class TemplateController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/template/';

    /** @var int Id of therapy template type */
    public const THERAPY_TEMPLATE_TYPE_ID = 4;
    /** @var int Id of objective status template type */
    public const OBJECTIVE_STATUS_TEMPLATE_ID = 3;
    /** @var int Id of life anamnesis template type */
    public const LIFE_ANAMNESIS_TEMPLATE_ID = 1;

    /**
     * AnalysisGroupController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->templateService = new TemplateTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of templates (Список шаблонов)
     * @Route("/", name="template_list", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TemplateDataTableService $dataTableService
     *
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
     */
    public function list(
        Request $request,
        TemplateDataTableService $dataTableService,
        FilterService $filterService
    ): Response
    {
        return $this->responseList(
            $request,
            $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [
                    self::FILTER_LABELS['TEMPLATE_TYPE'],
                ]
            )
        );
    }

    /**
     * New template of therapy template type
     * @Route("/new/therapy", name="template_new_therapy", methods={"GET","POST"})
     * @param Request $request
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @return Response
     * @throws Exception
     */
    public function newTherapyTemplate(
        Request $request,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService
    ): Response
    {
        return $this->responseNewTemplateOfTextParameters(
            $request,
            $templateParameterRepository,
            $templateTypeRepository,
            $textTemplateService,
            self::THERAPY_TEMPLATE_TYPE_ID
        );
    }

    /**
     * New template of objective status template type
     * @Route("/new/objective_status", name="template_new_objective_status", methods={"GET","POST"})
     * @param Request $request
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @return Response
     * @throws Exception
     */
    public function newObjectiveStatusTemplate(
        Request $request,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService
    ): Response
    {
        return $this->responseNewTemplateOfTextParameters(
            $request,
            $templateParameterRepository,
            $templateTypeRepository,
            $textTemplateService,
            self::OBJECTIVE_STATUS_TEMPLATE_ID
        );
    }

    /**
     * New template of life anamnesis template type
     * @Route("/new/life_anamnesis", name="template_new_life_anamnesis", methods={"GET","POST"})
     * @param Request $request
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @return Response
     * @throws Exception
     */
    public function newLifeAnamnesisTemplate(
        Request $request,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService
    ): Response
    {
        return $this->responseNewTemplateOfTextParameters(
            $request,
            $templateParameterRepository,
            $templateTypeRepository,
            $textTemplateService,
            self::LIFE_ANAMNESIS_TEMPLATE_ID
        );
    }

    /**
     * Template parameter info
     * @Route("/{id}", name="template_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Template $template
     *
     * @return Response
     * @throws Exception
     */
    public function show(Template $template): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $template);
    }

    /**
     * Edit template parameter
     * @Route("/{id}/edit", name="template_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Template $template
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TextTemplateService $textTemplateService
     * @return Response
     * @throws Exception
     */
    public function edit(
        Request $request,
        Template $template,
        TemplateParameterRepository $templateParameterRepository,
        TextTemplateService $textTemplateService
    ): Response
    {
        $parameters = $templateParameterRepository->findBy([
            'templateType' => $template->getTemplateType()
        ]);
        return $this->responseEdit(
            $request,
            $template,
            TemplateEditType::class,
            [
                'parameters' => $parameters,
                'template' => $template,
            ],
            function (EntityActions $actions) use ($textTemplateService, $template) {
                $textTemplateService->clearTemplateParameterTexts($template);
                $actions->getEntityManager()->flush();
                $textTemplateService->persistTemplateParameterTexts($actions->getForm()->all(), $template);
            }
        );
    }

    /**
     * Delete template type
     * @Route("/{id}", name="template_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Template $template
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Template $template): Response
    {
        return $this->responseDelete($request, $template);
    }

    /**
     * Response new template of text by parameters
     * @param Request $request
     * @param TemplateParameterRepository $templateParameterRepository
     * @param TemplateTypeRepository $templateTypeRepository
     * @param TextTemplateService $textTemplateService
     * @param $templateTypeId
     * @return RedirectResponse|Response
     * @throws Exception
     */
    private function responseNewTemplateOfTextParameters(
        Request $request,
        TemplateParameterRepository $templateParameterRepository,
        TemplateTypeRepository $templateTypeRepository,
        TextTemplateService $textTemplateService,
        $templateTypeId
    )
    {
        /** @var TemplateType $templateType */
        $templateType = $templateTypeRepository->find($templateTypeId);
        $parameters = $templateParameterRepository->findBy(['templateType' => $templateType, 'enabled' => true]);
        $textTemplate = (new Template())
            ->setTemplateType($templateType)
            ->setEnabled(true);
        $template = $this->templateService->newTemplate($textTemplate->getTemplateType());
        return $this->responseFormTemplate(
            $request,
            $textTemplate,
            $this->createForm(TemplateNewType::class, $textTemplate,
                [
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                    'parameters' => $parameters,
                ]
            ),
            self::RESPONSE_FORM_TYPE_NEW,
            function (EntityActions $actions) use ($textTemplate, $textTemplateService) {
                $textTemplateService->persistTemplateParameterTexts($actions->getForm()->all(), $textTemplate);
            },
            self::RESPONSE_FORM_TYPE_NEW
        );
    }


}
