<?php

namespace App\Services\TextTemplateService;

use App\Entity\Template;
use App\Entity\TemplateManyToManyTemplateParameterText;
use App\Entity\TemplateParameterText;
use App\Repository\TemplateManyToManyTemplateParameterTextRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class TextTemplateService
{
    /** @var EntityManagerInterface $entityManager */
    private $manyToManyTemplateParameterTextRepository;

    private $entityManager;

    public function __construct(TemplateManyToManyTemplateParameterTextRepository $manyToManyTemplateParameterTextRepository, EntityManagerInterface $entityManager)
    {
        $this->manyToManyTemplateParameterTextRepository = $manyToManyTemplateParameterTextRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Collect text form parameter text array
     * @param TemplateParameterText[] $parameterTextArray
     * @return string
     */
    public function getTextByParameterTextArray(array $parameterTextArray): string
    {
        $res = '';
        foreach ($parameterTextArray as $templateParameterText) {
            $res .=
                '<p>'
                . '<strong>'
                . $templateParameterText->getTemplateParameter()->getName()
                . ($templateParameterText->getText() ? ': ' : '')
                . '</strong>'
                . $templateParameterText->getText()
                . '.</p>';
        }
        return $res;
    }

    /**
     * Get array of template parameter texts from form
     * @param FormInterface $parameterTextsForm
     * @return array
     */
    public function getParameterTextArrayFromForm(FormInterface $parameterTextsForm): array
    {
        $parameterTextArray = [];
        foreach ($parameterTextsForm as $formChild) {
            /** @var TemplateParameterText $templateParameterText */
            $templateParameterText = $formChild->getData();
            if (is_a($templateParameterText, TemplateParameterText::class)) {
                $parameterTextArray[] = $formChild->getData();
            }
        }
        //todo если форма имеет дочерние формы, а массив $parameterTextArray пустой - Exception
        return $parameterTextArray;
    }

    /**
     * Get array of template parameter texts from template entity object
     * @param Template $templateEntity
     * @return array
     */
    public function getParameterTextArrayFromTemplate(Template $templateEntity): array
    {
        $parameterTextArray = [];
        /** @var TemplateManyToManyTemplateParameterText $manyToManyTemplateParameterText */
        foreach ($templateEntity->getTemplateManyToManyTemplateParameterTexts() as $manyToManyTemplateParameterText){
            $parameterTextArray[] = $manyToManyTemplateParameterText->getTemplateParameterText();
        }
        return $parameterTextArray;
    }

    /**
     * @param array $formData
     * @param Template $template
     */
    public function persistTemplateParameterTexts(array $formData, Template $template){
        /** @var Form $formChild */
        foreach ($formData as $formChild) {
            if (is_a($templateParameterText = $formChild->getData(), TemplateParameterText::class)) {
                $templateManyToManyParameterText = new TemplateManyToManyTemplateParameterText();
                $templateManyToManyParameterText->setTemplate($template);
                $templateManyToManyParameterText->setTemplateParameterText($templateParameterText);
                $this->entityManager->persist($templateManyToManyParameterText);
            }
        }
    }

    /**
     * @param Template $template
     */
    public function clearTemplateParameterTexts(Template $template){
        $templateTexts = $this->manyToManyTemplateParameterTextRepository->findBy(['template' => $template]);
        foreach ($templateTexts as $templateText) {
            $this->entityManager->remove($templateText);
        }
    }
}