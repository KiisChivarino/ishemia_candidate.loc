<?php

namespace App\Repository;

use App\Entity\BlogItem;
use App\Entity\BlogRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogRecord[]    findAll()
 * @method BlogRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRecordRepository extends ServiceEntityRepository
{
    /**
     * BlogRecordRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogRecord::class);
    }

    /**
     * @param BlogRecord $blogRecord
     */
    public function moveOldOutstandingItems(BlogRecord $blogRecord)
    {
        $qb = $this->_em->getRepository(BlogItem::class)->createQueryBuilder('bi');
        $items = $qb
            ->where('bi.completed = :completed')
            ->setParameter('completed', false)
            ->getQuery()
            ->getResult();
        /** @var BlogItem $item */
        foreach ($items as $item) {
            $item->setBlogRecord($blogRecord);
        }
    }

    /**
     * Returns blog
     *
     * @return array[]
     */
    public function getBlog()
    {
        $blog = [
            'records' => [],
            'outstanding' => [],
        ];
        $records = $this->_em->getRepository(BlogRecord::class)->createQueryBuilder('br')->orderBy('br.dateBegin', 'DESC')->getQuery()->getResult();
        foreach ($records as $record) {
            $commits = $this->_em->getRepository(BlogItem::class)->createQueryBuilder('bi')
                ->where('bi.completed = :completed AND bi.blogRecord = :blogRecord')
                ->setParameter('completed', true)
                ->setParameter('blogRecord', $record)
                ->getQuery()
                ->getResult();
            if ($commits) {
                $blog['records'][] = [
                    'record' => $record,
                    'commits' => $commits
                ];
            }
        }
        $blog['outstanding'] = $this->_em->getRepository(BlogItem::class)->createQueryBuilder('bi')
            ->where('bi.completed = :completed')
            ->setParameter('completed', false)
            ->getQuery()
            ->getResult();
        return $blog;
    }
}
