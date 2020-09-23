<?php


namespace App\Services\FilterService;

use Symfony\Component\Form\FormInterface;

/**
 * Class FilterData
 * создает объект с данными фильтра
 *
 * @package App\Services\FilterService
 */
class FilterData
{
    /** @var Filter $filter */
    private $filter;
    /** @var FormInterface|null */
    private $form;
    /** @var object|null */
    private $entity;

    /**
     * FilterData constructor.
     *
     * @param Filter $filter
     * @param FormInterface|null $form
     * @param object|null $entity
     */
    public function __construct(Filter $filter, FormInterface $form = null, object $entity = null)
    {
        $this->filter = $filter;
        $this->form = $form;
        $this->entity = $entity;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return FormInterface|null
     */
    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    /**
     * @return object|null
     */
    public function getEntity(): ?object
    {
        return $this->entity;
    }
}