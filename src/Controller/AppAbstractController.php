<?php

namespace App\Controller;

use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\LoggerService\LogService;
use App\Services\MultiFormService\MultiFormService;
use App\Services\Template\TemplateService;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\DBAL\DBALException;
use Exception;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class AppAbstractController
 *
 * @package App\Controller\Admin
 */
abstract class AppAbstractController extends AbstractController
{
    /** @var TemplateService $adminTemplateService */
    protected $templateService;

    /** @var string "new" type of form */
    protected const RESPONSE_FORM_TYPE_NEW = 'new';

    const FOREIGN_KEY_ERROR = '23503';

    /** @var string[] Labels of filters */
    public const FILTER_LABELS = [
        'ANALYSIS_GROUP' => 'analysisGroup',
        'PATIENT' => 'patient',
        'PATIENT_TESTING' => 'patientTesting',
        'HOSPITAL' => 'hospital',
        'MEDICAL_HISTORY' => 'medicalHistory',
        'STAFF' => 'staff',
        'PRESCRIPTION' => 'prescription',
        'TEMPLATE_TYPE' => 'templateType',
        'TEMPLATE_PARAMETER' => 'templateParameter',
        'LOG_ACTION' => 'logAction',
        'NOTIFICATION' => 'notification'
    ];

    /** @var string Label of form option for adding formTemplateItem in form */
    public const FORM_TEMPLATE_ITEM_OPTION_TITLE = 'formTemplateItem';

    /** @var string "edit" type of form */
    protected const RESPONSE_FORM_TYPE_EDIT = 'edit';

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Отображает действия с записью в таблице datatables
     *
     * @return Closure
     */
    protected function renderTableActions(): Closure
    {
        return function (int $enityId, $rowEntity, $route = null, ?array $routeParameters = []) {
            return $this->getTableActionsResponseContent($enityId, $rowEntity, $route, $routeParameters);
        };
    }

    /**
     * Sets variable template for templates
     *
     * @param Environment $twig
     * @param string $varName
     */
    protected function setTemplateTwigGlobal(Environment $twig, string $varName = 'template'): void
    {
        $twig->addGlobal($varName, $this->templateService);
    }

