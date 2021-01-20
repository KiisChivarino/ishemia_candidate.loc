<?php

namespace App\Services\EntityActions;

use App\Services\ControllerGetters\EntityActions;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * Class AbstractEntityActionsService
 * @package App\Services\EntityActions
 */
abstract class AbstractEntityActionsService implements EntityActionsInterface
{
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
     * @param string $class
     * @throws Exception
     */
    protected function setEntity($entity, string $class)
    {
        if (is_a($entity, $class)) {
            $this->entity = $entity;
        } else {
            throw new Exception('Invalid class of object');
        }
    }

    /**
     * Persists entity
     * @param ObjectManager $entityManager
     */
    protected function persist(ObjectManager $entityManager)
    {
        $entityManager->persist($this->entity);
    }

    /**
     * Actions with entity before persist
     * @param EntityActions $entityActions
     */
    protected function prepare(EntityActions $entityActions): void
    {

    }

    /**
     * Operations after submitting and validation form
     * @param EntityActions $entityActions
     * @param array|null $options
     * @throws Exception
     */
    public function after(EntityActions $entityActions, array $options = []): void
    {
        $this->configureOptions();
        $this->setOptions($options);
        $this->prepare($entityActions);
        $this->persist($entityActions->getEntityManager());
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
        $this->options = $options;
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
     * @param string $entityClass
     * @param array $options
     * @throws Exception
     */
    public function before(string $entityClass, array $options = []): void
    {
        $this->configureOptions();
        $this->setOptions($options);
    }

    /**
     * @return mixed
     */
    public function getEntity(){
        return $this->entity;
    }

    protected function configureOptions(){}
}