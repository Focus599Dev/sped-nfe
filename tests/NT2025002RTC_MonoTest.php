<?php

namespace NFePHP\NFe\Tests;

use NFePHP\NFe\Make;
use NFePHP\NFe\Factories\Parser;
use PHPUnit\Framework\TestCase;

class NT2025002RTC_MonoTest extends TestCase
{
    /**
     * @var Make
     */
    protected $make;

    protected function setUp()
    {
        $this->make = new Make();
    }

    /**
     * Testa criação completa da Monofasia Ad Rem (IBS e CBS) com ordenação correta
     */
    public function testMonofasiaAdRem()
    {
        $stdIBSCBS = new \stdClass();
        $stdIBSCBS->item = 1;
        $stdIBSCBS->CST = '620';
        $stdIBSCBS->cClassTrib = '620001';
        $this->make->tagIBSCBS($stdIBSCBS);

        // IBS Ad Rem
        $stdIBSPadrao = new \stdClass();
        $stdIBSPadrao->item = 1;
        $stdIBSPadrao->qBCMono = '1000.0000';
        $stdIBSPadrao->adRemIBS = '0.1234';
        $stdIBSPadrao->vIBSMono = '123.40';
        $this->make->tagGIBSMonoAdRemPadrao($stdIBSPadrao);

        $stdIBSReten = new \stdClass();
        $stdIBSReten->item = 1;
        $stdIBSReten->qBCMonoReten = '500.0000';
        $stdIBSReten->adRemIBSReten = '0.0500';
        $stdIBSReten->vIBSMonoReten = '25.00';
        $this->make->tagGIBSMonoAdRemReten($stdIBSReten);

        $stdIBSRet = new \stdClass();
        $stdIBSRet->item = 1;
        $stdIBSRet->vIBSMonoRet = '10.00';
        $this->make->tagGIBSMonoAdRemRet($stdIBSRet);

        $stdIBSBio = new \stdClass();
        $stdIBSBio->item = 1;
        $stdIBSBio->qBCBioComb = '100.0000';
        $stdIBSBio->vIBSDiferenca = '15.00';
        $this->make->tagGIBSMonoAdRemBioDiferenca($stdIBSBio);

        // CBS Ad Rem
        $stdCBSPadrao = new \stdClass();
        $stdCBSPadrao->item = 1;
        $stdCBSPadrao->qBCMono = '1000.0000';
        $stdCBSPadrao->adRemCBS = '0.0456';
        $stdCBSPadrao->vCBSMono = '45.60';
        $this->make->tagGCBSMonoAdRemPadrao($stdCBSPadrao);

        $stdCBSReten = new \stdClass();
        $stdCBSReten->item = 1;
        $stdCBSReten->qBCMonoReten = '500.0000';
        $stdCBSReten->adRemCBSReten = '0.0200';
        $stdCBSReten->vCBSMonoReten = '10.00';
        $this->make->tagGCBSMonoAdRemReten($stdCBSReten);

        $stdCBSRet = new \stdClass();
        $stdCBSRet->item = 1;
        $stdCBSRet->vCBSMonoRet = '5.00';
        $this->make->tagGCBSMonoAdRemRet($stdCBSRet);

        $stdCBSBio = new \stdClass();
        $stdCBSBio->item = 1;
        $stdCBSBio->qBCBioComb = '100.0000';
        $stdCBSBio->vCBSDiferenca = '6.00';
        $this->make->tagGCBSMonoAdRemBioDiferenca($stdCBSBio);

        // Totais do Item
        $stdMono = new \stdClass();
        $stdMono->item = 1;
        $stdMono->vTotIBSMonoItem = '148.40';
        $stdMono->vTotCBSMonoItem = '55.60';
        $gIBSCBSMono = $this->make->tagGIBSCBSMono($stdMono);

        $this->assertInstanceOf(\DOMElement::class, $gIBSCBSMono);

        // Validação da ordem estrita dos nós filhos de gIBSCBSMono:
        // 1. gIBSMonoAdRem, 2. gCBSMonoAdRem, 3. vTotIBSMonoItem, 4. vTotCBSMonoItem
        $children = [];
        foreach ($gIBSCBSMono->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $children[] = $child->nodeName;
            }
        }

        $this->assertEquals([
            'gIBSMonoAdRem',
            'gCBSMonoAdRem',
            'vTotIBSMonoItem',
            'vTotCBSMonoItem'
        ], $children);

