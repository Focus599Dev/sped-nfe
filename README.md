# focus599dev/sped-nfe

Pacote PHP para geração, assinatura, transmissão e consulta de **Nota Fiscal Eletrônica (NF-e)** , mantido pela FocusIT.

Baseado na especificação oficial do projeto SPED da SEFAZ, este pacote oferece suporte completo ao layout **versão 4.00**, integrando-se com todas as SEFAZ autorizadoras do Brasil.

---

## 📋 Índice

- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Estados atendidos](#estados-atendidos)
- [Funcionalidades](#funcionalidades)
- [Uso básico](#uso-básico)
- [Documentação](#documentação)
- [Suporte](#suporte)
- [Licença](#licença)

---

## Requisitos

- PHP **7.4** ou superior
- Extensões PHP: `curl`, `dom`, `json`, `gd`, `mbstring`, `openssl`, `soap`, `xml`, `zip`
- [Composer](https://getcomposer.org/)

---

## Instalação

Instale via Composer:

```bash
composer require focus599dev/sped-nfe
```

Ou adicione ao seu `composer.json`:

```json
"require": {
    "focus599dev/sped-nfe": "^5.3.0"
}
```

---

## Estados atendidos

### NF-e (modelo 55)
✅ **Todos os estados**

---

## Funcionalidades

- ✅ Geração do XML da NF-e (layout 4.00)
- ✅ Assinatura digital do XML com certificado A1/A3
- ✅ Transmissão às SEFAZ autorizadoras (produção e homologação)
- ✅ Consulta de status de serviço
- ✅ Consulta de cadastro de contribuinte
- ✅ Cancelamento de NF-e
- ✅ Inutilização de numeração
- ✅ Carta de Correção Eletrônica (CC-e)
- ✅ Manifesto do Destinatário
- ✅ Download/distribuição de documentos fiscais (DFe)
- ✅ Eventos reforma tributaria

---

## Conformidade

Este pacote é aderente com os padrões [PSR-1], [PSR-2] e [PSR-4].

[PSR-1]: https://www.php-fig.org/psr/psr-1/
[PSR-2]: https://www.php-fig.org/psr/psr-2/
[PSR-4]: https://www.php-fig.org/psr/psr-4/

> Desenvolvido com ❤️ pela equipe **FocusIT** — Soluções em Tecnologia Fiscal.
