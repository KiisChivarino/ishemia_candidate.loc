<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\PatientOfficeItems\HistoryListTemplateItem;
use App\Services\TemplateItems\PatientOfficeItems\NewsListTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientOfficeTemplateBuilder
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
abstract class PatientOfficeTemplateBuilder extends AppTemplateBuilder
{
    /** @var string[] News content */
    public const NEWS_LIST_CONTENT = [
        'title' => 'Новые записи',
        'h1' => 'Новые записи',
    ];

    /** @var string[] History content */
    public const HISTORY_LIST_CONTENT = [
        'title' => 'История',
        'h1' => 'История',
    ];

    /** @var string Default redirect route name */
    public const DEFAULT_REDIRECT_ROUTE_NAME = 'patient_office_main';

    /** @var string Путь к общим шаблонам crud админки */
    public const DEFAULT_COMMON_TEMPLATE_PATH = 'patientOffice/common_template/';

    /** @var  string[] */
    protected $historyListContent;

    /** @var  string[] */
    protected $newsListContent;

    /**
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct(
            $routeCollection,
            $className,
            self::DEFAULT_COMMON_TEMPLATE_PATH,
            self::DEFAULT_REDIRECT_ROUTE_NAME
        );
        $this->addContent(
            self::NEWS_LIST_CONTENT,
            self::HISTORY_LIST_CONTENT,
            self::SHOW_CONTENT
        );
    }

    /**
     * Builds historyList template settings
     * @return $this
     */
    public function historyList(): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getHistoryListTemplateItems());
        $this->getItem(HistoryListTemplateItem::TEMPLATE_ITEM_HISTORY_LIST_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->historyListContent);
        return $this;
    }

    /**
     * Builds newsList template settings
     * @return $this
     */
    public function newsList(): self
    {
        $this->setTemplateItems($this->templateItemsFactory->getNewsListTemplateItems());
        $this->getItem(NewsListTemplateItem::TEMPLATE_ITEM_NEWS_LIST_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->newsListContent);
        return $this;
    }

    /**
     * @param array|null $commonContent
     * @param array|null $historyListContent
     * @param array|null $newsListContent
     * @param array|null $showContent
     * @param array|null $listContent
     */
    protected function addPatientOfficeContent(
        ?array $commonContent = [],
        ?array $historyListContent = [],
        ?array $newsListContent = [],
        ?array $showContent = [],
        ?array $listContent = []
    ){
        $this->commonContent = $commonContent;
        $this->listContent = $listContent;
        $this->showContent = $showContent;
        $this->historyListContent = $historyListContent;
        $this->newsListContent = $newsListContent;
    }
}