        // Validação interna do gIBSMonoAdRem
        $gIBSMonoAdRem = $gIBSCBSMono->getElementsByTagName('gIBSMonoAdRem')->item(0);
        $ibsChildren = [];
        foreach ($gIBSMonoAdRem->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $ibsChildren[] = $child->nodeName;
            }
        }
        $this->assertEquals(['gMonoPadrao', 'gMonoReten', 'gMonoRet', 'gpBioDiferenca'], $ibsChildren);

        // Validação de valores IBS
        $this->assertEquals('1000.0000', $gIBSMonoAdRem->getElementsByTagName('qBCMono')->item(0)->nodeValue);
        $this->assertEquals('0.1234', $gIBSMonoAdRem->getElementsByTagName('adRemIBS')->item(0)->nodeValue);
        $this->assertEquals('123.40', $gIBSMonoAdRem->getElementsByTagName('vIBSMono')->item(0)->nodeValue);
        $this->assertEquals('500.0000', $gIBSMonoAdRem->getElementsByTagName('qBCMonoReten')->item(0)->nodeValue);
        $this->assertEquals('0.0500', $gIBSMonoAdRem->getElementsByTagName('adRemIBSReten')->item(0)->nodeValue);
        $this->assertEquals('25.00', $gIBSMonoAdRem->getElementsByTagName('vIBSMonoReten')->item(0)->nodeValue);
        $this->assertEquals('10.00', $gIBSMonoAdRem->getElementsByTagName('vIBSMonoRet')->item(0)->nodeValue);
        $this->assertEquals('100.0000', $gIBSMonoAdRem->getElementsByTagName('qBCBioComb')->item(0)->nodeValue);
        $this->assertEquals('15.00', $gIBSMonoAdRem->getElementsByTagName('vIBSDiferenca')->item(0)->nodeValue);

        // Validação interna do gCBSMonoAdRem
        $gCBSMonoAdRem = $gIBSCBSMono->getElementsByTagName('gCBSMonoAdRem')->item(0);
        $cbsChildren = [];
        foreach ($gCBSMonoAdRem->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $cbsChildren[] = $child->nodeName;
            }
        }
        $this->assertEquals(['gMonoPadrao', 'gMonoReten', 'gMonoRet', 'gpBioDiferenca'], $cbsChildren);

        // Validação de totais
        $this->assertEquals('148.40', $gIBSCBSMono->getElementsByTagName('vTotIBSMonoItem')->item(0)->nodeValue);
        $this->assertEquals('55.60', $gIBSCBSMono->getElementsByTagName('vTotCBSMonoItem')->item(0)->nodeValue);
        // Validação contra o schema XSD real DFeTiposBasicos_v1.00.xsd
        $this->validarContraXSD($gIBSCBSMono);
    }

    /**
     * Testa criação completa da Monofasia Ad Valorem (IBS e CBS) com ordenação correta
     */
    public function testMonofasiaAdValorem()
    {
        $stdIBSCBS = new \stdClass();
        $stdIBSCBS->item = 1;
        $stdIBSCBS->CST = '620';
        $stdIBSCBS->cClassTrib = '620002';
        $this->make->tagIBSCBS($stdIBSCBS);

        // IBS Ad Valorem
        $stdIBSPadrao = new \stdClass();
        $stdIBSPadrao->item = 1;
        $stdIBSPadrao->vBCMono = '1000.00';
        $stdIBSPadrao->pAliqMonoUF = '1.5000';
        $stdIBSPadrao->vIBSMonoUF = '15.00';
        $stdIBSPadrao->pAliqMonoMun = '0.5000';
        $stdIBSPadrao->vIBSMonoMun = '5.00';
        $stdIBSPadrao->vIBSMono = '20.00';
        $this->make->tagGIBSMonoAdValoremPadrao($stdIBSPadrao);

        $stdIBSReten = new \stdClass();
        $stdIBSReten->item = 1;
        $stdIBSReten->vBCMonoReten = '500.00';
        $stdIBSReten->pAliqMonoReten = '2.0000';
        $stdIBSReten->vIBSMonoReten = '10.00';
        $this->make->tagGIBSMonoAdValoremReten($stdIBSReten);

        $stdIBSRet = new \stdClass();
        $stdIBSRet->item = 1;
        $stdIBSRet->vIBSMonoRet = '4.00';
        $this->make->tagGIBSMonoAdValoremRet($stdIBSRet);

        // CBS Ad Valorem
        $stdCBSPadrao = new \stdClass();
        $stdCBSPadrao->item = 1;
        $stdCBSPadrao->vBCMono = '1000.00';
        $stdCBSPadrao->pAliqMonoCBS = '1.0000';
        $stdCBSPadrao->vCBSMono = '10.00';
        $this->make->tagGCBSMonoAdValoremPadrao($stdCBSPadrao);

        $stdCBSReten = new \stdClass();
        $stdCBSReten->item = 1;
        $stdCBSReten->vBCMonoReten = '500.00';
        $stdCBSReten->pAliqMonoReten = '1.0000';
        $stdCBSReten->vCBSMonoReten = '5.00';
        $this->make->tagGCBSMonoAdValoremReten($stdCBSReten);

        $stdCBSRet = new \stdClass();
        $stdCBSRet->item = 1;
        $stdCBSRet->vCBSMonoRet = '2.00';
        $this->make->tagGCBSMonoAdValoremRet($stdCBSRet);

        // Totais
        $stdMono = new \stdClass();
        $stdMono->item = 1;
        $stdMono->vTotIBSMonoItem = '30.00';
        $stdMono->vTotCBSMonoItem = '15.00';
        $gIBSCBSMono = $this->make->tagGIBSCBSMono($stdMono);

        $this->assertInstanceOf(\DOMElement::class, $gIBSCBSMono);

        // Ordem
        $children = [];
        foreach ($gIBSCBSMono->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $children[] = $child->nodeName;
            }
        }
        $this->assertEquals([
            'gIBSMonoAdValorem',
            'gCBSMonoAdValorem',
            'vTotIBSMonoItem',
            'vTotCBSMonoItem'
        ], $children);

        $gIBSMonoAdValorem = $gIBSCBSMono->getElementsByTagName('gIBSMonoAdValorem')->item(0);
        $this->assertEquals('1000.00', $gIBSMonoAdValorem->getElementsByTagName('vBCMono')->item(0)->nodeValue);
        $this->assertEquals('1.5000', $gIBSMonoAdValorem->getElementsByTagName('pAliqMonoUF')->item(0)->nodeValue);
        $this->assertEquals('15.00', $gIBSMonoAdValorem->getElementsByTagName('vIBSMonoUF')->item(0)->nodeValue);
        $this->assertEquals('0.5000', $gIBSMonoAdValorem->getElementsByTagName('pAliqMonoMun')->item(0)->nodeValue);
        $this->assertEquals('5.00', $gIBSMonoAdValorem->getElementsByTagName('vIBSMonoMun')->item(0)->nodeValue);
        $this->assertEquals('20.00', $gIBSMonoAdValorem->getElementsByTagName('vIBSMono')->item(0)->nodeValue);

        // Validação contra o schema XSD real DFeTiposBasicos_v1.00.xsd
        $this->validarContraXSD($gIBSCBSMono);
    }

    /**
     * Testa parsing das linhas TXT para a nova estrutura de monofasia
     */
    public function testParserLinhasTXTMonofasia()
    {
        $parser = new Parser();
        $reflection = new \ReflectionClass($parser);
        $method = $reflection->getMethod('fieldsToStd');
        $method->setAccessible(true);

        $structure = json_decode(file_get_contents(__DIR__ . '/../storage/txtstructure400.json'), true);

        // UC02A1 - IBS Ad Rem Padrao
        $linha = "UC02A1|1000.0000|0.1234|123.40|";
        $stdA1 = $method->invoke(null, explode('|', $linha), $structure['UC02A1']);
        $this->assertEquals('1000.0000', $stdA1->qBCMono);
        $this->assertEquals('0.1234', $stdA1->adRemIBS);
        $this->assertEquals('123.40', $stdA1->vIBSMono);

        // UC02C1 - CBS Ad Rem Padrao
        $linha = "UC02C1|1000.0000|0.0456|45.60|";
        $stdC1 = $method->invoke(null, explode('|', $linha), $structure['UC02C1']);
        $this->assertEquals('1000.0000', $stdC1->qBCMono);
        $this->assertEquals('0.0456', $stdC1->adRemCBS);
        $this->assertEquals('45.60', $stdC1->vCBSMono);

        // UC02 - Totais
        $linha = "UC02|123.40|45.60|";
        $stdTot = $method->invoke(null, explode('|', $linha), $structure['UC02']);
        $this->assertEquals('123.40', $stdTot->vTotIBSMonoItem);
        $this->assertEquals('45.60', $stdTot->vTotCBSMonoItem);
    }

    /**
     * Helper para validar nó contra o schema DFeTiposBasicos_v1.00.xsd
     * @param \DOMElement $gIBSCBSMono
     */
    private function validarContraXSD($gIBSCBSMono)
    {
        $xmlFragment = $this->make->dom->saveXML($gIBSCBSMono);
        $xsdFile = realpath(__DIR__ . '/../schemes/PL_010f_v1.04/DFeTiposBasicos_v1.00.xsd');

        $schemaWrapper = '<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"
           xmlns="http://www.portalfiscal.inf.br/nfe"
           targetNamespace="http://www.portalfiscal.inf.br/nfe"
           elementFormDefault="qualified">
    <xs:include schemaLocation="' . str_replace('\\', '/', $xsdFile) . '"/>
    <xs:element name="gIBSCBSMono" type="TMonofasia"/>
</xs:schema>';

        $innerXml = preg_replace('/^<gIBSCBSMono[^>]*>/', '', $xmlFragment);
        $innerXml = preg_replace('/<\/gIBSCBSMono>$/', '', $innerXml);

        $wrappedXml = '<?xml version="1.0" encoding="UTF-8"?>
<gIBSCBSMono xmlns="http://www.portalfiscal.inf.br/nfe">' . $innerXml . '</gIBSCBSMono>';

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($wrappedXml);

        $valid = $dom->schemaValidateSource($schemaWrapper);
        $this->assertTrue($valid, "O elemento gIBSCBSMono gerado deve ser 100% válido contra DFeTiposBasicos_v1.00.xsd");
    }
}
