# CRP - *C*ódigo de *R*oteamento *P*ostal
(ver demo em http://ppkrauss.github.io/CRP e dados em [neste link](http://data.okfn.org/tools/view?url=https%3A%2F%2Fraw.githubusercontent.com%2FppKrauss%2FCRP%2Fmaster%2Fdatapackage.json))

Os códigos de CEP de 5 dígitos (`CEP5`) **são de domínio público**, como se espera que seja um bem cultural, um complemento aos nomes de cidade e nomes de bairro, presente em mapas e guias públicos, e com seu uso obrigatório em formulários e cadastros exigidos pelo governo desde a década de 1970.

O mesmo já ocorreu, mas não ocorre hoje (2016), com os CEPs de 8 dígidos (`CEP8`), que complementam nomes de rua e identificam trechos e localizações de interesse público. A *Empresa brasileira de Correios e Telégrafos* (ETC), [reclamou direitos autorais](http://pt.stackoverflow.com/q/54539/4186) sobre o banco de dados oficial do `CEP8`.

Para contornar o problema, é até simples,  
> basta não utilizar o nome "CEP" e não reproduzir exatamente a *string* do `CEP8` num banco de dados  público.

É solução em aplicações que fazem uso apenas indireto do CEP, como por exemplo validar endereços já localizados ou  validar mapeamentos de face de quadra.

# OBJETIVOS

A finalidade do presente projeto é  **estabelecer as convenções** para se transcrever a *string* de CEP no formato CRP, e demonstrar que a convenção é consistente, simples e reversível.

# CONVENÇÃO DO CRP

As convenções a seguir estão sendo submetidas à aprecisação da comunidade de potenciais usuários.

## Nome 

O nome "CRP" é uma alternativa à marca "CEP". Este projeto está efetuando o registro, com [licenças abertas](http://opendefinition.org/od/2.0/pt-br/), da marca "CRP". A seguir uma breve explanação e justificativas para a escolha da sigla "CRP".

As principais aplicações do CEP nos dias de hoje (2016) estão vinculadas às finalidades logísticas. Neste sentido o termo "roteamento" (empregado por exemplo na Alemanha e na Suíça) pode ser adotado no lugar de "endereçamento". Este é o nexo para a sugestão do  nome alternativo **`CRP`**, abreviação de **Código de Roteamento Postal**.

<small>NOTA: a ideia de "roteamento" no lugar de "endereçamento" ajuda inclusive a evitar confusão com termos como "endereço do lote" e "ponto de endereçamento" &mdash; o lote ou seu centroide pode ter mais de um CEP, ou seja, o lote pode ter mais de um portão para receber entregas.</small>

## Código
A proposta de sintaxe é bastante simples, a maior parte das explicações a seguir é para demonstrar que essa sintaxe é reversível e as suas especificações são completas.

Não é necessário um código único para todo o Brasil, a unicidade pode ser garantida por unidade da federação (UF), de modo que cada _string_ de CRP contém a  UF e um código contextualizado pela sua UF. Por exemplo "SP2345678".

Tendo isso em vista, a conversão entre CEP e CRP seria simples e facilmente reversível. Exemplos:

* Os CEPs de Minas Gerais ("30000-000" a "39999-999") seriam entradas com prefixo "MG" e sufixos variando de 0 a 9999999 (sete dígitos), ou seja, _strings_ representadas como "MG0000-000" a "MG9999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MG" por "3".

* Os CEPs  do Maranhão ("65000-000" a "65999-999") seriam entradas com prefixo "MA" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, _strings_ representadas como "MA000-000" a "MA999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MA" por "65".

* Os CEPs  do Amazonas ("69000-000" a "69299-999" e "69400-000" a "69899-999") seriam entradas com prefixo "AM" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, _strings_ representadas como "AM000-000" a "AM299-999" e "AM400-000" a "AM899-999". <br/>Para converter de CRP de volta para CEP basta trocar "AM" por "69".

A única excessão à regra do "nome da UF no prefixo" seria São Paulo, que tem uma conjunto de CEPs só para a zona/região metropolitana ("01000-000" a "09999-999"), batizado de "ZM" (com "Z" de "zona" para destacar dos demais).

A tabela completa, baseada na [lista geral dos CEPs](https://en.wikipedia.org/wiki/List_of_postal_codes_in_Brazil#Eight-digit_form), está em **[CEP-to-CRP.csv](data/CEP-to-CRP.csv)**, e define com rigor todos os detalhes da conversão entre *strings* de CEP e CRP.  O script PHP [rgxGen.php](src/rgxGen.php) gera as [*regular expressions*](https://en.wikipedia.org/wiki/Regular_expression) adequadas ao algoritmo de conversão &mdash;  dois exemplos foram implementados, um  em Javascript, [convert.js](src/convert.js), usado no [demo](http://ppkrauss.github.io/CRP), e outro em  PHP no script [convert.php](src/convert.php).

## Notas sobre extensibilidade e compactação
O formato CRM acima descrito também pode:

* ser estendido para uma segunda convenção OSM de "CEP de local sem CEP", ou seja, realizar um registro preliminar de CEP para locais tais como favelas, ocupação irregular e novos loteamentos. Convenções simples como concatenação  de uma letra podem ser usadas.

* ter seu código compactado, reduzido apenas à parte inteira (`CRP_int`), quando o contexto de UF (ou zona metropolitana) for conhecido.

## Notas sobre o ecosistema de CRPs
Outros países do Mercosul, como a Argentina, já adotam um sistema de codificação postal que inclui a "subdivisão principal do país" como prefixo. A codificação em uma ou duas letras dos nomes das subdivisões do país,  por sua vez, é padronizada pela ISO&nbsp;3166-2 &mdash; ver por exemplo [ISO&nbsp;3166-2:AR](https://en.wikipedia.org/wiki/ISO_3166-2:BR) e [ISO&nbsp;3166-2:BR](https://en.wikipedia.org/wiki/ISO_3166-2:BR).

## Notas sobre a implantação em SQL

Como numa [base de dados SQL](https://en.wikipedia.org/wiki/SQL) é mais econômico representar uma sequência de dígitos na forma de inteiro, uma tabela SQL de *códigos CRP* pode ser expressa como se segue:

```sql
CREATE TABLE crp (
	uf char(2) NOT NULL,	-- UF, letras CRP. REFERENCES state(uf).
	cod int NOT NULL,       -- dígitos CRP
	info  JSONb,            -- demais informações desejadas. PostgreSQL 9.5+
	PRIMARY KEY (uf,cod),
	CHECK(crp_is_valid(uf,suffix))
);

CREATE VIEW vw_crp AS 
  SELECT *, crp_format(uf,cod) AS crp, crp_asCEP(uf,cod) AS cep 
  FROM crp;
```

No caso do PostgreSQL, que oferece nativamente o tratamento de regurlar expressions,  o código das  funções `crp_is_valid()`,  `crp_asCEP()` e `crp_format()` pode ser implementado em SQL,  [PL/pgSQL](https://www.postgresql.org/docs/9.5/static/plpgsql.html) ou adaptando diretamente os códigos deste projeto ([convert.js](src/convert.js) para [PLv8](https://github.com/plv8/plv8) ou [convert.php](src/convert.php) para [PL/PHP](https://www.postgresql.org/docs/9.5/static/external-pl.html)).

------

[![License: CC0](https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/CC0_button.svg/88px-CC0_button.svg.png)(http://creativecommons.org/publicdomain/zero/1.0)
