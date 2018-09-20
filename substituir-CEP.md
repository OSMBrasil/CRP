
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
