<?php


namespace App\AppBundle\DataSowing;


use App\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use RuntimeException;

/**
 * Class DataSowing
 * Библиотека для добавления справочников из файлов
 *
 * @package App\AppBundle\DataSowing
 */
class DataSowing
{
    const PATH_TO_CSV = 'data/AppFixtures/';

    /**
     * Заполняет справочники из csv файла
     * file - путь к csv файлу
     * entityClass - имя класса заполняемых сущностей
     * delimiter - разделитель значений в csv файле
     * replaceFieldNameArr: ключ - имя поля в csv файле, значение - на какое имя свойства заменить; если значение null - не вносить данные этого поля
     * $persistArr: дополнительные свойства, которых нет в csv
     * $foreignKeyArr: массив [поле=>entity класс...] для получения объектов по внешнему ключу
     */
    public function setEntitiesFromCsv(
        ObjectManager $manager,
        string $file,
        string $entityClass,
        string $delimiter = ';',
        array $replaceFieldNameArr = [],
        array $persistArr = [],
        array $foreignkeyArr = []
    ) {
        if (!(is_readable($file))) {
            throw new RuntimeException(sprintf('Не удалось прочитать файл '.$file.'!'));
        };
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
                        $entityData[lcfirst($headerKeyName)] = trim($data[$headerValueId]) !== '' ? trim($data[$headerValueId]) : null;
                    }
                }
                //добавляем дополнительные свойства, которых нет в csv
                foreach ($persistArr as $key => $value) {
                    $entityData[lcfirst($key)] = $value;
                }

                //меняем внешние ключи на объекты
                foreach ($foreignkeyArr as $property => $class) {
                    $entityData[lcfirst($property)] = $manager->getRepository($class)->find($entityData[lcfirst($property)]);
                }

                //выполнение сеттеров по подготовленным свойствам
                $manager
                    ->getRepository($entityClass)
                    ->setEntityData(
                        $entityData,
                        (new $entityClass()),
                        $persistArr
                    );
            }
            fclose($handle);
            $manager->flush();
        }
    }

    /**
     * Добавляет роли из yaml файла
     */
    public function addRoles(ObjectManager $manager)
    {
        $const = Yaml::parseFile('config/services/roles.yaml');
        foreach ($const['parameters'] as $roleData) {
                unset($roleData['route']);
                $manager
                    ->getRepository(Role::class)
                    ->setEntityData(
                        $roleData,
                        (new Role())
                    );
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
     */
    public function addEntitiesFromCatalog(ObjectManager $manager, array $catalog, string $entityClass, array $params)
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
            $manager
                ->getRepository($entityClass)
                ->setEntityData(
                    $data,
                    (new $entityClass())
                );
        }
    }
}