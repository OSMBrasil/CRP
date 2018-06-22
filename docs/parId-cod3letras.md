## Algoritmo para a redução de nome de cidade a código de 3 letras

Nota: este algoritmo faz parte da proposta do [PAR-ID](parId.md), o código de endereçamento rural. O código de abreviatura de três letras do nome do municipio é um dos componentes do PAR-ID.

Como o Estado de São Paulo tem 645 municípios e o alfabeto moderno tem 26 letras, com 3 letras dá e sobra para a identificação única dos municípios: existem 26*26*26 = 17576 códigos, ou seja, cada cidade pode optar, em média, entre ~27 códigos que melhor se candidatariam a "sigla" do seu nome. Mesmo quando duas ou mais cidades apresentarem nomes ou abreviações parecidas, não há risco de ficar sem opção adequada para a escolha de uma sigla razoável.

Por ser um código compacto, não se pode esperar "ótimas siglas"... Exceto no caso de nomes de cidade tais como Itu ou Poá, que já estariam prontos, ou com 3 nomes, tais como  Campo Limpo Paulista e São José dos Campos, cujas iniciais `CLP` e `SJC` possuem três letras; ou ainda Salto e Avaré, cujas inicias seguidas de vogais (`SLT` e `AVR`) já formam automaticamente o seu código.  Para a grande maioria, todavia, será no máximo aquilo que se espera, um "código mnemônico razoável".

As **principais justificativas e garantias** para usar algoritmos são as seguintes:

1. "lógica mnemônica" para quem precisar decorar mais de um código;

2. "eleição justa", para os municípios e munícipes não tenham a chance de opinar;

3. dilui o custo presente e risco futuro de uma *autoridade central* ofertando a tabela "deNome-paraCódigo" e atribuindo códigos a cada nova modificação na lista de nomes oficiais;

4. garante a reutilização da mesma lógica em outros estados, tornando mais atrativa sua adoção como padrão em escala nacional.

Além dos exemplos mais evidentes já citados, um bom exemplo de "lógica mnemônica", para a construção dos códigos, é o referencial das opções:

* pela lógica dos aeroportos (siglas IATA), quebra-se em sílabas para construir as opções. Exemplos: Uberaba (U-BE-RA-BA) opta entre `UBE`, `URA` e `UBA`;  Uberlândia (U-BER-LAN-DIA) opta entre `UBE`, `ULA` e `UDI`. Salto e Avaré poderiam ser `SAL` e `AVA`.

* pela lógica da redução direta, elimina-se as vogais. Exemplos: Uberaba (UBRB) opta entre `UBR`, `UBB` ou `URB`;  Uberlândia (UBRLND) opta entre `UBR`, `UBL`, `UBN`, `URD`, etc. Salto e Avaré seriam `SLT` e `AVR`.

* pela lógica mista: algo como optar em Avaré por `AVA` ou `AVR`, em Uberaba por `UBE`, `UBR`, `URA`, `UBB`, etc. nesta ordem de prioridade.

Qualquer que seja a decisão, será uma lógica apenas preferencial, que permitiria deduzir digamos metade dos códigos com facilidade, mas não todos eles.

Do ponto de vista puramente computacional a adoção de um algoritmo simples (digamos 100 linhas de código de Perl ou Javascript) permite que o padrão seja auto-suficiente, sem gasto de memória de dados nem demandar acesso à internet para consultar uma *autoridade central*. Se está em ambiente sem internet mas tem a tabela oficial do IBGE, pode gerar os códigos.

Comparativamente com uma tabela de ~650 entradas "deNome-paraCódigo", o algoritmo compilado é muito mais econômico, por exemplo para se manter o *cache* os códigos num celular.  Mais ainda quando forem os ~5500 municípios brasileiros.

## Escopo, algoritmo ideal e metadados

