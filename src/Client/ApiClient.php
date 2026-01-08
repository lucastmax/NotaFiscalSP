<?php

namespace NotaFiscalSP\Client;

use Exception;
use NotaFiscalSP\Entities\BaseInformation;
use NotaFiscalSP\Entities\WsdlBase;
use NotaFiscalSP\Responses\BasicResponse;
use SoapClient;

class ApiClient
{
    public static function send(WsdlBase $wsdlBase, $method, BaseInformation $baseInformation)
    {
        $options = [
            'location' => $wsdlBase->getEndPoint(),
            'keep_alive' => true,
            'trace' => true,
            'local_cert' => $baseInformation->getCertificatePath(),
            'passphrase' => $baseInformation->getCertificatePass(),
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                    'allow_self_signed'=> true,
                ]
            ])
        ];
        
        $arguments = [
                $method => [
                    'VersaoSchema' => $baseInformation->getLayoutVersion(),
                    'MensagemXML' => $baseInformation->getXml()
                ],
        ];

        try {
            

            $client = new SoapClient($wsdlBase->getWsdl(), $options);

            $options = [];
            $result = $client->__soapCall($method, $arguments, $options);
            return $result->RetornoXML;
        } catch (Exception $e) {
            $response = new BasicResponse();
            $response->setSuccess(false);
            $response->setXmlInput($baseInformation->getXml());
            $response->setMessage($e);
            return $response;
        }
    }
}