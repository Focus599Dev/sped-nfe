<?php

namespace NFePHP\NFe\Tests;

use NFePHP\NFe\Make;
use NFePHP\NFe\Factories\Parser;
use PHPUnit\Framework\TestCase;

class NT2026008Test extends TestCase
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
     * Testa inserção de vUnComLiq e vProdLiq via tagprod
     */
    public function testTagProdComValoresLiquidos()
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->cProd = 'PROD001';
        $std->cEAN = 'SEM GTIN';
        $std->xProd = 'PRODUTO TESTE NT 2026.008';
        $std->NCM = '12345678';
        $std->CFOP = '5102';
        $std->uCom = 'UN';
        $std->qCom = 2;
        $std->vUnCom = 100.00;
        $std->vProd = 200.00;
        $std->vUnComLiq = 80.00;
        $std->vProdLiq = 160.00;
        $std->cEANTrib = 'SEM GTIN';
        $std->uTrib = 'UN';
        $std->qTrib = 2;
        $std->vUnTrib = 100.00;
        $std->indTot = 1;

        $prod = $this->make->tagprod($std);

        $this->assertInstanceOf(\DOMElement::class, $prod);
        $this->assertEquals('80.00', $prod->getElementsByTagName('vUnComLiq')->item(0)->nodeValue);
        $this->assertEquals('160.00', $prod->getElementsByTagName('vProdLiq')->item(0)->nodeValue);
    }

    /**
     * Testa inserção via tagProdLiq complementar
     */
    public function testTagProdLiqComplementar()
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->cProd = 'PROD002';
        $std->cEAN = 'SEM GTIN';
        $std->xProd = 'PRODUTO TESTE COMPLEMENTAR';
        $std->NCM = '12345678';
        $std->CFOP = '5102';
        $std->uCom = 'UN';
        $std->qCom = 1;
        $std->vUnCom = 50.00;
        $std->vProd = 50.00;
        $std->cEANTrib = 'SEM GTIN';
        $std->uTrib = 'UN';
        $std->qTrib = 1;
        $std->vUnTrib = 50.00;
        $std->indTot = 1;

        $this->make->tagprod($std);

        $stdLiq = new \stdClass();
        $stdLiq->item = 1;
        $stdLiq->vUnComLiq = '42.50';
        $stdLiq->vProdLiq = '42.50';

        $this->make->tagProdLiq($stdLiq);

        $reflection = new \ReflectionClass($this->make);
        $prop = $reflection->getProperty('aProd');
        $prop->setAccessible(true);
        $aProd = $prop->getValue($this->make);

        $prod = $aProd[1];
        $this->assertEquals('42.50', $prod->getElementsByTagName('vUnComLiq')->item(0)->nodeValue);
        $this->assertEquals('42.50', $prod->getElementsByTagName('vProdLiq')->item(0)->nodeValue);
    }

    /**
     * Testa grupo ICMSPrevistoPagtoAntecip
     */
    public function testTagICMSPrevistoPagtoAntecip()
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->vICMSPrevisto = 15.50;

        $icms = $this->make->tagICMSPrevistoPagtoAntecip($std);

        $this->assertInstanceOf(\DOMElement::class, $icms);
        $group = $icms->getElementsByTagName('ICMSPrevistoPagtoAntecip')->item(0);
        $this->assertNotNull($group);
        $this->assertEquals('15.50', $group->getElementsByTagName('vICMSPrevisto')->item(0)->nodeValue);
    }

    /**
     * Testa inserção de vProdLiqTot no totalizador
     */
    public function testTagTotComValorLiquido()
    {
        $stdTot = new \stdClass();
        $stdTot->vBC = 100.00;
        $stdTot->vICMS = 18.00;
        $stdTot->vICMSDeson = 0.00;
        $stdTot->vBCST = 0.00;
        $stdTot->vST = 0.00;
        $stdTot->vProd = 100.00;
        $stdTot->vProdLiqTot = 80.00;
        $stdTot->vFrete = 0.00;
        $stdTot->vSeg = 0.00;
        $stdTot->vDesc = 0.00;
        $stdTot->vII = 0.00;
        $stdTot->vIPI = 0.00;
        $stdTot->vIPIDevol = 0.00;
        $stdTot->vPIS = 1.65;
        $stdTot->vCOFINS = 7.60;
        $stdTot->vOutro = 0.00;
        $stdTot->vNF = 100.00;

        $icmsTot = $this->make->tagICMSTot($stdTot);

        $reflection = new \ReflectionClass($this->make);
        $prop = $reflection->getProperty('total');
        $prop->setAccessible(true);
        $total = $prop->getValue($this->make);

        $this->assertNotNull($total);
        $this->assertEquals('80.00', $total->getElementsByTagName('vProdLiqTot')->item(0)->nodeValue);

        // Verifica se vProdLiqTot antecede ICMSTot na sequência de filhos
        $first = $total->firstChild;
        $this->assertEquals('vProdLiqTot', $first->nodeName);
    }

    /**
     * Testa parsing da linha I com campos de valor líquido no final
     */
    public function testParserComCamposNoFinalDaLinhaI()
    {
        $parser = new Parser();
        $reflection = new \ReflectionClass($parser);
        $method = $reflection->getMethod('fieldsToStd');
        $method->setAccessible(true);

        $structure = json_decode(file_get_contents(__DIR__ . '/../storage/txtstructure400.json'), true);
        $structI = $structure['I'];

        // Linha I com 28 campos normais + 2 novos campos no final
        $linha = "I|PROD01|SEM GTIN|PRODUTO TESTE|12345678|||5102|UN|1.00|100.00|100.00|SEM GTIN|UN|1.00|100.00|||||1|||||||85.00|85.00|";
        $fields = explode('|', $linha);

        $std = $method->invoke(null, $fields, $structI);

        $this->assertEquals('PROD01', $std->cProd);
        $this->assertEquals('100.00', $std->vProd);
        $this->assertEquals('85.00', $std->vUnComLiq);
        $this->assertEquals('85.00', $std->vProdLiq);
    }

    /**
     * Testa parsing da linha I11a isolada
     */
    public function testParserComLinhaI11aIsolada()
    {
        $parser = new Parser();
        $reflection = new \ReflectionClass($parser);
        $method = $reflection->getMethod('fieldsToStd');
        $method->setAccessible(true);

        $structure = json_decode(file_get_contents(__DIR__ . '/../storage/txtstructure400.json'), true);
        $structI11a = $structure['I11A'];

        $linha = "I11a|75.00|150.00|";
        $fields = explode('|', $linha);

        $std = $method->invoke(null, $fields, $structI11a);

        $this->assertEquals('75.00', $std->vUnComLiq);
        $this->assertEquals('150.00', $std->vProdLiq);
    }
}
