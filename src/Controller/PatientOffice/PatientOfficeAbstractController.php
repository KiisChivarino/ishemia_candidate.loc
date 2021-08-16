<?php

namespace App\Controller\PatientOffice;

use App\Controller\AppAbstractController;
use App\Services\TemplateItems\PatientOfficeItems\HistoryListTemplateItem;
use App\Services\TemplateItems\PatientOfficeItems\NewsListTemplateItem;
use Closure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PatientOfficeAbstractController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 */
abstract class PatientOfficeAbstractController extends AppAbstractController
{
    /**
     * Responses list of new items in patient office
     * @param string $templatePath
     * @param array $parameters
     * @param string|null $customTwigTemplate
     * @param Closure|null $actionsAfterBuildingTemplate - without parameters
     * @return Response
     */
    protected function responseNewsList(
        string   $templatePath,
        array    $parameters = [],
        ?string  $customTwigTemplate = null,
        ?Closure $actionsAfterBuildingTemplate = null
    ): Response
    {
        $this->templateService->newsList();
        return $this->responseListForPatient(
            $templatePath,
            $parameters,
            $customTwigTemplate ?? NewsListTemplateItem::TEMPLATE_ITEM_NEWS_LIST_NAME,
            $actionsAfterBuildingTemplate
        );
    }

    /**
     * Responses list of old items in patient office
     * @param string $templatePath
     * @param array $parameters
     * @param string|null $customTwigTemplate
     * @param Closure|null $showActions
     * @return Response
     */
    protected function responseHistoryList(
        string   $templatePath,
        array    $parameters = [],
        ?string  $customTwigTemplate = null,
        ?Closure $actionsAfterBuildingTemplate = null
    ): Response
    {
        $this->templateService->historyList();
        return $this->responseListForPatient(
            $templatePath,
            $parameters,
            $customTwigTemplate ?? HistoryListTemplateItem::TEMPLATE_ITEM_HISTORY_LIST_NAME,
            $actionsAfterBuildingTemplate
        );
    }

    /**
     * Render list of news or history for patient office
     * @param string $templatePath
     * @param array $parameters
     * @param string $twigTemplateName
     * @param Closure|null $actionsAfterBuildTemplate
     * @return Response
     */
    private function responseListForPatient(
        string   $templatePath,
        array    $parameters,
        string   $twigTemplateName,
        ?Closure $actionsAfterBuildTemplate
    ): Response
    {
        if ($actionsAfterBuildTemplate !== null) {
            $actionsAfterBuildTemplate();
        }
        return $this->render($templatePath . $twigTemplateName . '.html.twig', $parameters);
    }
}
