<?php

namespace App\Repository;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AppRepository
 *
 * @package App\Repository
 */
class AppRepository extends ServiceEntityRepository
{
    /**
     * Добавляет данные в БД
     *
     * @param array $params
     * @param $entity
     * @param array $persistArr
     *
     * @return mixed
     * @throws ORMException
     */
    public function setEntityData(array $params, $entity, array $persistArr = [])
    {
        foreach ($params as $key => $param) {
            if (array_key_exists($key, $persistArr)) {
                if (gettype($persistArr[$key]) === 'string') {
                    $persistArr[$key] = trim($persistArr[$key]) ? trim($persistArr[$key]) : null;
                }
                $method = 'set'.ucfirst($key);
                $entity->{$method}($persistArr[$key]);
                continue;
            }
            if (gettype($param) === 'string') {
                $param = trim($param) !== '' ? trim($param) : null;
            }
            $method = 'set'.ucfirst($key);
            $entity->{$method}($param);
        }
        $this->getEntityManager()->persist($entity);
        return $entity;
    }

    /**
     * Возвращает последний id для сущности
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function getNextEntityId(): int
    {
        $lastEntity = $this->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $lastEntity ? $lastEntity->getId() + 1 : 1;
    }

    /**
     * Set property values for file entity
     *
     * @param object $fileEntity
     * @param UploadedFile $uploadedFile
     */
    public function setFileProperties(object $fileEntity, UploadedFile $uploadedFile): void
    {
        $fileEntity->setFile($uploadedFile);
        $fileEntity->setFileName($uploadedFile->getClientOriginalName());
        $fileEntity->setExtension(preg_replace('/.+\//', '', $uploadedFile->getMimeType()));
        $fileEntity->setUploadedDate(new DateTime());
    }
}
