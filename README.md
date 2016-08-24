# CRP - *C*ódigo de *R*oteamento *P*ostal

Os códigos de CEP de 5 dígitos (`CEP5`) **são de domínio público**, como se espera que seja um bem cultural, um complemento aos nomes de cidade e nomes de bairro, presente em mapas e guias públicos, e com seu uso obrigatório em formulários e cadastros exigidos pelo governo desde a década de 1970.

O mesmo já ocorreu, mas não ocorre hoje (2016), com os CEPs de 8 dígidos (`CEP8`), que complementam nomes de rua e identificam trechos e localizações de interesse público. A *Empresa brasileira de Correios e Telégrafos* (ETC), [reclamou direitos autorais](http://pt.stackoverflow.com/q/54539/4186) sobre o banco de dados oficial do `CEP8`. Para resolver a situação, permitindo ao menos publicar abertamente na Internet mapas ou listagens dos códigos de CEP, uma negociação dos representantes da [*OpenStreetMap*](http://www.openstreetmap.org/about)  com a [nova diretoria da ETC](http://www.osul.com.br/correios-reduzem-salario-da-diretoria/), e uma [Ação Civil Pública](https://pt.wikipedia.org/wiki/A%C3%A7%C3%A3o_civil_p%C3%BAblica) e de insconstitucionalidade,  entraram em andamento... Por ser encaminhamento coletivo e de interesse difuso, processso será certamente moroso (anos).

Para contornar por hora esse problema, é até simples,
> basta não utilizar o nome "CEP" e não reproduzir exatamente a *string* do `CEP8` num banco de dados  público.

O presente projeto é uma proposta de padronização simples para que o cidadão brasileiro, e as comunidades [OpenStreetMap-Brasil](http://www.openstreetmap.com.br), [Geonames]()  e outras, possam trabalhar mais a vontade com os dados do CEP.

# OBJETIVOS

A finalidade do presente projeto é simplesmente **estabelecer uma convenção para transcrever a *string* de CEP em um outro formato** (digamos XCEP), e demonstrar que a convenção é consistente, simples e reversível.

Num segundo momento, em um projeto muito mais amplo, denominado atualmente de *[Mapa do CEP](http://wiki.okfn.org/Open_Knowledge_Brasil/Mapa-do-CEP)*, a proposta então o armazenamento de dados de XCEP nos mapas da OpenStreetMap-Brasil (OSM-BR). Na prática isso consiste em  transcrever todos os dados de CEP em XCEP, armazenando o XCEP nos mapas da OSM-BR. Outros projetos, tais como API para a resolução de coordenadas espaciais (transformar coordenada em  XCEP) também seriam usuárias da convenção.

# CONVENÇÃO PROPOSTA
Nome e *string* alternativos para evitar conflito de direitos autorais e usar [licenças abertas](http://opendefinition.org/od/2.0/pt-br/) sobre os dados. A proposta de sintaxe é bastante simples, a maior parte das explicações é para demonstrar que essa sintaxe é reversível e as suas especificações são completas.

## Nome alternativo ao CEP

As principais aplicações do CEP nos dias de hoje (2016) estão vinculadas às finalidades logísticas. Neste sentido o termo "roteamento" (empregado por exemplo na Alemanha e na Suíça) pode ser adotado no lugar de "endereçamento". Este é o nexo para a sugestão do  nome alternativo **`CRP`**, abreviação de **Código de Roteamento Postal**.

<small>NOTA: a ideia de "roteamento" no lugar de "endereçamento" ajuda inclusive a evitar confusão com termos como "endereço do lote" e "ponto de endereçamento" &mdash; o lote ou seu centroide pode ter mais de um CEP, ou seja, o lote pode ter mais de um portão para receber entregas.</small>

## String alternativa ao CEP
A representação do CEP não precisa ser um inteiro único para o Brasil, podem ser inteiros com unicidade garantida por unidade da federação (UF), de modo a termos em uma [base SQL](https://en.wikipedia.org/wiki/SQL) cada _string_ de CEP representada pela UF e um código dentro da UF, ou seja, com a unicidade garantida por `UNIQUE(uf,codigo)`. Podemos convencionar que a _string_ alternativa é então algo como o código da UF concatenado ao código restante do CEP. Por exemplo "SP12345-678".

Como na base de dados é mais econômico representar uma sequência de dígitos na forma de inteiro (uma [máscara simples de `printf`](https://en.wikipedia.org/wiki/Printf_format_string) converte de volta na _string_ padrão), uma tabela SQL de *códigos CRP* pode ser expressa como tabela PostgreSQL como por exemplo,


```sql
CREATE TABLE crp (
	prefix char(2) NOT NULL,	-- UF, letras CRP. REFERENCES state(uf).
	suffix int NOT NULL,   		-- dígitos CRP
	info  JSONb,        		-- demais informações desejadas.
	PRIMARY KEY (prefix,suffix)
);
```

Tendo isso em vista, a conversão entre CEP e CRP seria simples e facilmente reversível. Exemplos:

* Os CEPs de Minas Gerais ("30000-000" a "39999-999") seriam entradas com prefixo "MG" e sufixos variando de 0 a 9999999 (sete dígitos), ou seja, _strings_ representadas como "MG0000-000" a "MG9999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MG" por "3".

* Os CEPs  do Maranhão ("65000-000" a "65999-999") seriam entradas com prefixo "MA" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, _strings_ representadas como "MA000-000" a "MA999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MA" por "65".

* Os CEPs  do Amazonas ("69000-000" a "69299-999" e "69400-000" a "69899-999") seriam entradas com prefixo "AM" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, _strings_ representadas como "AM000-000" a "AM299-999" e "AM400-000" a "AM899-999". <br/>Para converter de CRP de volta para CEP basta trocar "AM" por "69".

A única excessão à regra do "nome do estado no prefixo" seria São Paulo, que tem uma conjunto de CEPs só para a zona metropolitana ("01000-000" a "09999-999"), o qual poderia ser batizado de "ZM".

A tabela completa, baseada na [lista geral dos CEPs](https://en.wikipedia.org/wiki/List_of_postal_codes_in_Brazil#Eight-digit_form), está em **[CEP-to-CRP.csv](data/CEP-to-CRP.csv)**, e define a rigor todos os detalhes da conversão entre *strings* de CEP e CRP.  O script PHP [convert.php](src/convert.php) exemplifica a implementação da conversão através de algoritmos simples.

## Notas sobre extensibilidade e compactação
O formato CRM acima descrito também pode:

* ser estendido para uma segunda convenção OSM de "CEP de local sem CEP", ou seja, realizar um registro preliminar de CEP para locais tais como favelas, ocupação irregular e novos loteamentos. Convenções simples como concatenação  de uma letra podem ser usadas.

* ter seu código compactado, reduzido apenas à parte inteira (`CRP_int`), quando o contexto de UF (ou zona metropolitana) for conhecido.

## Notas sobre o ecosistema de CRPs
Outros países do Mercosul, como a Argentina, já adotam um sistema de codificação postal que inclui a UF (a rigor "subdivisão principal") como prefixo. A codificação em 2 letras dos nomes das subdivisões do país,  por sua vez é padronizada pela ISO&nbsp;3166-2 &mdash; ver por exemplo [ISO&nbsp;3166-2:AR](https://en.wikipedia.org/wiki/ISO_3166-2:BR) e [ISO&nbsp;3166-2:BR](https://en.wikipedia.org/wiki/ISO_3166-2:BR).
