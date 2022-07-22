<?php
namespace App\Services\StatusService;

/**
 * Class DataTableStatusRender
 * @package App\Services\StatusService
 */
class StatusRender
{
    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $text;

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return StatusRender
     */
    public function setText(string $text): StatusRender
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return StatusRender
     */
    public function setColor(string $color): StatusRender
    {
        $this->color = $color;
        return $this;
    }
}
