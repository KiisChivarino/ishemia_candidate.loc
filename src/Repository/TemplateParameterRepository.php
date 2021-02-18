<?php

namespace App\Repository;

use App\Entity\TemplateParameter;
use App\Entity\TemplateType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateParameterRepository
 * @method TemplateParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameter[]    findAll()
 * @method TemplateParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class TemplateParameterRepository extends AppRepository
{
    /**
     * TemplateParameterRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameter::class);
    }

    /**
     * Gets Template Type With Objective Status Id.
     * @param $templateType
     * @return TemplateParameter[]
     */
    public function getTemplateParameterByTemplateType(TemplateType $templateType): array
    {
        return $this->findBy(
            [
                'templateType' => $templateType
            ]
        );
    }
}
