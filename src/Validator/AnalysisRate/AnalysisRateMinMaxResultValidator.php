<?php

namespace App\Validator\AnalysisRate;

use App\Entity\AnalysisRate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AnalysisRateMinMaxResultValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var AnalysisRate $analysisRate */
        $analysisRate = $this->context->getObject();

        if($analysisRate->getRateMin() >= $analysisRate->getRateMax()){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }


}