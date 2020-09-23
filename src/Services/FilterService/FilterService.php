<?php


namespace App\Services\FilterService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class FilterService
 * Manage filters
 *
 * @package App\Services\FilterService
 */
class FilterService
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var SessionInterface $session */
    private $session;

    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var RequestStack $requestStack */
    private $requestStack;

    /**
     * FilterService constructor.
     *
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param FormFactoryInterface $formFactory
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session, FormFactoryInterface $formFactory, RequestStack $requestStack)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    /**
     * Put filter in session
     *
     * @param Filter $filter
     *
     * @return bool
     */
    public function putFilterInSession(Filter $filter)
    {
        if (!$filter->getValue()) {
            return false;
        }
        $this->session->set($filter->getName(), $filter->getValue());
        return true;
    }

    /**
     * Remove filter from session
     *
     * @param Filter $filter
     */
    public function clearSession(Filter $filter)
    {
        $this->session->remove($filter->getName());
    }

    /**
     * Get entity object by filter value
     *
     * @param Filter $filter
     *
     * @return bool|object|null
     */
    public function getEntityByFilterValue(Filter $filter)
    {
        if (!$filter->getValue()) {
            return null;
        }
        return $this->em->getRepository($filter->getEntityClass())->find($filter->getValue());
    }

    /**
     * Set filter value from session
     *
     * @param Filter $filter
     *
     * @return bool
     */
    public function setFilerValueFromSession(Filter $filter)
    {
        if (!$this->session->has($filter->getName())) {
            return false;
        }
        $filter->setValue($this->session->get($filter->getName()));
        return true;
    }

    /**
     * Создает объект Filter
     *
     * @param string $name
     * @param string $entityClass
     * @param string|null $value
     *
     * @return Filter
     */
    public function createFilter(string $name, string $entityClass, string $value = null)
    {
        return new Filter($name, $entityClass, $value);
    }

    /**
     * Возвращает entity из сессии или из GET массива
     *
     * @param Filter $filter
     * @param null $entityId
     *
     * @return bool|object|null
     */
    public function getFilterEntity(Filter $filter, $entityId = null)
    {
        if ($entityId !== null) {
            /** entity from GET */
            return $this->em->getRepository($filter->getEntityClass())->find($entityId);
        } else {
            $this->setFilerValueFromSession($filter);
            /** entity from SESSION */
            return $this->getEntityByFilterValue($filter);
        }
    }

    /**
     * @param string $entityClassName
     * @param array $formBuilderData
     * @param string|null $filterName
     *
     * @return FilterData
     */
    public function generateFilter(string $entityClassName, array $formBuilderData, string $filterName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        $filterName = $filterName ? $filterName : $this->generateFilterName($request->get('_route'), $entityClassName);
        //создаю фильтр
        $filter = $this->createFilter($filterName, $entityClassName);
        //id сущности из get или из сессии
        if ($request->query->get($filterName)) {
            $filter->setValue($request->query->get($filterName));
            $this->putFilterInSession($filter);
        } else {
            $this->setFilerValueFromSession($filter);
        }
        $entity = $this->getEntityByFilterValue($filter);
        $entityName = mb_strtolower(substr($entityClassName, strripos($entityClassName, '\\') + 1));
        $formBuilderData['data'] = $entity ? $entity : '';
        $formBuilderData['attr']['data-filter_name'] = $filterName;
        $form = $this->createFormBuilder($filterName)->add($entityName, EntityType::class, $formBuilderData)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $entity = $formData[$entityName];
            if ($entity) {
                //id фильтра из формы
                $filter->setValue($entity->getId());
                $this->putFilterInSession($filter);
            } else {
                //сброс фильтра, если сущность не создана
                $this->clearSession($filter);
            }
        }
        return new FilterData($filter, $form ?? null, $entity ?? null);
    }

    /**
     * Обертка для создания FormBuilder
     *
     * @param string $name
     * @param null $data
     * @param array $options
     *
     * @return FormBuilderInterface
     */
    protected function createFormBuilder(string $name, $data = null, array $options = [])
    {
        return $this->formFactory->createNamedBuilder($name, FormType::class, $data, $options);
    }

    /**
     * Генерирует имя фильтра из роута и имени сущности, по которой будет фильтроваться список
     *
     * @param string $routeName
     * @param string $entityClassName
     *
     * @return string
     */
    public function generateFilterName(string $routeName, string $entityClassName)
    {
        return
            'filter_'
            .str_replace('_', '', $routeName).'_'
            .mb_strtolower(substr($entityClassName, strripos($entityClassName, '\\') + 1));
    }
}