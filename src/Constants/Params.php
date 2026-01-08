<?php

namespace NotaFiscalSP\Constants;

class Params
{
    // CNPJ Necessário para identificação da Empresa
    const CNPJ = 'cnpj';

    // CPF - Identificação pessoa física
    const CPF = 'cpf';

    // Layout do XML (1 = atual, 2 = Reforma Tributária)
    const LAYOUT_VERSION = 'layoutVersion';

    // Inscrição Municipal do Prestador utilizada na RPS
    const IM = 'im';

    // Caminho para o Certificado Digital utilizado para assinar os Documentos
    const CERTIFICATE_PATH = 'certificate';

    // Senha do certificado
    const CERTIFICATE_PASS = 'certificatePass';
}