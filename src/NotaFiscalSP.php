<?php

namespace NotaFiscalSP;

use NotaFiscalSP\Builders\BaseEntitiesBuilder;
use NotaFiscalSP\Entities\Requests\NF\Period;
use NotaFiscalSP\Services\NfService;
use NotaFiscalSP\Services\NftsService;
use NotaFiscalSP\Validators\BaseInformationValidator;

/**
 * Class NotaFiscalSP
 * @package NotaFiscalSP
 */
class NotaFiscalSP
{
    private $baseInformation;
    private $nfService;
    private $nftsService;

    public function __construct(array $options)
    {
        // Validate Params
        BaseInformationValidator::basic($options);
        $this->baseInformation = BaseEntitiesBuilder::makeBaseInformation($options);

        $this->nfService = new NfService;
        $this->nftsService = new NftsService;

        // Case 'IM' not Defined, get from API
        if (!$this->baseInformation->getIm())
            $this->baseInformation->setIm($this->cnpjInfo());
    }

    /**
     *  NF METHODS
     */

    public function cnpjInfo($document = null)
    {
        return $this->nfService->checkCNPJ($this->baseInformation, $document);
    }

    public function consultarNf($params)
    {
        return $this->nfService->getNf($this->baseInformation, $params);
    }

    /**
     * Download an issued NFS-e XML to a local file.
     *
     * @param mixed $params NFS-e number or query parameters accepted by consultarNf().
     * @param string $filePath Destination path for the XML file.
     * @return string Path to the saved XML file.
     * @throws RuntimeException When the query fails or no NFS-e XML is returned.
     */
    public function baixarXmlNf($params, $filePath)
    {
        $response = $this->consultarNf($params);

        if (!$response || $response->getSuccess() !== 'true') {
            $message = $response && method_exists($response, 'getMessage')
                ? $response->getMessage()
                : 'Não foi possível consultar a NFS-e.';
            throw new \RuntimeException($message ?: 'Não foi possível consultar a NFS-e.');
        }

        $xmlOutput = $response->getXmlOutput();
        if (!is_string($xmlOutput) || trim($xmlOutput) === '') {
            throw new \RuntimeException('A Prefeitura não retornou conteúdo XML para a NFS-e.');
        }

        $document = new \DOMDocument();
        if (!$document->loadXML($xmlOutput, LIBXML_NONET)) {
            throw new \RuntimeException('A resposta da Prefeitura não contém XML válido.');
        }

        $xpath = new \DOMXPath($document);
        $invoice = $xpath->query('//*[local-name()="NFe"]')->item(0);
        if (!$invoice) {
            throw new \RuntimeException('A resposta não contém a NFS-e solicitada.');
        }

        $invoiceXml = $invoice->ownerDocument->saveXML($invoice);
        if ($invoiceXml === false || file_put_contents($filePath, $invoiceXml) === false) {
            throw new \RuntimeException('Não foi possível salvar o XML da NFS-e no caminho informado.');
        }

        return $filePath;
    }

    public function informacaoLote($params = [])
    {
        return $this->nfService->lotInformation($this->baseInformation, $params);
    }

    public function consultarLote($lotNumber)
    {
        return $this->nfService->getLot($this->baseInformation, $lotNumber);
    }

    public function notasEmitidas($params)
    {
        if ($params instanceof Period && !$params->getInscricaoMunicipal()) {
            $params->setInscricaoMunicipal($this->baseInformation->getIm());
        }

        return $this->nfService->getIssued($this->baseInformation, $params);
    }

    public function notasRecebidas($params)
    {
        return $this->nfService->getReceived($this->baseInformation, $params);
    }

    public function cancelarNota($params)
    {
        return $this->nfService->cancelNf($this->baseInformation, $params);
    }

    public function enviarNota($params)
    {           return $this->nfService->sendNf($this->baseInformation, $params);
    }

    public function enviarLote($params)
    {
        return $this->nfService->sendLot($this->baseInformation, $params);
    }

    public function testeEnviarLote($params)
    {
        return $this->nfService->testSendLot($this->baseInformation, $params);
    }

    /**
     *  ASYNC NF METHODS
     */

    public function testeEnviarLoteAsync($params)
    {
        return $this->nfService->testSendAsyncLot($this->baseInformation, $params);
    }

    public function enviarLoteAsync($params)
    {
        return $this->nfService->sendAsyncLot($this->baseInformation, $params);
    }

    public function emitirGuiaAsync($params)
    {
        return $this->nfService->makeReceiptAsync($this->baseInformation, $params);
    }

    public function consultarSituacaoGuia($params)
    {
        return $this->nfService->checkReceiptSituation($this->baseInformation, $params);
    }

    public function consultarGuia($params)
    {
        return $this->nfService->checkReceipt($this->baseInformation, $params);
    }

    public function consultarLoteAsync($params)
    {
        return $this->nfService->checkAsyncLot($this->baseInformation, $params);
    }

    /**
     *  NFTS
     */
    public function consultarNfts($params)
    {
        return $this->nftsService->getNfts($this->baseInformation, $params);
    }

    public function informacaLoteNfts($params = [])
    {
        return $this->nftsService->lotInformation($this->baseInformation, $params);
    }

    public function consultarLoteNfts($lotNumber)
    {
        return $this->nftsService->getLot($this->baseInformation, $lotNumber);
    }

    public function consultarAutorizacaoEmissao($params = null)
    {
        return $this->nftsService->checkEmission($this->baseInformation, $params);
    }

    public function testeLoteNfts($params)
    {
        return $this->nftsService->testLotNFTS($this->baseInformation, $params);
    }

    public function enviarLoteNfts($params)
    {
        return $this->nftsService->lotNfts($this->baseInformation, $params);
    }

    public function enviarNfts($params)
    {
        return $this->nftsService->sendNfts($this->baseInformation, $params);
    }

    public function cancelarNfts($params)
    {
        return $this->nftsService->cancelNfts($this->baseInformation, $params);
    }

}
