<?php

namespace App\Validator\PatientTestingResult;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PatientTestingResult extends Constraint
{
    public $message = 'Значение результата анализа "{{ string }}" должно быть больше или равно "0"';
}