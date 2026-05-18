<?php

namespace NotaFiscalSP\Constants\Requests;

class RpsEnum
{
    const RPS = 'RPS';
    const RPS_TYPE = 'TipoRPS';
    const EMISSION_DATE = 'DataEmissao';
    const RPS_STATUS = 'StatusRPS';
    const RPS_TAX = 'TributacaoRPS';
    const SERVICE_VALUE = 'ValorServicos';
    const DEDUCTION_VALUE = 'ValorDeducoes';
    const PIS_VALUE = 'ValorPIS';
    const COFINS_VALUE = 'ValorCOFINS';
    const INSS_VALUE = 'ValorINSS';
    const IR_VALUE = 'ValorIR';
    const CSLL_VALUE = 'ValorCSLL';
    const SERVICE_CODE = 'CodigoServico';
    const SERVICE_TAX = 'AliquotaServicos';
    const ISS_RETENTION = 'ISSRetido';
    const IM_TAKER = 'InscricaoMunicipalTomador';
    const IE_TAKER = 'InscricaoEstadualTomador';
    const CORPORATE_NAME_TAKER = 'RazaoSocialTomador';
    const EMAIL_TAKER = 'EmailTomador';
    const DISCRIMINATION = 'Discriminacao';
    const CPFCNPJ_TAKER = 'CPFCNPJTomador';
    const CPFCNPJ_INTERMEDIARY = 'CPFCNPJIntermediario';
    const IM_INTERMEDIARY = 'InscricaoMunicipalIntermediario';
    const ISS_RETENTION_INTERMEDIARY = 'ISSRetidoIntermediario';
    const EMAIL_INTERMEDIARY = 'EmailIntermediario';
    const TAX_VALUE_INTERMEDIARY = 'ValorCargaTributaria';
    const TAX_PERCENT_INTERMEDIARY = 'PercentualCargaTributaria';
    const TAX_ORIGIN = 'FonteCargaTributaria';
    const CEI_CODE = 'CodigoCEI';
    const WORK_REGISTRATION = 'MatriculaObra';
    const CITY_INSTALLMENT = 'MunicipioPrestacao';
    const TOTAL_VALUE = 'ValorTotalRecebido';
    const ENCAPSULATION_NUMBER = 'NumeroEncapsulamento';
    const RETENCAO_PIS_COFINS = "RetencaoPisCofins";
    
    // --- Layout 2 (Reforma Tributária 2026) - Campos adicionais do tpRPS
    const INITIAL_CHARGED_VALUE = 'ValorInicialCobrado';
    const FINAL_CHARGED_VALUE = 'ValorFinalCobrado';
    const FINE_VALUE = 'ValorMulta';
    const INTEREST_VALUE = 'ValorJuros';
    const IPI_VALUE = 'ValorIPI';
    const DEDUCTION_CIBS_VALUE = 'ValorDeducaoCIBS';
    const EXIGIBILITY_SUSPENDED = 'ExigibilidadeSuspensa';
    const ONEROSITY = 'Onerosidade';
    const ADVANCE_INSTALLMENT_PAYMENT = 'PagamentoParceladoAntecipado';
    const NCM = 'NCM';
    const NBS = 'NBS';
    const ACTIVITY_EVENT = 'atvEvento';
    const IBS_CBS = 'IBSCBS';

    // Grupo gpPrestacao (Layout 2)
    const PRESTATION_LOCATION_CODE = 'cLocPrestacao';
    const PRESTATION_COUNTRY_CODE = 'cPaisPrestacao';

    // Grupo tpTrib / tpGIBSCBS (Layout 2)
    const TRIBUTES_GROUP = 'trib';
    const G_IBS_CBS = 'gIBSCBS';

    public static function simpleTypes()
    {
        return [
            RpsEnum::RPS_TYPE,
            RpsEnum::EMISSION_DATE,
            RpsEnum::RPS_STATUS,
            RpsEnum::RPS_TAX,
            RpsEnum::SERVICE_VALUE,
            RpsEnum::DEDUCTION_VALUE,
            RpsEnum::PIS_VALUE,
            RpsEnum::COFINS_VALUE,
            RpsEnum::INSS_VALUE,
            RpsEnum::IR_VALUE,
            RpsEnum::CSLL_VALUE,
            RpsEnum::SERVICE_CODE,
            RpsEnum::SERVICE_TAX,
            RpsEnum::ISS_RETENTION,
        ];
    }

    
    public static function layout2SimpleTypes()
    {
        return [
            RpsEnum::INITIAL_CHARGED_VALUE,
            RpsEnum::FINAL_CHARGED_VALUE,
            RpsEnum::FINE_VALUE,
            RpsEnum::INTEREST_VALUE,
            RpsEnum::IPI_VALUE,
            RpsEnum::DEDUCTION_CIBS_VALUE,
            RpsEnum::EXIGIBILITY_SUSPENDED,
            RpsEnum::ONEROSITY,
            RpsEnum::ADVANCE_INSTALLMENT_PAYMENT,
            RpsEnum::NCM,
            RpsEnum::NBS,
        ];
    }

    public static function complexTypes()
    {
        return [
            RpsEnum::ACTIVITY_EVENT,
            RpsEnum::TRIBUTES_GROUP,
            RpsEnum::IBS_CBS,
        ];
    }

    public static function takerInformations()
    {
        return [
            RpsEnum::IM_TAKER,
            RpsEnum::IE_TAKER,
            RpsEnum::CORPORATE_NAME_TAKER,
        ];
    }
}