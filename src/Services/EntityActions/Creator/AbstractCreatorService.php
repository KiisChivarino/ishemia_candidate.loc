<?php

namespace App\Services\EntityActions\Creator;

use App\Services\EntityActions\AbstractEntityActionsService;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractCreatorService
 * @package App\Services\EntityActions\Creator
 */
class AbstractCreatorService extends AbstractEntityActionsService
{

    /** @var TranslatorInterface */
    private $translator;

    /**
     * AbstractCreatorService constructor.
     * @param string $entityClass
     * @param TranslatorInterface $translator
     */
    public function __construct(string $entityClass, TranslatorInterface $translator)
    {
        $this->entityClass = $entityClass;
        $this->translator = $translator;
    }

    /**
     * Actions with entity before submitting and validating form
     * @param array $options
     * @param null $entity
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): void
    {
        parent::before($options, $entity);
        $this->create();
    }

    /**
     * Create new entity
     * @throws Exception
     */
    protected function create(): void
    {
        if (class_exists($this->entityClass)) {
            $this->setEntity(new $this->entityClass);
        } else {
            throw new Exception(
                $this->translator->trans(
                    'command.success',
                    ['%entityClass%' => $this->entityClass]
                )
            );
        }
        if (method_exists($this->entity, 'setEnabled')) {
            $this->entity->setEnabled(true);
        }
    }
}