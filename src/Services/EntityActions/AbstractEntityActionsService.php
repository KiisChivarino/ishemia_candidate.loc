<?php

namespace App\Services\EntityActions;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class AbstractEntityActionsService
 * @package App\Services\EntityActions
 */
abstract class AbstractEntityActionsService implements EntityActionsInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AuthUserRoleType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * entity object
     */
    protected $entity;

    /**
     * @var array custom variables for actions with entity like [variableName=>variableValue]
     */
    protected $options = [];

    /** @var array Array like [variableName=>variableType] of allowed options */
    protected $optionsCheckArray = [];

    /**
     * Sets entity to which actions are performed
     * @param $entity
     * @throws Exception
     */
    protected function setEntity($entity)
    {
        if (is_a($entity, $this->entityClass)) {
            $this->entity = $entity;
        } else {
            throw new Exception('Invalid class of object');
        }
    }

    /**
     * Persists entity
     */
    protected function persist()
    {
        $this->entityManager->persist($this->getEntity());
    }

    /**
     * Actions with entity before persist
     */
    protected function prepare(): void
    {
    }

    /**
     * @param array $options
     * @throws Exception
     */
    public function execute(array $options = []): void
    {
        $this->before($options);
        $this->after($options);
    }

    /**
     * Operations after submitting and validation form
     * @param array|null $options
     * @throws Exception
     */
    public function after(array $options = []): void
    {
        $this->setOptions($options);
        $this->prepare();
        $this->persist();
    }

    /**
     * Sets options into array of options
     * @param array|null $options
     * @throws Exception
     */
    protected function setOptions(?array $options = []): void
    {
        foreach ($options as $optionKey => $optionValue) {
            if ($this->checkOption($optionKey, $optionValue)) {
                $options[$optionKey] = $optionValue;
            } else {
                throw new Exception('Option with key ' . $optionKey . ' doesn\'t allowed!');
            }
        }
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Adds option into array of checking options
     * @param string $type
     * @param string $optionName
     */
    protected function addOptionCheck(string $type, string $optionName): void
    {
        $this->optionsCheckArray[$optionName] = $type;
    }

    /**
     * Check option for a name and type using array of checking options
     * @param string $optionKey
     * @param $optionValue
     * @return bool
     */
    protected function checkOption(string $optionKey, $optionValue): bool
    {
        return array_key_exists($optionKey, $this->optionsCheckArray)
            && (
                gettype($optionValue) === $this->optionsCheckArray[$optionKey]
                || is_a($optionValue, $this->optionsCheckArray[$optionKey])
            );
    }

    /**
     * @param array $options
     * @throws Exception
     */
    public function before(array $options = []): void
    {
        $this->configureOptions();
        $this->setOptions($options);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set entity class and set options: name of option and type of option
     */
    abstract protected function configureOptions(): void;
}