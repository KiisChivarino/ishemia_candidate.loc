<?php

namespace App\Repository;

use App\Entity\TemplateType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateTypeRepository
 * @method TemplateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateType[]    findAll()
 * @method TemplateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class TemplateTypeRepository extends AppRepository
{
    //Ids of template type
    /** @var int Id of Anamnesis of life template type */
    private const TEMPLATE_TYPE_ID_ANAMNESIS_LIFE = 1;
    /** @var int Id of Objective status template type */
    private const TEMPLATE_TYPE_ID_OBJECTIVE_STATUS = 3;

    /**
     * TemplateTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateType::class);
    }

    /**
     * Gets Template Type With Objective Status Id.
     * @return TemplateType|null
     */
    public function getTemplateTypeWithObjectiveStatusId(): ?TemplateType
    {
        return $this->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_OBJECTIVE_STATUS
            ]
        );
    }

    /**
     * Gets Template Type With Amnamnesis Of Life Id.
     * @return TemplateType|null
     */
    public function getTemplateTypeWithAmnamnesisOfLifeId(): ?TemplateType
    {
        return $this->findOneBy(
            [
                'id' => self::TEMPLATE_TYPE_ID_ANAMNESIS_LIFE
            ]
        );
    }
}
