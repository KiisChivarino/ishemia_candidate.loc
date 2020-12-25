<?php

namespace App\Repository;

use App\Entity\NotificationTemplate;
use App\Entity\NotificationTemplateText;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationTemplateText|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTemplateText|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTemplateText[]    findAll()
 * @method NotificationTemplateText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTemplateTextRepository extends AppRepository
{
    /**
     * NotificationTemplateTextRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplateText::class);
    }

    /**
     * Ищет текст шаблона уведомления для определенного типа канала и шаблона уведомления
     * @param string $channel
     * @param NotificationTemplate $notificationTemplate
     * @return NotificationTemplateText|null
     * @throws NonUniqueResultException
     */
    public function findForChannel(
        string $channel, NotificationTemplate $notificationTemplate
    ): ?NotificationTemplateText {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.channelType', 'cT')
            ->andWhere('cT.name = :channel')
            ->setParameter('channel', $channel)
            ->andWhere('n.notificationTemplate = :notificationTemplate')
            ->setParameter('notificationTemplate', $notificationTemplate)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
