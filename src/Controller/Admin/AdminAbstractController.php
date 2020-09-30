<?php

namespace App\Controller\Admin;

use App\Controller\AppAbstractController;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Closure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AppAbstractController
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
abstract class AdminAbstractController extends AppAbstractController
{
    /** @var string "new" type of form */
    protected const RESPONSE_FORM_TYPE_NEW = 'new';
    /** @var string "edit" type of form */
    protected const RESPONSE_FORM_TYPE_EDIT = 'edit';

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
     * @return RedirectResponse|Response
     */
    public function responseNew(
        Request $request,
        object $entity,
        string $typeClass,
        ?FilterLabels $filterLabels = null,
        array $customFormOptions = [],
        ?Closure $entityActions = null
    ) {
        if (method_exists($entity, 'setEnabled')) {
            $entity->setEnabled(true);
        }
        $template = $this->templateService->new($filterLabels ? $filterLabels->getFilterService() : null);
        $options = array_merge($customFormOptions, $filterLabels ? $this->getFiltersByFilterLabels($template, $filterLabels->getFilterLabelsArray()) : []);
        $options[self::FORM_TEMPLATE_ITEM_OPTION_TITLE] = $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME);
        return $this->responseFormTemplate(
            $request,
            $entity,
            $this->createForm($typeClass, $entity, $options),
            self::RESPONSE_FORM_TYPE_NEW,
            $entityActions
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
     * @return RedirectResponse|Response
     */
    public function responseEdit(Request $request, object $entity, string $typeClass, array $customFormOptions = [], ?Closure $entityActions = null)
    {
        return $this->responseFormTemplate(
            $request,
            $entity,
            $this->createForm(
                $typeClass, $entity,
                array_merge($customFormOptions, [self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $this->templateService->edit()->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),])
            ),
            self::RESPONSE_FORM_TYPE_EDIT,
            $entityActions
        );
    }

    /**
     * Delete entity and redirect
     *
     * @param Request $request
     * @param object $entity
     *
     * @return RedirectResponse|Response
     */
    public function responseDelete(Request $request, object $entity)
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($entity);
                $entityManager->flush();
            } catch (DBALException $e) {
                if ($e->getPrevious()->getCode() == 23503) {
                    $this->addFlash(
                        'error',
                        'Запись не удалена! Удалите все дочерние элементы!'
                    );
                    return $this->redirectToRoute($this->templateService->getRoute('list'));
                } else {
                    $this->addFlash('error', 'Ошибка! Запись не удалена. Обратитесь к администратору...');
                    return $this->redirectToRoute($this->templateService->getRoute('list'));
                }
            }
        }
        $this->addFlash('success', 'Запись успешно удалена');
        return $this->redirectToRoute($this->templateService->getRoute('list'));
    }

    /**
     * Set next id for entity
     *
     * @return Closure
     */
    public function setNextEntityIdFunction(): Closure
    {
        return function (EntityActions $actions) {
            $actions->getEntity()->setId($actions->getEntityManager()->getRepository(get_class($actions->getEntity()))->getNextEntityId());
        };
    }
}