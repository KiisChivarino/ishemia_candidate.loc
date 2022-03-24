<?php

namespace App\AppBundle\DataSowing;

use App\Entity\AuthUser;
use App\Entity\ChannelType;
use App\Entity\NotificationReceiverType;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DataSowing
 * Библиотека для добавления справочников из файлов
 *
 * @package App\AppBundle\DataSowing
 */
class DataSowing
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var array Константы типов каналов уведомлений */
    private $channelTypes;

    /** @var array Константы типов получателей уведомлений */
    private $notificationReceiverTypeNames;

    /** @var array Константы названий получателей уведомлений */
    private $notificationReceiverTypeTitles;

    /**
     * DataSowing constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param array                  $channelTypes
     * @param array                  $notificationReceiverTypeNames
     * @param array                  $notificationReceiverTypeTitles
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        array $channelTypes,
        array $notificationReceiverTypeNames,
        array $notificationReceiverTypeTitles
    ) {
        $this->entityManager = $entityManager;
        $this->channelTypes = $channelTypes;
        $this->notificationReceiverTypeNames = $notificationReceiverTypeNames;
        $this->notificationReceiverTypeTitles = $notificationReceiverTypeTitles;
    }

    /**
     * Заполняет справочники из csv файла
     * file - путь к csv файлу
     * entityClass - имя класса заполняемых сущностей
     * delimiter - разделитель значений в csv файле
     * replaceFieldNameArr: ключ - имя поля в csv файле, значение - на какое имя свойства заменить; если значение null - не вносить данные этого поля
     * $persistArr: дополнительные свойства, которых нет в csv
     * $foreignKeyArr: массив [поле=>entity класс...] для получения объектов по внешнему ключу
     *
     * @param ObjectManager $manager
     * @param string        $file
     * @param string        $entityClass
     * @param string        $delimiter
     * @param array         $replaceFieldNameArr
     * @param array         $persistArr
     * @param array         $foreignkeyArr
     */
    public function setEntitiesFromCsv(
        ObjectManager $manager,
        string $file,
        string $entityClass,
        string $delimiter = ';',
        array $replaceFieldNameArr = [],
        array $persistArr = [],
        array $foreignkeyArr = []
    ): void {
        if (!(is_readable($file))) {
            throw new RuntimeException(sprintf('Не удалось прочитать файл '.$file.'!'));
        }
        if (($handle = fopen($file, "r")) !== false) {
            $headers = array_flip(fgetcsv($handle, null, $delimiter)); //заголовки csv файла
            while (($data = fgetcsv($handle, null, $delimiter)) !== false) {
                $entityData = [];
                foreach ($headers as $headerKeyName => $headerValueId) {
                    /** проверка поля на наличие в массиве замены имени/игнорирования поля */
                    if (array_key_exists($headerKeyName, $replaceFieldNameArr)) {
                        if ($replaceFieldNameArr[$headerKeyName] == null) {
                            //если в массиве замены для данного csv заголовка значение null, игнорируем все значения столбца
                            continue;
                        } else {
                            //используем свойство с именем из массива замены
                            $entityData[lcfirst($replaceFieldNameArr[$headerKeyName])] = $data[$headerValueId];
                        }
                    } else {
                        //добавляем свойство с именем из заголовка csv файла
                        $entityData[lcfirst($headerKeyName)] =
                            trim($data[$headerValueId]) !== '' ? trim($data[$headerValueId]) : null;
                    }
                }
                //добавляем дополнительные свойства, которых нет в csv
                foreach ($persistArr as $key => $value) {
                    $entityData[lcfirst($key)] = $value;
                }

                //меняем внешние ключи на объекты
                foreach ($foreignkeyArr as $property => $class) {
                    $entityData[lcfirst($property)] =
                        $manager
                            ->getRepository($class)
                            ->find($entityData[lcfirst($property)]);
                }

                //выполнение сеттеров по подготовленным свойствам
                $this->entityManager->getRepository($entityClass)->setEntityData(
                    $entityData,
                    (new $entityClass()),
                    $persistArr
                );
            }
            fclose($handle);
        }
    }

    /**
     * Добавляет роли из yaml файла
     * Adds roles from yaml file
     */
    public function addRoles(): void
    {
        $const = Yaml::parseFile('config/services/roles.yaml');
        foreach ($const['parameters']['roles'] as $roleData) {
            unset($roleData['route']);
            $this->entityManager->getRepository(AuthUser::class)->setEntityData(
                $roleData,
                new Role()
            );
        }
    }

    /**
     * Добавляет типы каналов из yaml файла
     * Adds channel types from yaml file
     */
    public function addChannelTypes(): void
    {
        $i = 1;
        foreach ($this->channelTypes as $channelType) {
            $this->addEntityFormYaml(
                [
                    'id'   => $i,
                    'name' => $channelType,
                ],
                new ChannelType()
            );
            $i++;
        }
    }

    /**
     * Добавляет новую сущность
     * Adds new entity
     *
     * @param array  $insertData
     * @param object $entity
     */
    public function addEntityFormYaml(array $insertData, object $entity): void
    {
        $this->entityManager->getRepository(AuthUser::class)->setEntityData(
            $insertData,
            $entity
        );
    }

    /**
     * Добавляет типы получателей из yaml файла
     * Adds notifications receiver types from yaml file
     */
    public function addReceiverTypes(): void
    {
        $i = 1;
        $namesAndTitles = array_merge($this->notificationReceiverTypeNames, $this->notificationReceiverTypeTitles);
        foreach ($namesAndTitles as $name => $title) {
            $this->addEntityFormYaml(
                [
                    'id'    => $i,
                    'name'  => $name,
                    'title' => $title
                ],
                new NotificationReceiverType()
            );
            $i++;
        }
    }

    /**
     * Добавляет сущности из выборки справочника, им соответствующего
     * Если значение параметра для сущности является свойтвом справочника, вызывает геттер справочника,
     * если является именем класса справочника, то добавляет текущий объект справочника, соответствующий сущности,
     * в других случаях просто добавляет значение параметра
     * $catalog - объекты справочника
     * $entityClass - класс заполняемой сущности
     * $params - массив параметров: ключ - свойство заполняемой сущности, значение - свойство справочника, имя класса справочника или любое другое значение
     *
     * @param array  $catalog
     * @param string $entityClass
     * @param array  $params
     */
    public function addEntitiesFromCatalog(array $catalog, string $entityClass, array $params): void
    {
        foreach ($catalog as $catalogItem) {
            $data = [];
            //подготовка массива данных
            foreach ($params as $key => $value) {
                if (is_string($value)) {
                    $method = 'get'.ucfirst($value);
                    if (method_exists($catalogItem, $method)) {
                        //выполнение геттера
                        $data[$key] = $catalogItem->{$method}();
                    } elseif (get_class($catalogItem) == $value) {
                        //добавление сущности справочника
                        $data[$key] = $catalogItem;
                    } else {
                        $data[$key] = $value;
                    }
                } else {
                    $data[$key] = $value;
                }
            }
            //внесение подготовленных данных в бд
            $this->entityManager->getRepository($entityClass)->setEntityData(
                $data,
                (new $entityClass())
            );
        }
    }
}