Conceitualmente a tabela DeNome-paraCódigo construída por algoritmo é dita **"transparente"** porque todos podem ver e auditar o processo que levou do nome oficial da cidade para o código oficial de 3 letras. Uma tabela imposta por julgamento humano, por melhor que seja, terá sempre como referência um processo de [caixa preta](https://en.wikipedia.org/wiki/Black_box). Por isso dizemos que a codificação de 3 letras resultante do algoritmo público  é *transparente* &mdash;  importante que seja também licenciado por [CC0](https://creativecommons.org/publicdomain/zero/1.0/) &mdash;, e a resultante do julgamento humano é *opaca*.

As metas principais do projeto podem ser conseguidas com um algoritmo que se baseie apenas na listagem dos nomes oficiais dos municípios. Por outro lado, para otimizar e garantir por exemplo que o grupo das cidades maiores e mais densamente habitadas tenham mais chances de obter um "código ótimo". Nesta caso a planilha oficial passaria a ter 3 colunas: nome, ano de fundação, número de habitantes. A [Lista de municípios de São Paulo por população](https://pt.wikipedia.org/wiki/Lista_de_municípios_de_São_Paulo_por_população) dá uma ideia de quais seriam os 10 a 50 municípios priorizados neste "grupo de elite".

Apesar do algoritmo ideal exigir mais dados, o resultado continuará sendo considerado *transparente*, pois será baseado em dados oficiais e amplamente acessíveis para o cidadão que desejar auditar os códigos ou gerar de forma autônoma a sua tabela DeNome-paraCódigo. As duas colunas-extra são apenas referências de agrupamento para priorizar na sequência de escolha e atribuição de códigos.

<!--
se contemplar cidades de maior população com siglas mais amigáveis &mdash; o ano ajuda a estimar menos subjetivamente o critério de "maior população", numa perspectiva de décadas.

PS: em 1900 a distribuição da população era muito mais uniforme, a diferença exponencial entre mais e menos populosas ocorreu décadas depois

* https://pt.wikipedia.org/wiki/Lista_de_munic%C3%ADpios_de_S%C3%A3o_Paulo_por_popula%C3%A7%C3%A3o_(1900) são carlos era importante
* https://pt.wikipedia.org/wiki/Lista_de_munic%C3%ADpios_de_S%C3%A3o_Paulo_por_popula%C3%A7%C3%A3o_(1960) mudanças de nomenclatura
* Cidadades com mais de 100mil e com mais de 1 milhão.
-->

## Colisões fora de ordem

Suponhamos que a cidade de Itaju tenha preferência pelas abreviações `IJU` ou `ITJ`... E que o algoritmo processe Itaí, Itajobi e Itaju, nesta ordem,  abreviando respectivamente, na primeira tentativa, como `ITA`, `ITJ` e `ITU`, e em seguida, ao conferir que Itu já estaria ocupada, busca outras abreviações (que idealmente resultaria em `ITB` e `ITJ` para as duas úlimas).

Esse caso destaca que nem sempre obteremos uma regra de resolução de colisões perfeita com base apenas na ocupação sequencial, sem conferir globalmente as colisões antes das atribuições definitivas. Itaju não poderia usar nem `ITA` nem `ITJ`, restando algo como `IJU`. Uma das "saídas" é a utilização de siglas oficiais (ex. padrão IATA utilizado em aeroportos) ou consagradas pelo uso popular (ex. "Sampa" para São Paulo).

Também existem tabelas de abreviações padronizadas por outros órgãos, apesar de não serem abreviações oficiais nem consagradas. Por exemplo a [tabela do CDHU](http://www.cdhu.sp.gov.br/download/manuais-e-cadernos/nomenclatura/nomenclatura-municipios.pdf). O ideal, todavia, é apenas **"treinar" o algortimo** (ou parte dele) com ajuda dessas tabelas, para estabelecer um referencial de boas práticas nos algorítmos.

<!--
## Siglas e abreviações consagradas

Aqui vale novamente a estratégia do "treino" do algorítimo. Por exemplo a tabela de siglas utilizadas por aeroportos nos fornece
PTM: Patos de Minas-MG
PTS: Patos-PB

URA: Uberaba-MG  = U-BE-RA-BA = UBE, URA, UBA
UDI: Uberlândia-MG = = U-BER-LAN-DIA = UBE, ULA, UDI.

PTC: Patrocínio-MG, PAT e PTRC .. PA-TRO-CI-NI-O, PTC
BSB: Brasília-DF
ANS: Anápolis-GO
CTB: Curitiba-PR
PAM: Pará de Minas-MG
BHZ: Belo Horizonte-MG
MOC: Montes Claros-MG
não MCL

VZT: Vazante-MG
POA: Porto Alegre-MG
GV: Governador Valadares-MG
ITBA: Ituiutaba-MG
JAMPA: João Pessoa-PB
SJC: São José dos Campos-SP
-->

## ALGORITMO SUGERIDO

A "receita" será apenas esboçada para mostrar que é factivel. Como a implementação de referência será escrita em [Perl](https://en.wikipedia.org/wiki/Perl), podemos aqui na descrição geral do algoritmo adotar a convenção dos prefixos `@` e `$` para designar nomes de variáveis, respectivamente listas e strings. A grosso modo o algoritmo corresponde aos seguintes passos:


1. Carga e filtragem. Carregar todos os nomes em `@N` e a cada um dos nomes aplicar os filtros iniciais. Os códigos eleitos vão posteriormente sendo carregados na lista associativa de nomes-candidatos, `%NC`, e de nomes finais, `%NF`, inicialmente vazias.

2. Varredura dos casos ótimos. Aqueles poucos, "perfeitos", como Itu e São José dos Campos que já ficam carregados de antemão em `%N`, ou seja, `$NF{ITU}="ITU"` e  `$NF{SAO JOSE DOS CAMPOS}="CJC"`.<!--  Águas de Santa Bárbara = ASB, Águas de São Pedro = ASP, Boa Esperança do Sul = BES, Bom Jesus dos Perdões = BJP, etc.-->

3. Os restantes nomes de  `@N` que não tiveram a sorte de ir para `%NF`, ficam dividios em dois gruopos, o "de elite", `@GE` e o normal, `@GN`.

4. Aplicar o algorito principal a `@GE`, o grupo de elite.

5. Aplicar o algorito principal a `@GN`, o grupo dos restantes.

### Filtros e demais subrotinas

A seguir apenas uma desecrição sumária dos principais filtros e métodos de redução utilizados no algoritmo principal e nos filtros.

* Filtragem inicial. Remover acentos e colocar todas as suas letras em maiúsculas.

* Decomposição silábica.

* Redução de inicial e  vogais.

* Tratamento de exceções e preposições: decide se palavras  como "de", "do" ou "das" são descartáveis (preposições) como tratar apóstrofes, como em "Santa Rita d'Oeste".

* Tratamento de nomes compostos com mais de três palavras, como "Santa Rita do Passa Quatro", onde a heurística sugere descartar a segunda inicial e ficar com as duas últimas (SPQ).

* Controle de sequências de tentivas. Sequências heurísticas para se combinar letras depois que as melhores foram descartadas por colisão. <!--  Em caso de colisão, aplicar regras do item 1 e tentar 1XX com XX={12,21,13,31}, X1X, XX1, depois demais variações.-->

### Formação do "grupo de elite"

Nomes são também tradições. A cidade de São Carlos tem mais de 100 anos. Em 1900 era uma das 5 mais populosas, hoje está na 31º posição... Ainda assim manteve-se entre as 10% maiores. Essa "inércia" das cidades antigas permite-nos chutar melhor. O algoritmo portanto seria algo como:

1. Cidades com mais de *H×M* mil habitantes, resultando *n* cidades eleitas.

2. Unior ao grupo as *n/3*  cidades  mais antigas entre aquelas com mais de <i>M×(H-1)</i> mil habitantes.

Por exemplo *M=100* e *H=5*, equivale a dizer grosseiramente que a "a nota de corte é 500 mil habitantes". Por volta de 10 cidades satisfarão o primeiro critério e outrs 3 cidades satisfarão o segundo.
