<?php

namespace NotaFiscalSP\Helpers;

use Exception;
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Greenter\XMLSecLibs\Sunat\SignedXml;
use NotaFiscalSP\Constants\FieldData\BooleanFields;
use NotaFiscalSP\Constants\Requests\RpsEnum;
use NotaFiscalSP\Constants\Requests\SimpleFieldsEnum;
use NotaFiscalSP\Entities\BaseInformation;

/**
 * Class Certificate
 * @package NotaFiscalSP\Helpers
 */
class Certificate
{
    /**
     * @param $path
     * @param $pass
     * @return string
     * @throws Exception
     */
    public static function pfx2pem($path, $pass)
    {
        // Get File
        $pfx = file_get_contents($path);

        // Export PEM
        $certificate = new X509Certificate($pfx, $pass);
        $pem = $certificate->export(X509ContentType::PEM);

        return $pem;
    }

    /**
     * @param $pem
     * @param $xml
     * @return string
     */
    public static function signXmlWithCertificate($pem, $xml)
    {
        // Create Signed XML
        $signer = new SignedXml();

        // Sign XML
        $signer->setCertificate($pem);
        $xmlSigned = $signer->signXml($xml);

        // Return XML File Signed
        return $xmlSigned;
    }


    public static function signItem(BaseInformation $baseInformation, $content)
    {
        $signatureValue = '';
        $pkeyId = openssl_get_privatekey($baseInformation->getCertificate());
        openssl_sign($content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyId);
        return base64_encode($signatureValue);
    }

    public static function rpsSignatureString($params)
    {
        $document = General::getKey($params, SimpleFieldsEnum::CNPJ) ? General::getKey($params, SimpleFieldsEnum::CNPJ) : General::getKey($params, SimpleFieldsEnum::CPF);
        //Required Fields
        
        $tipoDoc = ((General::getKey($params, SimpleFieldsEnum::CPF)) ? '1' : '2');
        if(is_null($document)){
            $tipoDoc = 3;
        }
    }
    
    /**
     * Monta a string de assinatura do RPS conforme “Campos para assinatura do RPS – versão 2.0”.
     * Diferenças principais:
     * - IM do prestador com 12 posições (zero à esquerda).
     * - Não existe <ValorServicos>; usar <ValorInicialCobrado> ou <ValorFinalCobrado>.
     */
    public static function rpsSignatureStringV2($params)
    {
        $document = General::getKey($params, SimpleFieldsEnum::CNPJ) ? General::getKey($params, SimpleFieldsEnum::CNPJ) : General::getKey($params, SimpleFieldsEnum::CPF);

        $tipoDoc = ((General::getKey($params, SimpleFieldsEnum::CPF)) ? '1' : '2');
        if (is_null($document)) {
            $tipoDoc = 3;
        }

        $valorCobrado = null;
        if (!empty($params[RpsEnum::INITIAL_CHARGED_VALUE])) {
            $valorCobrado = General::getKey($params, RpsEnum::INITIAL_CHARGED_VALUE);
        } elseif (!empty($params[RpsEnum::FINAL_CHARGED_VALUE])) {
            $valorCobrado = General::getKey($params, RpsEnum::FINAL_CHARGED_VALUE);
        }

        $string =
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::IM_PROVIDER)) .
            sprintf('%-5s', General::getKey($params, SimpleFieldsEnum::RPS_SERIES)) .
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::RPS_NUMBER)) .
            str_replace('-', '', General::getKey($params, RpsEnum::EMISSION_DATE)) .
            General::getKey($params, RpsEnum::RPS_TAX) .
            General::getKey($params, RpsEnum::RPS_STATUS) .
            ($params[RpsEnum::ISS_RETENTION] == 'false' ? BooleanFields::FALSE : BooleanFields::TRUE) .
            sprintf('%015s', str_replace(array('.', ','), '', number_format($valorCobrado, 2))) .
            sprintf('%015s', str_replace(array('.', ','), '', number_format(General::getKey($params, RpsEnum::DEDUCTION_VALUE), 2))) .
            sprintf('%05s', General::getKey($params, RpsEnum::SERVICE_CODE)) .
            $tipoDoc .
            sprintf('%014s', $document);

        return $string;
    }


    public static function cancelSignatureString($params)
    {
        return sprintf('%08s', General::getKey($params, SimpleFieldsEnum::IM_PROVIDER)) .
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::NFE_NUMBER));
    }


    public static function nftsSignatureString($elements)
    {
        return '<tpNFTS>' . Certificate::makeXmlString($elements) . '</tpNFTS>';
    }

    public static function makeXmlString($array)
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (is_array($value)){
                $value = Certificate::makeXmlString($value);
            }
            $string .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        return $string;
    }

    public static function nftsCancellationSignatureString($elements)
    {
        return '<PedidoCancelamentoNFTSDetalheNFTS>' . Certificate::makeXmlString($elements) . '</PedidoCancelamentoNFTSDetalheNFTS>';
    }

}
