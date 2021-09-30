<?php

namespace App\Validator\AnalysisRate;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AnalysisRateMinMaxResult extends Constraint
{
    public $message = 'Минимальное значение значение не может быть больше максимального';
}