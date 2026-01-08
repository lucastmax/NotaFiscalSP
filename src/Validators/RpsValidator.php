<?php

namespace NotaFiscalSP\Validators;

use NotaFiscalSP\Constants\FieldData\BooleanFields;
use NotaFiscalSP\Constants\FieldData\RPSType;
use NotaFiscalSP\Constants\FieldData\Status;
use NotaFiscalSP\Constants\Requests\ComplexFieldsEnum;
use NotaFiscalSP\Constants\Requests\DetailEnum;
use NotaFiscalSP\Constants\Requests\RpsEnum;
use NotaFiscalSP\Constants\Requests\SimpleFieldsEnum;
use NotaFiscalSP\Entities\BaseInformation;
use NotaFiscalSP\Entities\Requests\NF\Rps;
use NotaFiscalSP\Exceptions\InvalidParam;
use NotaFiscalSP\Helpers\Certificate;

class RpsValidator
{
    public static function  validateRpsType($value){
        if(!in_array($value, [RPSType::RECIBO_PROVISORIO,RPSType::RECIBO_PROVENIENTE_DE_NOTA_CONJUGADA, RPSType::CUPOM])){
            $expected = RPSType::RECIBO_PROVISORIO. ', '. RPSType::RECIBO_PROVENIENTE_DE_NOTA_CONJUGADA. ' ou '.RPSType::CUPOM;
            throw new InvalidParam('TipoRps', $expected);
        }
    }

    public static function  validateRpsStatus($value){
        if(!in_array($value, [Status::NORMAL, Status::CANCELLED, Status::MISPLACED])){
            $expected = Status::NORMAL. ', '.  Status::CANCELLED. ' ou '.Status::MISPLACED;
            throw new InvalidParam('StatusRps', $expected);
        }
    }

    public static function validateRps(BaseInformation $baseInformation, $rps2)
    {
        $rpsOK = [];

        $rps = !array_key_exists(0, $rps2) ? [$rps2] : $rps2 ;

        foreach ($rps as $item) {
            if ($item instanceof Rps) {
                $item = $item->toArray();
            }
            if (empty($item[SimpleFieldsEnum::IM_PROVIDER]))
                $item[SimpleFieldsEnum::IM_PROVIDER] = $baseInformation->getIm();

            if(isset($item[RpsEnum::ISS_RETENTION])){
                $item[RpsEnum::ISS_RETENTION] = $item[RpsEnum::ISS_RETENTION] ? BooleanFields::LOWER_TRUE : BooleanFields::LOWER_FALSE;
            }else{
                $item[RpsEnum::ISS_RETENTION] = BooleanFields::LOWER_FALSE;
            }

            $item[ComplexFieldsEnum::RPS_KEY] = true;

            // Layout 2 (Reforma Tributária): ajustar campos e assinatura
            if (method_exists($baseInformation, 'getLayoutVersion') && $baseInformation->getLayoutVersion() === 2) {
                // No layout 2 não existe ValorServicos; usar ValorInicialCobrado/ValorFinalCobrado
                if (!empty($item[RpsEnum::SERVICE_VALUE])) {
                    // mantém compatibilidade: se vier ValorServicos e não vierem os novos, usa como ValorFinalCobrado
                    if (empty($item[RpsEnum::INITIAL_CHARGED_VALUE]) && empty($item[RpsEnum::FINAL_CHARGED_VALUE])) {
                        $item[RpsEnum::FINAL_CHARGED_VALUE] = $item[RpsEnum::SERVICE_VALUE];
                    }
                    unset($item[RpsEnum::SERVICE_VALUE]);
                }
                if (empty($item[RpsEnum::INITIAL_CHARGED_VALUE]) && empty($item[RpsEnum::FINAL_CHARGED_VALUE])) {
                    throw new InvalidParam('Para layout 2 informe ValorInicialCobrado ou ValorFinalCobrado');
                }
            }

            if (method_exists($baseInformation, 'getLayoutVersion') && $baseInformation->getLayoutVersion() === 2 && method_exists(Certificate::class, 'rpsSignatureStringV2')) {
                $item[DetailEnum::SIGN] = Certificate::rpsSignatureStringV2($item);
            } else {
                $item[DetailEnum::SIGN] = Certificate::rpsSignatureString($item);
            }
            $rpsOK[] = $item;
        }
        return $rpsOK;
    }
}