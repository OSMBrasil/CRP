
# Motivos para substituir o CEP

O [Código de Endereçamento Postal](https://pt.wikipedia.org/wiki/C%C3%B3digo_de_Endere%C3%A7amento_Postal) foi introduzido
em 1971, inspirado no no [modelo estadunidense](https://en.wikipedia.org/wiki/ZIP_Code)...
Existem justificavas históricas para a sequência de decisões tomadas,  mas nos dias de hoje não existem motivos racionais para se continuar usando o CEP:

1. Como **padrão de apoio à automação e à garantia de precisão do [endereço postal](https://schema.org/PostalAddress)**, é obsoleto, hoje existem formas mais seguras, compactas e eficientes.

2. Como **utilidade pública**:

     2.1. Na garantia a *redução de custos* ao usuário do padrão, nunca foi inteiramente "barato", pelo contrário, nos anos recentes com a demanda de diversos setores pelo uso de bases de endereços confiáveis, se tornou umgrande custo [por ser uma base de dados privada](https://pt.stackoverflow.com/q/54539/4186).

     2.2. Na *oferta plural e democrática da utilidade*, nunca foi universal, ainda hoje, ~30 anos depois de implementado o CEP de 8 dígitos, são poucas as cidades com "CEP de rua", e, mesmo nos locais onde foi implantando, há uma enorme fila de espera pelo CEP do bairro ou loteamento.

3. Como **padrão de "endereço estepe"**, complemento de segurança do endereço expresso por *"cidade, logradouro e número"*: nunca foi solução pois o CEP não representa o endereço, apenas uma parte dele.

4. Como "identificador de face de quadra ou conjunto de quadras":

    4.1. É um *identificador opaco*, e não há perspectiva técnica para mudar esse fato (se tornar *identificador transparente*), ou perspectiva política de se decentralizar a autoridade do CEP.<br/>O item 2.2 acima reforça e confirma a demanda por decentralização.

    4.2. Carece de recursos para a *garantia de unicidade*. Ruas diferentes com mesmo CEP ou falhas da autoridade central no "processo de batizar com CEP", não podem ser facilmente corrigidas, perduram e se propagam por anos.

## Histórico e perspectivas

Na década de 1970, quando o CEP surgiu, os recursos computationais eram caros e escaços, somente grandes empresas tinham condições de gerenciar bancos de dados
de escala nacional, e assumir perante o governo a responsabilidade por uma base como a do CEP.

Na década de 1990 houve uma atualização também inspiradada na atualização
estadunidense (o ZIP+4 da década de 1980).
Depois disso, no Brasil e no mundo, os assim chamados *postal codes* foram dando mostras de obsolecência por conta dos mapas na internet e recursos automáticos de localização e geocodificação.
As demandas por "coordenadas do local", com precisão de metros, começaram a ser mais importantes do que o "logradouro de acesso ao local"...

Com as tecnologias de indexação espacial de hoje (2018) e seus identificadores amigáveis,
notadamente [Geohash, PlusCode, S2 e similares](https://en.wikipedia.org/wiki/Discrete_Global_Grid#Alphanumeric_global_grids),
fica evidente que podemos fazer melhor:

* São [identificadores transparentes](https://doi.org/10.5281/zenodo.159004), não demandam autoridade central para "batizar o local".
* São tecnologias abertas, baseadas em padres abertos, e softwares com licenças livres. Não existe patente ou direitou autoral cerceando o direto do cidadão e gerando custos.
* Expressam o endereço integral, não só uma parte dele.

Substituir o CEP por qualquer uma dessas tecnologias livres seria um grande avanço.

## Primeiros passos

O problema é antigo, mas só com o crescimento da demanda por pequenas e médias empresas, e por fim com a demanda do terceiro setor
brasileiro, que nos anos recentes engrossou o movimento por dados abertos, é que veio a ser reconhecido como PROBLEMA.

A primeira manifestação pública e focada na questão parece ter sido iniciada em 2012 e consolidada como [relatório contundente do Código Urbanao em 2015](https://web.archive.org/web/20150321054429/http://codigourbano.org/por-que-o-cep-deve-ser-tratado-como-informacao-publica/). O pedido formal de 2012 expressou com clareza:

> Muitos sistemas necessitam acesso à base de dados do CEP, porém a ECT coloca barreiras técnicas e financeiras ao acesso a estes dados. A falta de acesso livre a esta base de dados causa a disseminação de cópias desatualizadas, o que prejudica não apenas os usuários mas a própria ECT, uma vez que ela é obrigada a entregar correspondência mesmo com o CEP informado incorretamente. Portanto é do interesse do público e da própria ECT que estes dados sejam oferecidos de forma livre através de uma API (interface de programação) aberta e de fácil utilização.<br>[eSIC 2012]

No mesmo ano, uns meses depois, surgiu uma inusitada "chamada para o debate" em um site de perguntas e respostas técnicas, [pt.stackoverflow.com/54539](https://pt.stackoverflow.com/q/54539/4186), e a questão foi novamente debatida, desta vez com um enfoque mais orientado à demanda que poderia ser resumida como *"direito de publicar uma lista de números CEPs da cidade"*, que, assim como o direito de publicar nomes de rua, não admite direitos autorais, e o detentor de *direitos morais sobre a obra* (uma simples listagem de números), seria a Câmara Municipal, não uma empresa privada.

Em 2018 foi esboçado um [primeiro rascunho para explicar o que seria um código mais moderno](http://openstreetmap.com.br/CLP) e capaz de substituir o CEP sem prolemas de direito autorial ou "autoridade central de batismo".

<!--
FONTES:

* https://web.archive.org/web/20180926112715/https://www.codigourbano.org/por-que-o-cep-deve-ser-tratado-como-informacao-publica/
* https://web.archive.org/web/20180926112754/https://www.codigourbano.org/integra-do-posicionamento-dos-correios-sobre-a-abertura-da-base-de-dados-do-cep/
* https://web.archive.org/web/20180926112931/http://www.acessoainformacao.gov.br/precedentes/ECT/99923000436201387.pdf
* https://web.archive.org/web/20180926112850/http://www.acessoainformacao.gov.br/precedentes/ECT/99923001172201206.pdf
* https://web.archive.org/web/20180926112252/https://www.escavador.com/sobre/12890020/odarci-roque-de-maia-junior

Outros: segundo este post, https://pt.stackoverflow.com/a/57858/4186  , reclama autoria do pedido de 2016, registrado como https://web.archive.org/web/20180926120112/http://www.consultaesic.cgu.gov.br/busca/dados/Lists/Pedido/Item/displayifs.aspx?List=0c839f31%252D47d7%252D4485%252Dab65%252Dab0cee9cf8fe&ID=454529&Web=88cc5f44%252D8cfe%252D4964%252D8ff4%252D376b5ebb3bef&_InfoPath_Sentinel=1
Mas sem registro. Vale imagem,
http://www.consultaesic.cgu.gov.br/busca/dados/Lists/Pedido/Item/displayifs.aspx?List=0c839f31-47d7-4485-ab65-ab0cee9cf8fe&ID=454529&Web=88cc5f44-8cfe-4964-8ff4-376b5ebb3bef
Foi "Acesso gratuito para consulta de informações de CEP - Pedido 99923000332201615", ironicamente registrado como "acesso concedido".

-->
