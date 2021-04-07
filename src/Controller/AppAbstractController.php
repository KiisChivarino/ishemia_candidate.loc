<?php

namespace App\Controller;

use App\Controller\Admin\MedicalHistoryController;
use App\Controller\Admin\PrescriptionController;
use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\EntityActions\Builder\EntityActionsBuilder;
use App\Services\EntityActions\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Builder\EditorEntityActionsBuilder;
use App\Services\EntityActions\Editor\AbstractEditorService;
use App\Services\EntityActions\EntityActionsInterface;
use App\Services\LoggerService\LogService;
use App\Services\MultiFormService\FormData;
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
        $this->templateService->edit();
        return $this->responseFormTemplate(
            $request,
            $entity,
            $this->createForm(
                $typeClass, $formEntity ? $formEntity : $entity,
                array_merge($customFormOptions, [self::FORM_TEMPLATE_ITEM_OPTION_TITLE =>
                    $this->templateService->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),])
            ),
            $formName,
            $entityActions,
            self::RESPONSE_FORM_TYPE_EDIT
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
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($entity);
                $entityManager->flush();
                $this->setLogDelete($entityName, $entity);
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
    protected function responseFormTemplate(
        Request $request,
        object $entity,
        FormInterface $form,
        string $formName,
        ?Closure $entityActions = null,
        string $type = null
    )
    {
        $renderForm = $this->renderForm($formName, $this->getRenderFormParameters($form, ['entity' => $entity]));
        if (!$this->handleRequest($request, $form)) {
            return $renderForm;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($entityActions) {
                $entityActionsObject = new EntityActions($entity, $request, $entityManager, $form);
                $actionsResult = $entityActions($entityActionsObject);
                if (is_a($actionsResult, Response::class)) {
                    return $actionsResult;
                }
            }
            $entityManager->persist($entity);
            $this->setFormLog($type, $entity);
            if (!$this->flush()) {
                return $renderForm;
            }
            $this->setDefaultRedirectRouteParameters($entity);
            return $this->redirectSubmitted();
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $value) {
                $this->addFlash('error', $value->getMessage());
            }
        }
        return $renderForm;
    }

    /**
     * Response new form using Creator service
     * @param Request $request
     * @param CreatorEntityActionsBuilder $creatorEntityActionsBuilder
     * @param FormData $formData
     * @param FilterLabels|null $filterLabels
     * @param string $formTemplateName
     * @return RedirectResponse|Response
     * @throws Exception
     */
    protected function responseNewWithActions(
        Request $request,
        CreatorEntityActionsBuilder $creatorEntityActionsBuilder,
        FormData $formData,
        ?FilterLabels $filterLabels = null,
        string $formTemplateName = self::RESPONSE_FORM_TYPE_NEW
    )
    {
        /** @var TemplateService $template */
        $template = $this->templateService->new($filterLabels ? $filterLabels->getFilterService() : null);
        $formData->setFormOptions(array_merge(
                $formData->getFormOptions(),
                $filterLabels ? $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray()) : [],
                [
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
                ]
            )
        );
        return $this->responseFormWithActions(
            $request,
            $creatorEntityActionsBuilder,
            $formData,
            $formTemplateName,
            self::RESPONSE_FORM_TYPE_NEW
        );
    }

    /**
     * Response edit form using Editor Service
     * @param Request $request
     * @param EditorEntityActionsBuilder $editorEntityActionsBuilder
     * @param FormData $formData
     * @param string $templateEditName
     * @return Response
     * @throws Exception
     */
    protected function responseEditWithActions(
        Request $request,
        EditorEntityActionsBuilder $editorEntityActionsBuilder,
        FormData $formData,
        string $templateEditName = self::RESPONSE_FORM_TYPE_EDIT
    )
    {
        $this->templateService->edit();
        return $this->responseFormWithActions(
            $request,
            $editorEntityActionsBuilder,
            $formData,
            $templateEditName,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * @param Request $request
     * @param EditorEntityActionsBuilder[] $editorEntityActionsBuilderArray
     * @param array $formDataArray
     * @param string $templateEditName
     * @return RedirectResponse|Response
     * @throws Exception
     */
    protected function responseEditMultiFormWithActions(
        Request $request,
        array $editorEntityActionsBuilderArray,
        array $formDataArray,
        string $templateEditName = self::RESPONSE_FORM_TYPE_EDIT
    )
    {
        $this->templateService->edit();
        return $this->responseMultiFormWithActions(
            $request,
            $editorEntityActionsBuilderArray,
            $formDataArray,
            $templateEditName,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Response several forms for creating using service of actions with entity
     * @param Request $request
     * @param array $creatorEntityActionsBuilderArray
     * @param array $formDataArray
     * @param string $templateEditName
     * @return RedirectResponse|Response
     * @throws ReflectionException
     */
    protected function responseNewMultiFormWithActions(
        Request $request,
        array $creatorEntityActionsBuilderArray,
        array $formDataArray,
        string $templateEditName = self::RESPONSE_FORM_TYPE_NEW
    )
    {
        $this->templateService->new();
        return $this->responseMultiFormWithActions(
            $request,
            $creatorEntityActionsBuilderArray,
            $formDataArray,
            $templateEditName,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Response several forms using service of actions with entity
     * @param Request $request
     * @param array $entityActionsBuilderArray
     * @param array $formDataArray
     * @param string $templateName
     * @param string $type
     * @param null $defaultEntity
     * @return RedirectResponse|Response
     * @throws ReflectionException
     * @throws Exception
     */
    protected function responseMultiFormWithActions(
        Request $request,
        array $entityActionsBuilderArray,
        array $formDataArray,
        string $templateName,
        string $type,
        $defaultEntity = null
    )
    {
        foreach ($entityActionsBuilderArray as $entityActionsBuilder) {
            if (!is_a($entityActionsBuilder->getEntityActionsService(), AbstractEditorService::class)) {
                throw new Exception('EntityActionsBuilder must contains AbstractEditorService');
            }
            $entityActionsBuilder
                ->getEntityActionsService()
                ->before($entityActionsBuilder->getBeforeOptions());
        }
        $defaultEntity = $defaultEntity
            ? $defaultEntity
            : $entityActionsBuilderArray[0]->getEntityActionsService()->getEntity();
        $formGeneratorService = (new MultiFormService())->mergeFormDataOptions(
            $formDataArray,
            [
                'label' => false,
                self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $this->templateService
                    ->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
            ]
        );
        $form = $formGeneratorService->generateForm($this->createFormBuilder(), $formDataArray);
        $renderParameters = $this->getRenderFormParameters(
            $form,
            [
                'entity' => $defaultEntity
            ]
        );
        $formRender = $this->renderForm($templateName, $renderParameters);
        if (!$this->handleRequest($request, $form)) {
            return $formRender;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($entityActionsBuilderArray as $editorEntityActionsBuilder) {
                /** @var EntityActionsInterface $entityActionsEditorService */
                $entityActionsEditorService = $editorEntityActionsBuilder->getEntityActionsService();
                $entityActionsEditorService->after(
                    $editorEntityActionsBuilder->getAfterOptions()($entityActionsEditorService)
                );
            }
            if (!$this->flush()) {
                return $formRender;
            }
            foreach ($entityActionsBuilderArray as $editorEntityActionsBuilder) {
                $this->setFormLog($type, $editorEntityActionsBuilder->getEntityActionsService()->getEntity());
            }
            $this->setDefaultRedirectRouteParameters($defaultEntity);
            return $this->redirectSubmitted();
        }
        return $formRender;
    }

    /**
     * Response form using service of entity actions
     * @param Request $request
     * @param EntityActionsBuilder $entityActionsBuilder
     * @param FormData $formData
     * @param string $templateName
     * @param string $type
     * @return RedirectResponse|Response
     * @throws Exception
     */
    private function responseFormWithActions(
        Request $request,
        EntityActionsBuilder $entityActionsBuilder,
        FormData $formData,
        string $templateName,
        string $type
    )
    {
        $entityActionsService = $entityActionsBuilder->getEntityActionsService();
        $entityActionsService->before($entityActionsBuilder->getBeforeOptions());
        $defaultEntity = $entityActionsService->getEntity();
        $form = $this->createForm(
            $formData->getFormClassName(),
            $defaultEntity,
            array_merge(
                $formData->getFormOptions(),
                [
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE =>
                        $this->templateService->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
        );
        $renderParameters = $this->getRenderFormParameters($form);
        $formRender = $this->renderForm($templateName, $renderParameters);
        if (!$this->handleRequest($request, $form)) {
            return $formRender;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityActionsService->after(
                $entityActionsBuilder->getAfterOptions()($entityActionsService)
            );
            if (!$this->flush()) {
                return $formRender;
            }
            $this->setFormLog($type, $defaultEntity);
            $this->setDefaultRedirectRouteParameters($defaultEntity);
            return $this->redirectSubmitted();
        }
        return $formRender;
    }

    /**
     * Handle request of form
     * @param Request $request
     * @param FormInterface $form
     * @return bool
     */
    protected function handleRequest(Request $request, FormInterface $form): bool
    {
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                'Неизвестная ошибка в данных! Проверьте данные или обратитесь к администратору...'
            );
            return false;
        }
        return true;
    }

    /**
     * Render form before submit
     * @param string $formName
     * @param array $renderParameters
     * @return Response
     * @throws Exception
     */
    protected function renderForm(string $formName, array $renderParameters): Response
    {
        return $this->render(
            $this->templateService->getTemplateFullName(
                $formName,
                $this->getParameter('kernel.project_dir')),
            $renderParameters
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
     * Set log of create object
     * @param $entity
     * @throws Exception
     */
    protected function setLogCreate($entity)
    {
        (new LogService($this->getDoctrine()->getManager()))
            ->setUser($this->getUser())
            ->setDescription(
                $this->translator->trans(
                    'log.new.entity',
                    [
                        '%entity%' =>
                            $this->templateService
                                ->getItem(self::RESPONSE_FORM_TYPE_NEW)
                                ->getContentValue('entity'),
                        '%id%' => $entity->getId()
                    ]
                )
            )
            ->logCreateEvent();
    }

    /**
     * Set log for form by it`s type
     * @param string $type
     * @param $entity
     * @throws Exception
     */
    protected function setFormLog(string $type, $entity): void
    {
        switch ($type) {
            case 'new':
                $this->setLogCreate($entity);
                break;
            case 'edit':
                $this->setLogUpdate($entity);
                break;
        }
    }

    /**
     * Set log of update entity object
     * @param $entity
     * @throws Exception
     */
    protected function setLogUpdate($entity): void
    {
        (new LogService($this->getDoctrine()->getManager()))
            ->setUser($this->getUser())
            ->setDescription(
                $this->translator->trans(
                    'log.update.entity',
                    [
                        '%entity%' => $this->templateService
                            ->getItem(self::RESPONSE_FORM_TYPE_EDIT)
                            ->getContentValue('entity'),
                        '%id%' => $entity->getId()
                    ]
                )
            )
            ->logUpdateEvent();
    }

    /**
     * Set log of delete entity object
     * @param string $entityName
     * @param $entity
     */
    protected function setLogDelete(string $entityName, $entity): void
    {
        $entityManager = $this->getDoctrine()->getManager();
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
        $entityManager->flush();
    }

    /**
     * Redirect this way if form isValid and isSubmitted
     * @return RedirectResponse
     */
    protected function redirectSubmitted(): RedirectResponse
    {
        return $this->redirectToRoute(
            $this->templateService->getRoute($this->templateService->getRedirectRouteName()),
            $this->templateService->getRedirectRouteParameters()
        );
    }

    /**
     * Flush
     * @return bool
     */
    protected function flush(): bool
    {
        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (DBALException $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_abstract_controller.error.dbal_exception')
            );
            return false;
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_abstract_controller.error.exception'));
            return false;
        }
        $this->addFlash(
            'success',
            $this->translator->trans(
                'app_abstract_controller.success.add'
            )
        );
        return true;
    }

    /**
     * Returns default parameters of form and adds custom parameters if they exist
     * @param FormInterface $form
     * @param array $customRenderParameters
     * @return array
     */
    protected function getRenderFormParameters(FormInterface $form, array $customRenderParameters = []): array
    {
        return array_merge(
            [
                'form' => $form->createView(),
                'filters' =>
                    $this->templateService
                        ->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                        ->getFiltersViews(),
            ],
            $customRenderParameters
        );
    }

    /**
     * Sets default route parameters if they haven`t been added before
     * @param $entity
     */
    protected function setDefaultRedirectRouteParameters($entity): void
    {
        if ($this->templateService->getRedirectRouteParameters() == null) {
            $this->templateService->setRedirectRouteParameters(
                [
                    'id' => $entity->getId()
                ]
            );
        }
    }

    /**
     * Returns parameter from GET request array
     * @param Request $request
     * @param string $parameterKey
     * @return mixed|RedirectResponse
     * @throws Exception
     */
    protected function getGETParameter(Request $request, string $parameterKey): int
    {
        if ($parameter = $request->query->get($parameterKey)) {
            return $parameter;
        } else {
            throw new Exception($this->translator->trans('app_controller.error.parameter_not_found'));
        }
    }

    /**
     * Returns entity by id
     * @param $entityClass
     * @param int $parameter
     * @return object|RedirectResponse
     * @throws Exception
     */
    protected function getEntityById($entityClass, int $parameter)
    {
        $entity = $this->getDoctrine()->getManager()->getRepository($entityClass)->find($parameter);
        if ($entity === null || !is_a($entity, $entityClass)) {
            throw new Exception($this->translator->trans('app_controller.error.parameter_not_found'));
        }
        return $entity;
    }

    /**
     * Returns MedicalHistory entity by GET parameter
     * @param Request $request
     * @return MedicalHistory|object|RedirectResponse
     * @throws Exception
     * @todo сделать нормальные роуты в админке и убрать этот дебильный метод!!!
     */
    protected function getMedicalHistoryByParameter(Request $request): MedicalHistory
    {
        /** @var MedicalHistory $medicalHistory */
        return $this->getEntityById(
            MedicalHistory::class,
            $this->getGETParameter($request, MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)
        );
    }

    /**
     * Returns Prescription entity by GET parameter
     * @param Request $request
     * @return Prescription|object|RedirectResponse
     * @throws Exception
     * @todo сделать нормальные роуты в админке и убрать этот дебильный метод!!!
     */
    protected function getPrescriptionByParameter(Request $request): Prescription
    {
        /** @var Prescription $prescription */
        return $this->getEntityById(
            Prescription::class,
            $this->getGETParameter($request, PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)
        );
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
