<?php

namespace App\Services\Patient;

use App\Entity\Patient;
use App\Form\Admin\Patient\PatientRiskFactorType;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PatientRiskFactorService
 *
 * @package App\Services\Patient
 */
class PatientRiskFactorService
{
    /**
     * Устанавливает факторы риска из групп факторов риска в request
     *
     * @param Request $request
     */
    public function setRiskFactors(Request $request): void
    {
        $form = $request->request->get('form');
        $formPatientData = $request->request->get('form')['patient']['riskFactor'] ?? null;
        $riskFactorArr = [];
        $i = 1;
        while (isset($formPatientData[PatientRiskFactorType::RISK_FACTOR_GROUP_TITLE.$i])) {
            $riskFactor = $formPatientData[PatientRiskFactorType::RISK_FACTOR_GROUP_TITLE.$i];
            $riskFactorArr = array_merge($riskFactorArr, $riskFactor);
            $i++;
        }
        $form['patient']['riskFactor'] = $riskFactorArr;
//        VarDumper::dump($request->request->get('form')['patient']['riskFactor']);
//        exit;
        $request->request->set('form', $form);
    }

    /**
     * Устанавливает флаг в checked, если установлен фактор риска
     *
     * @param FormView $formView
     * @param Patient $patient
     */
    public function setRiskFactorsChecked(FormView $formView, Patient $patient): void
    {
        foreach ($patient->getRiskFactor() as $factor) {
            /**
             * @var string $key
             * @var FormView $value
             */
            foreach ($formView->offsetGet('riskFactor')->children as $key => $value) {
                if (strpos($key, PatientRiskFactorType::RISK_FACTOR_GROUP_TITLE) !== false) {
                    foreach ($value->children as $riskFactorFormViewKey => $riskFactorFormViewValue) {
                        if ($factor->getId() == $riskFactorFormViewKey) {
                            $riskFactorFormViewValue->vars['checked'] = true;
                        }
                    }
                }
            }
        }
    }
}
