<?php

namespace App\Services\Template;

use App\Services\FilterService\FilterData;
use App\Services\FilterService\FilterService;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TemplateItem
 * template item is class of properties and methods of common template file so as list, new, show, actions and other
 *
 * @package App\Services\Template
 */
abstract class TemplateItem
{
    /** @var string Path to yaml file with common content */
    private const YAML_CONTENT_PATH = '../config/services/template/content.yaml';

    /** @var bool $isEnabled enables or disables template item */
    protected $isEnabled;

    /** @var array|mixed $content content of template item */
    protected $content;

    /** @var string $name name of template item */
    protected $name;

    /** @var string $path path to template item (default is path to common templates directory) */
    protected $path;

    /** @var FilterData[] entity, form and filter objects for every filter */
    private $filterData;

    /**
     * TemplateItem constructor.
     */
    public function __construct()
    {
        $this->content = $this->getContentFromYaml();
        $this->setIsEnabled(true);
        $this->path = TemplateService::DEFAULT_COMMON_TEMPLATE_PATH;
    }

    /**
     * Get enabled status of template item
     *
     * @return bool
     */
    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Set enabled status of template item
     *
     * @param bool $isEnabled
     *
     * @return $this
     */
    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Добавляет переменную в контент или переопределяет значение по умолчанию
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setContent(string $key, string $value): self
    {
        $this->content[$key] = $value;
        return $this;
    }

    /**
     * Get content
     *
     * @return array|string[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Adds content array  to common content
     *
     * @param array $contents
     *
     * @return TemplateItem
     */
    public function addContentArray(?array $contents = []): self
    {
        if(is_array($contents)){
            $this->content = array_merge($this->content, $contents);
        }
        return $this;
    }

    /**
     * Returns value of content by key
     *
     * @param string $contentKey
     *
     * @return mixed|string|null
     */
    public function getContentValue(string $contentKey)
    {
        return array_key_exists($contentKey, $this->getContent()) ? $this->getContent()[$contentKey] : null;
    }

    /**
     * Returns name of template item
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Adds name of template item
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Добавить фильтры
     *
     * @param string $filterName
     * @param FilterData $filterData
     *
     * @return $this
     */
    public function addFilterData(string $filterName, FilterData $filterData): self
    {
        $this->filterData[$filterName] = $filterData;
        return $this;
    }

    /**
     * Получить фильтры
     *
     * @return FilterData[]
     */
    public function getFilterData()
    {
        return $this->filterData;
    }

    /**
     * Возвращает данные фильтра
     *
     * @param string $filterDataName
     *
     * @return FilterData|null
     */
    public function getFilterDataByName(string $filterDataName): ?FilterData
    {
        $filterData = $this->getFilterData();
        if (isset($filterData[$filterDataName])) {
            return $filterData[$filterDataName];
        }
        return null;
    }

    /**
     * Получить формы фильтров для вывода в шаблоне
     *
     * @return array
     */
    public function getFiltersViews()
    {
        $views = [];
        $filterData = $this->getFilterData() ?? [];
        foreach ($filterData as $filterDataOne) {
            $views[] = $filterDataOne->getForm()->createView();
        }
        return $views;
    }

    /**
     * Returns path to template item
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Adds current path to template item
     *
     * @param string $path
     *
     * @return TemplateItem
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns array of common content from yaml
     *
     * @return array
     */
    private function getContentFromYaml(): array
    {
        if (file_exists(self::YAML_CONTENT_PATH)) {
            $yaml = Yaml::parseFile(self::YAML_CONTENT_PATH);
            if (isset($yaml['parameters'])) {
                return $yaml['parameters'];
            }
        }
        return [];
    }

    /**
     * Устанавливает фильтры для шаблона
     *
     * @param FilterService $filterService
     * @param TemplateFilter[] $filters
     *
     * @return TemplateItem
     */
    public function setFilters(FilterService $filterService, array $filters): self
    {
        /**
         * @var string $filterEntity
         * @var TemplateFilter $filterFormData
         */
        foreach ($filters as $templateFilter) {
            $this->addFilterData(
                $templateFilter->getTemplateFilterName(),
                $filterService->generateFilter(
                    $templateFilter->getEntityClass(),
                    $templateFilter->getFormData(),
                    $templateFilter->getFilterName() ? $templateFilter->getFilterName() : null
                )
            );
        }
        $this->setIsEnabled(true);

        return $this;
    }

    /**
     * Устанавливает контент для шаблона
     *
     * @param array $contents
     */
    public function setContents(?array $contents = []): void
    {
        foreach ($contents as $key => $value) {
            $this->setContent($key, $value);
        }
    }
}