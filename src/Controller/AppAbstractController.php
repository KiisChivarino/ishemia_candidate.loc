<?php

namespace App\Controller;

use App\Controller\Admin\MedicalHistoryController;
use App\Controller\Admin\PrescriptionController;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Repository\MedicalHistoryRepository;
use App\Services\CompletePrescription\CompletePrescriptionService;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\EntityActions\Core\Builder\EntityActionsBuilder;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;
use App\Services\EntityActions\Core\Builder\EditorEntityActionsBuilder;
use App\Services\EntityActions\Core\EntityActionsInterface;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\LoggerService\LogService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\Template\TemplateService;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use App\Utils\Helper;
use Closure;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use InvalidArgumentException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        'NOTIFICATION' => 'notification',
        'CHANNEL_TYPE' => 'channelType'
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
     * @param array|null $renderParameters
     * @return Response
     */
    public function responseList(
        Request $request,
        $dataTableService,
        ?FilterLabels $filterLabels = null,
        ?array $options = [],
        ?Closure $listActions = null,
        ?array $renderParameters = []
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
            array_merge(
                [
                    'datatable' => $table,
                    'filters' => $template->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)->getFiltersViews(),
                ],
                $renderParameters
            )
        );
    }

    /**
     * Show entity info
     *
     * @param string $templatePath
     * @param object $entity
     * @param array $parameters
     * @return Response
     */
    public function responseShow(
        string $templatePath,
        object $entity,
        array $parameters = []
    ): Response
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
                $typeClass, $formEntity ?: $entity,
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
                $entityId = $entity->getId();
                $entityManager->remove($entity);
                $this->setLogDelete($entityName, $entityId);
                $entityManager->flush();
            } catch (DBALException $e) {
                if ($e->getPrevious()->getCode() === self::FOREIGN_KEY_ERROR) {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('app_controller.error.foreign_key')
                    );
                } else {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('app_controller.error.delete_dbal_exception')
                    );
                }
                return $this->redirectToRoute(
                    $this->templateService->getRoute('list'),
                    $this->templateService->getRedirectRouteParameters()
                );
            }
        }
        $this->addFlash('success', $this->translator->trans('app_controller.success.success_delete'));
        return $this->redirectToRoute(
            $this->templateService->getRoute('list'),
            $this->templateService->getRedirectRouteParameters()
        );
    }

    /**
     * Complete prescription
     *
     * @param Prescription $prescription
     * @param CompletePrescriptionService $completePrescriptionService
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function completePrescription(
        Prescription $prescription,
        CompletePrescriptionService $completePrescriptionService
    ): void
    {
        if (PrescriptionInfoService::isSpecialPrescriptionsExists($prescription)) {
            $completePrescriptionService->completePrescription($prescription);
            $this->getDoctrine()->getManager()->flush();
        } else {
            $this->setLogUpdate($prescription, 'log.update.prescription.complete.failed');
        }
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
            return $this->redirectToCurrentRoute($request);
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
                return $this->redirectToCurrentRoute($request);
            }
            $this->setDefaultRedirectRouteParameters($entity);
            return $this->redirectSubmitted();
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->redirectInvalidForm($form, $request);
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
        $entityActionsService = $creatorEntityActionsBuilder->getEntityActionsService();
        $entityActionsService->before($creatorEntityActionsBuilder->getBeforeOptions());
        /** @var TemplateService $template */
        $template = $this->templateService->new(
            $filterLabels ? $filterLabels->getFilterService() : null,
            $creatorEntityActionsBuilder->getEntityActionsService()->getEntity()
        );
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

        $entityActionsService = $editorEntityActionsBuilder->getEntityActionsService();
        $entityActionsService->before($editorEntityActionsBuilder->getBeforeOptions());

        $this->templateService->edit(
            $editorEntityActionsBuilder->getEntityActionsService()->getEntity()
        );

        return $this->responseFormWithActions(
            $request,
            $editorEntityActionsBuilder,
            $formData,
            $templateEditName,
            self::RESPONSE_FORM_TYPE_EDIT
        );
    }

    /**
     * Response template of edit form using form builder and entity actions service
     * @param Request $request
     * @param EditorEntityActionsBuilder[] $editorEntityActionsBuilderArray - array of objects EditorEntityActionsBuilder
     * @param array $formDataArray - array of objects FormDataArray
     * @param null $defaultEntity - default entity for redirect and others, if null will be checked entity of first item from EntityActionsBuilderArray
     * @param string $templateEditName - name of twig template
     * @return RedirectResponse|Response
     * @throws \ReflectionException
     */
    protected function responseEditMultiFormWithActions(
        Request $request,
        array $editorEntityActionsBuilderArray,
        array $formDataArray,
        $defaultEntity = null,
        string $templateEditName = self::RESPONSE_FORM_TYPE_EDIT
    )
    {
        $this->templateService->edit(
            $defaultEntity ?: $editorEntityActionsBuilderArray[0]->getEntityActionsService()->getEntity()
        );
        foreach ($editorEntityActionsBuilderArray as $editorEntityActionsBuilder) {
            $editorEntityActionsBuilder->getEntityActionsService()->before($editorEntityActionsBuilder->getBeforeOptions());
        }
        return $this->responseMultiFormWithActions(
            $request,
            $editorEntityActionsBuilderArray,
            $formDataArray,
            $templateEditName,
            self::RESPONSE_FORM_TYPE_EDIT,
            $defaultEntity
        );
    }

    /**
     * Response several forms for creating using service of actions with entity
     * @param Request $request
     * @param array $creatorEntityActionsBuilderArray - array of EntityActionsBuilders: EntityActionsCreator, options for it before and after submit form
     * @param array $formDataArray - array of form data: name of form class, entity object for form, form options and others
     * @param null $defaultEntity - default entity for redirect and others, if null will be checked entity of first item from EntityActionsBuilderArray
     * @param string $templateName - special name of twig template
     * @return RedirectResponse|Response
     * @throws ReflectionException
     */
    protected function responseNewMultiFormWithActions(
        Request $request,
        array $creatorEntityActionsBuilderArray,
        array $formDataArray,
        $defaultEntity = null,
        string $templateName = self::RESPONSE_FORM_TYPE_NEW
    )
    {
        $this->templateService->new();
        return $this->responseMultiFormWithActions(
            $request,
            $creatorEntityActionsBuilderArray,
            $formDataArray,
            $templateName,
            self::RESPONSE_FORM_TYPE_NEW,
            $defaultEntity
        );
    }

    /**
     * Response several forms using service of actions with entity
     * @param Request $request
     * @param array $entityActionsBuilderArray
     * @param array $formDataArray
     * @param string $templateName
     * @param string $type
     * @param null $defaultEntity - default entity for redirect and others, if null will be checked entity of first item from EntityActionsBuilderArray
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
        $defaultEntity = $defaultEntity ?: $entityActionsBuilderArray[0]->getEntityActionsService()->getEntity();
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
            return $this->redirectToCurrentRoute($request);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($entityActionsBuilderArray as $entityActionsBuilder) {
                /** @var EntityActionsInterface $entityActionsService */
                $entityActionsService = $entityActionsBuilder->getEntityActionsService();
                $entityActionsService->after(
                    $this->getAfterEntityActionsOptions($entityActionsBuilder->getAfterOptions(), $entityActionsService)
                );
            }
            foreach ($entityActionsBuilderArray as $entityActionsBuilder) {
                $this->setFormLog($type, $entityActionsBuilder->getEntityActionsService()->getEntity());
            }
            if (!$this->flush()) {
                return $formRender;
            }
            $this->setDefaultRedirectRouteParameters($defaultEntity);
            return $this->redirectSubmitted();
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->redirectInvalidForm($form, $request);
        }
        return $formRender;
    }

    /**
     * Redirect with flash messages when form is not valid
     * @param FormInterface $form
     * @param Request $request
     * @return RedirectResponse
     */
    protected function redirectInvalidForm(FormInterface $form, Request $request): RedirectResponse
    {
        foreach ($form->getErrors(true) as $value) {
            $this->addFlash('error', $value->getMessage());
        }
        return $this->redirectToCurrentRoute($request);
    }

    /**
     * Returns RedirectResponse of current page
     * @param Request $request
     * @return RedirectResponse
     */
    protected function redirectToCurrentRoute(Request $request): RedirectResponse
    {
        return $this->redirect($request->headers->get('referer'));
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
            return $this->redirectToCurrentRoute($request);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityActionsService->after(
                $this->getAfterEntityActionsOptions($entityActionsBuilder->getAfterOptions(), $entityActionsService)
            );
            $this->setFormLog($type, $defaultEntity);
            if (!$this->flush()) {
                return $this->redirectToCurrentRoute($request);
            }
            $this->setDefaultRedirectRouteParameters($defaultEntity);
            return $this->redirectSubmitted();
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->redirectInvalidForm($form, $request);
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
        $form->handleRequest($request);
        try {
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
            $filters[$filterLabel] = $filterEntity ?: '';
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
     * @param object $entity
     * @param string|null $message
     * @throws Exception
     */
    protected function setLogUpdate(object $entity, string $message = null): void
    {
        (new LogService($this->getDoctrine()->getManager()))
            ->setUser($this->getUser())
            ->setDescription(
                $this->translator->trans(
                    $message ?? 'log.update.entity',
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
     * @param int $entityId
     */
    protected function setLogDelete(string $entityName, int $entityId): void
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
                        '%id%' => $entityId,
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
        $this->getDoctrine()->getManager()->flush();
        try {
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
     * @throws Exception
     */
    protected function setDefaultRedirectRouteParameters($entity): void
    {
        if ($this->templateService->getRedirectRouteParameters() === null) {
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
        }

        return false;
    }

    /**
     * Returns entity by id
     * @param $entityClass
     * @param int $parameter
     * @return mixed
     * @throws Exception
     */
    protected function getEntityById($entityClass, int $parameter)
    {
        $entity = $this->getDoctrine()->getManager()->getRepository($entityClass)->find($parameter);
        if ($entity === null || !is_a($entity, $entityClass)) {
            return false;
        }
        return $entity;
    }

    /**
     * Returns MedicalHistory entity by GET parameter
     * @param Request $request
     * @return MedicalHistory|bool
     * @throws Exception
     * @todo сделать нормальные рауты в админке и убрать этот дебильный метод!!!
     */
    protected function getMedicalHistoryByParameter(Request $request)
    {
        if (!$medicalHistory =
            $this->getEntityById(
                MedicalHistory::class,
                $this->getGETParameter($request, MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY)
            )
        ) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.parameter_not_found')
            );
            return false;
        }
        return $medicalHistory;
    }

    /**
     * Returns Prescription entity by GET parameter
     * @param Request $request
     * @return Prescription|false
     * @throws Exception
     * @todo сделать нормальные рауты в админке и убрать этот дебильный метод!!!
     */
    protected function getPrescriptionByParameter(Request $request)
    {
        if (!$prescription = $this->getEntityById(
            Prescription::class,
            $this->getGETParameter($request, PrescriptionController::PRESCRIPTION_ID_PARAMETER_KEY)
        )) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.parameter_not_found')
            );
            return false;
        }
        return $prescription;
    }

    /**
     * Отображает действия с записью в таблице datatables
     *
     * @return Closure
     */
    protected function renderTableActions(): Closure
    {
        return function (int $entityId, $rowEntity, ?array $routeParameters = null) {
            return $this->getTableActionsResponseContent($entityId, $rowEntity, $routeParameters);
        };
    }

    /**
     * Gets the response content for table actions
     * @param int $entityId
     * @param object $rowEntity
     * @param array|null $routeParams
     * @return false|string
     */
    protected function getTableActionsResponseContent(
        int $entityId,
        object $rowEntity,
        ?array $routeParams = null
    )
    {
        $oldParams = ['id' => $entityId]; //todo убрать после очистки проекта от id в параметрах роута
        $newParams = [Helper::getShortLowerClassName($rowEntity) => $rowEntity->getId()];

        $showTemplateItemRoute = $this->templateService
            ->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->getTemplateItemRoute();
        $showTemplateItemRoute
            ->setRouteParams(
                $routeParams ??
                $this->chooseRouteParameters(
                    $showTemplateItemRoute->getRouteName(),
                    $newParams,
                    $oldParams
                )
            );

        $editTemplateItemRoute = $this->templateService
            ->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->getTemplateItemRoute();
        $editTemplateItemRoute
            ->setRouteParams(
                $routeParams ??
                $this->chooseRouteParameters(
                    $editTemplateItemRoute->getRouteName(),
                    $newParams,
                    $oldParams
                )
            );

        $deleteTemplateItemRoute = $this->templateService
            ->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->getTemplateItemRoute();
        $deleteTemplateItemRoute
            ->setRouteParams(
                $routeParams ??
                $this->chooseRouteParameters(
                    $deleteTemplateItemRoute->getRouteName(),
                    $newParams,
                    $oldParams
                )
            );

        return $this->render(
            $this->templateService->getCommonTemplatePath() . 'tableActions.html.twig',
            [
                'template' => $this->templateService,
                'deleteId' => $entityId,
            ]
        )->getContent();
    }

    /**
     * Этот костыль выбирает между новыми параметрами с entity в роуте и старыми с id в роуте
     * @param string $routeName
     * @param array $newParams
     * @param array $oldParams
     * @return array
     */
    private function chooseRouteParameters(string $routeName, array $newParams, array $oldParams): array
    {
        return $this->isRouteParamsExists($routeName, $newParams) ? $newParams : $oldParams;
    }

    /**
     * Check route for params existing
     * //todo перенести в сервис
     * @param string $routeName
     * @param array $params
     * @return bool
     */
    private function isRouteParamsExists(string $routeName, array $params): bool
    {
        try {
            $this->generateUrl(
                $routeName,
                $params
            );
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Get entity actions options after submitting and validation form
     * @param Closure|null $afterOptions
     * @param EntityActionsInterface $entityActionsService
     * @return array
     */
    private function getAfterEntityActionsOptions(
        ?Closure $afterOptions,
        EntityActionsInterface $entityActionsService
    ): array
    {
        return ($afterOptions !== null) ? $afterOptions($entityActionsService) : [];
    }

    /**
     * Returns current medical history or adds flash message if current medical history is not found
     * @param Patient $patient
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return MedicalHistory|false
     * @throws Exception
     */
    protected function getCurrentMedicalHistory(Patient $patient, MedicalHistoryRepository $medicalHistoryRepository)
    {
        if (!$medicalHistory = $medicalHistoryRepository->getCurrentMedicalHistory($patient)) {
            $this->addFlash(
                'error',
                $this->translator->trans('app_controller.error.current_medical_history_not_found')
            );
            return false;
        }
        return $medicalHistory;
    }

    /**
     * Метод для редактирования даных из ячейки таблицы. Документация:
     * 1. В редактируемом столбце должны лежать данные в формате
     * (id и class обязательно должны присутствовать + div - обязательный контейнер):
     * <div id="entity{{ id материала }}"
     *  data-url="{{Урл до контроллера}}"
     *  class="xEditable">{{Данные}}</div>
     *
     * 2. Готовим форму. В ней должно быть ТОЛЬКО ОДНО редактируемое поле и всё.
     * К редактируемому полю обязательно добавляем
     * 'attr' => [
     * 'class' => 'xEditableField'
     * ]
     *
     * 3. В контроллере прописываем следующие данные
     * Также мы передаём замыкание, в нём мы указываем какое поле у нас отрендерится в ячейке после редактирования
     * $this->templateService->edit();
     * return $this->submitFormForAjax(
     * $request,
     * $patientTestingResult,
     * ResultPatientTestingResultType::class,
     * function (FormInterface $form): string{
     * return $form->getData()->getResult();
     * }
     * );
     * @param Request $request
     * @param $entity
     * @param $formType
     * @param Closure $getRenderValue
     * @return JsonResponse|Response
     */
    protected function submitFormForAjax(Request $request, $entity, $formType, Closure $getRenderValue){
        $form = $this->createForm($formType, $entity,
            [
                'label' => false
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            $entityManager->persist($formData);
            $entityManager->flush();
            $renderValue = (string) $getRenderValue($form);
            return new JsonResponse([
                'code' => 200,
                'id' => $formData->getId(),
                'renderValue' => $renderValue,
                'message' => $this->translator->trans('app_controller.success.success_post')
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $errorList = [];
            foreach ($form->getErrors(true) as $value) {
                $errorList[] = $value->getMessage();
            }
            return new JsonResponse([
                'code' => 300,
                'error' => $errorList,
                'id' => $entity->getId(),
                'message' => $this->translator->trans('app_abstract_controller.error.exception')
            ]);
        }

        return $this->render('xEditableAjaxForm.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
