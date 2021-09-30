<?php

namespace App\Validator\PatientTestingResult;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PatientTestingResultValidator extends ConstraintValidator
{

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \App\Entity\PatientTestingResult $PatientTestingResult */
        $PatientTestingResult = $this->context->getObject();

        if ($PatientTestingResult->getResult() < 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $PatientTestingResult->getAnalysis()->getName())
                ->addViolation();
        }
    }

}