    /**
     * Отображает шаблон вывода списка элементов с использованием datatable
     *
     * @param Request $request
     * @param $dataTableService
     * @param FilterLabels|null $filterLabels
     * @param array|null $options
     * @param Closure|null $listActions
     * @return Response
     */
    public function responseList(
        Request $request,
        $dataTableService,
        ?FilterLabels $filterLabels = null,
        ?array $options = [],
        ?Closure $listActions = null
    ): Response
    {
        $template = $this->templateService->list($filterLabels ? $filterLabels->getFilterService() : null);

        if ($filterLabels) {
            $filters = $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray());
        }
        $table = $dataTableService->getTable(
            $this->renderTableActions(),
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME),
            $filters ?? null,
            $options
        );
        if ($listActions) {
            $listActions();
        }
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render(
            $template->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->getPath() . 'list.html.twig',
            [
                'datatable' => $table,
                'filters' => $template->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getFiltersViews(),
            ]
        );
    }

    /**
     * Возвращает массив фильтров по меткам
     *
     * @param $template
     * @param $filterLabels
     *
     * @return array
     */
    protected function getFiltersByFilterLabels($template, $filterLabels): array
    {
        $filters = [];
        foreach ($filterLabels as $filterLabel) {
            $filterEntity = $template
                ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                ->getFilterDataByName($filterLabel)
                ->getEntity();
            $filters[$filterLabel] = $filterEntity ? $filterEntity : '';
        }
        return $filters;
    }

    /**
     * Show entity info
     *
     * @param string $templatePath
     * @param object $entity
     * @param array $parameters
     *
     * @return Response
     * @throws Exception
     */
    public function responseShow(string $templatePath, object $entity, array $parameters = []): Response
    {
        $this->templateService->show($entity);
        $parameters['entity'] = $entity;
        return $this->render($templatePath . 'show.html.twig', $parameters);
    }

    /**
     * Response form
     *
     * @param Request $request
     * @param object $entity
     * @param FormInterface $form
     * @param string $formName
     * @param Closure|null $entityActions
     * @param string|null $type
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function responseFormTemplate(
        Request $request,
        object $entity,
        FormInterface $form,
        string $formName,
        ?Closure $entityActions = null,
        string $type = null
    )
    {
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.invalid_handle_request')
            );
            return $this->render(
                $this->templateService->getTemplateFullName(
                    $formName,
                    $this->getParameter('kernel.project_dir')),
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'filters' =>
                        $this->templateService
                            ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                            ->getFiltersViews(),
                ]
            );
        }
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                if ($entityActions) {
                    $entityActionsObject = new EntityActions($entity, $request, $entityManager, $form);
                    $actionsResult = $entityActions($entityActionsObject);
                    if (is_a($actionsResult, Response::class)) {
                        return $actionsResult;
                    }
                }
                $entityName = $this->templateService->getItem($type)->getContentValue('entity');
                $entityManager->persist($entity);
                switch ($type) {
                    case 'new':
                        /** @noinspection PhpParamsInspection */
                        (new LogService($entityManager))
                            ->setUser($this->getUser())
                            ->setDescription(
                                $this->translator->trans(
                                    'log.new.entity',
                                    [
                                        '%entity%' => $entityName,
                                        '%id%' => $entity->getId(),
                                    ]
                                )
                            )
                            ->logCreateEvent();
                        break;
                    case 'edit':
                        /** @noinspection PhpParamsInspection */
                        (new LogService($entityManager))
                            ->setUser($this->getUser())
                            ->setDescription(
                                $this->translator->trans(
                                    'log.update.entity',
                                    [
                                        '%entity%' => $entityName,
                                        '%id%' => $entity->getId(),
                                    ]
                                )
                            )
                            ->logUpdateEvent();
                        break;
                }
                $entityManager->flush();
            } catch (DBALException $e) {
                $this->addFlash('error', $this->translator->trans('app_controller.error.post_dbal_exception'));
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . $formName . '.html.twig',
                    [
                        'entity' => $entity,
                        'form' => $form->createView(),
                    ]
                );
            } catch (Exception $e) {
                $this->addFlash('error', $this->translator->trans('app_controller.error.exception'));
                return $this->render(
                    $this->templateService->getCommonTemplatePath() . $formName . '.html.twig',
                    [
                        'entity' => $entity,
                        'form' => $form->createView(),
                    ]
                );
            }
            $this->addFlash('success', $this->translator->trans('app_controller.success.success_post'));
            return $this->redirectToRoute(
                $this->templateService->getRoute(
                    $this->templateService->getRedirectRouteName()),
                $this->templateService->getRedirectRouteParameters() ?
                    $this->templateService->getRedirectRouteParameters() :
                    [
                        'id' => $entity->getId()
                    ]
            );
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $value) {
                $this->addFlash('error', $value->getMessage());
            }
        }
        return $this->render(
            $this->templateService->getTemplateFullName(
                $formName,
                $this->getParameter('kernel.project_dir')),
            [
                'entity' => $entity,
                'form' => $form->createView(),
                'filters' =>
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                        ->getFiltersViews(),
            ]
        );
    }

    /**
     * Response edit form using multi form formBuilder
     *
     * @param Request $request
     * @param object $entity
     * @param array $formDataArray
     * @param Closure|null $entityActions
     *
     * @param string $templateEditName
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function responseEditMultiForm(
        Request $request,
        object $entity,
        array $formDataArray,
        ?Closure $entityActions = null,
        string $templateEditName = self::RESPONSE_FORM_TYPE_EDIT
    )
    {
        $template = $this->templateService->edit($entity);
        $formGeneratorService = new MultiFormService();
        $formGeneratorService->mergeFormDataOptions(
            $formDataArray,
            [
                'label' => false,
                self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
            ]
        );
        return $this->responseFormTemplate(
            $request,
            $entity,
            $formGeneratorService->generateForm($this->createFormBuilder(), $formDataArray),
            $templateEditName,
            $entityActions,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Response new form using multi form formBuilder
     * @param Request $request
     * @param object $entity
     * @param array $formDataArray
     * @param Closure|null $entityActions
     * @param FilterLabels|null $filterLabels
     * @param string $formName
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function responseNewMultiForm(
        Request $request,
        object $entity,
        array $formDataArray,
        ?Closure $entityActions = null,
        ?FilterLabels $filterLabels = null,
        string $formName = self::RESPONSE_FORM_TYPE_NEW
    )
    {
        if (method_exists($entity, 'setEnabled')) {
            $entity->setEnabled(true);
        }
        $template = $this->templateService->new($filterLabels ? $filterLabels->getFilterService() : null);
        $formGeneratorService = new MultiFormService();
        $formGeneratorService->mergeFormDataOptions(
            $formDataArray,
            [
                'label' => false,
                self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
            ]
        );
        return $this->responseFormTemplate(
            $request,
            $entity,
            $formGeneratorService->generateForm($this->createFormBuilder(), $formDataArray),
            $formName,
            $entityActions,
            self::RESPONSE_FORM_TYPE_NEW
        );
    }

    /**
     * Response edit form
     *
     * @param Request $request
     * @param object $entity
     * @param string $typeClass
     * @param array $customFormOptions
     * @param Closure|null $entityActions
     *
     * @param string $formName
     * @param object|null $formEntity
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function responseEdit(
        Request $request,
        object $entity,
        string $typeClass,
        array $customFormOptions = [],
        ?Closure $entityActions = null,
        string $formName = self::RESPONSE_FORM_TYPE_EDIT,
        object $formEntity = null
    )
    {
        return $this->responseFormTemplate(
            $request,
            $entity,
            $this->createForm(
                $typeClass, $formEntity ? $formEntity : $entity,
                array_merge($customFormOptions, [self::FORM_TEMPLATE_ITEM_OPTION_TITLE =>
                    $this->templateService->edit()->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),])
            ),
            $formName,
            $entityActions,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Response new form
     *
     * @param Request $request
     * @param object $entity
     * @param string $typeClass
     * @param FilterLabels|null $filterLabels
     * @param array $customFormOptions
     * @param Closure|null $entityActions
     *
     * @param string $formName
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function responseNew(
        Request $request,
        object $entity,
        string $typeClass,
        ?FilterLabels $filterLabels = null,
        array $customFormOptions = [],
        ?Closure $entityActions = null,
        string $formName = self::RESPONSE_FORM_TYPE_NEW
    )
    {
        if (method_exists($entity, 'setEnabled')) {
            $entity->setEnabled(true);
        }
        $template = $this->templateService->new($filterLabels ? $filterLabels->getFilterService() : null);
        $options = array_merge(
            $customFormOptions,
            $filterLabels ? $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray()) : []
        );
        $options[self::FORM_TEMPLATE_ITEM_OPTION_TITLE] = $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME);
        return $this->responseFormTemplate(
            $request,
            $entity,
            $this->createForm($typeClass, $entity, $options),
            $formName,
            $entityActions,
            self::RESPONSE_FORM_TYPE_NEW
        );
    }

    /**
     * Delete entity and redirect
     *
     * @param Request $request
     * @param object $entity
     *
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function responseDelete(Request $request, object $entity)
    {
        $this->templateService->delete();
        $entityName = $this->templateService->getItem('delete')->getContentValue('entity');
        if ($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                /** @noinspection PhpParamsInspection */
                (new LogService($entityManager))
                    ->setUser($this->getUser())
                    ->setDescription(
                        $this->translator->trans(
                            'log.delete.entity',
                            [
                                '%entity%' => $entityName,
                                '%id%' => $entity->getId(),
                            ]
                        )
                    )
                    ->logDeleteEvent();
                $entityManager->remove($entity);
                $entityManager->flush();
            } catch (DBALException $e) {
                if ($e->getPrevious()->getCode() == self::FOREIGN_KEY_ERROR) {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('app_controller.error.foreign_key')
                    );
                    return $this->redirectToRoute($this->templateService->getRoute('list'));
                } else {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('app_controller.error.delete_dbal_exception')
                    );
                    return $this->redirectToRoute($this->templateService->getRoute('list'));
                }
            }
        }
        $this->addFlash('success', $this->translator->trans('app_controller.success.success_delete'));
        return $this->redirectToRoute($this->templateService->getRoute('list'));
    }

    /**
     * Render form before submit
     * @param string $formName
     * @param $entity
     * @param $form
     * @return Response
     * @throws Exception
     */
    protected function renderForm(string $formName, $entity, $form): Response
    {
        return $this->render(
            $this->templateService->getTemplateFullName(
                $formName,
                $this->getParameter('kernel.project_dir')),
            [
                'entity' => $entity,
                'form' => $form->createView(),
                'filters' =>
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                        ->getFiltersViews(),
            ]
        );
    }

    /**
     * Gets the response content for table actions
     * @param int $entityId
     * @param $rowEntity
     * @param string|null $route
     * @param array|null $routeParameters
     * @return false|string
     */
    protected function getTableActionsResponseContent(
        int $entityId,
        $rowEntity,
        ?string $route,
        ?array $routeParameters = []
    )
    {
        return $this->render(
            $this->templateService->getCommonTemplatePath() . 'tableActions.html.twig',
            [
                'template' => $this->templateService,
                'parameters' => array_merge(
                    [
                        'id' => $entityId,
                        'rowEntity' => $rowEntity
                    ],
                    $routeParameters
                ),
                'route' => $route
            ]
        )->getContent();
    }
}
