# CRP
Código de Roteamento Postal

-----

Os [algoritmos de mapeamento baseados em *faces de quadra*](https://en.wikipedia.org/wiki/Postal_code#Codes_defined_independently_from_administrative_borders) são relativamente simples (podem ser implementados por ex. com PostGIS) e, com a  disponibilização os [dados públicos de face de quadra pelo IBGE em 2016](https://lists.openstreetmap.org/pipermail/talk-br/2016-July/011502.html), tornaram-se viáveis. 

Os códigos de CEP de 5 dígitos (`CEP5`) são de domínio público, como qualquer outra norma técnica citada por lei no sistema legislativo brasileiro. Os códigos de 8 dígitos (`CEP8`) também deveriam ser.

Em anos recentes a entidade encarregada do controle dos códigos de CEP, a "Empresa brasileira de Correios e Telégrafos - ECT", reclamou direitos autrais sobre a marca e o banco de dados oficial do CEP, [apesar de serem direitos adquiridos em 1978 e tornados inconstitucionais a partir de 1988](http://pt.stackoverflow.com/q/54539/4186). Uma negociação com os Correios, ou uma eventual contestação jurídica, estão em curso, mas é um processo incerto e moroso.

O cidadão e as empresas brasileiras teem direito de uso do CEP como dado aberto por ser o CEP uma norma requisitada por Lei, e portanto, automaticamente, uma obriagação do Estado a sua publicidade e expressão em domínio público, tal como os nomes de rua. O CEP é como qualquer outra tabela anexa a uma Lei, e "dono do CEP" é a Câmara Municipal ("dona" dos nomes de rua e [autoridade das "leis de batismo"](http://www.lexml.gov.br/busca/search?keyword=denomina+via&f1-tipoDocumento=Legisla%C3%A7%C3%A3o)), que apenas delega o "batismo de CEP" aos Correios.

Para contornar esse problema, 
> basta não utilizar o nome "CEP" e não reproduzir exatamente a string do `CEP8`. 

O presente projeto é uma proposta de padronização simples para que a comunidade OSM possa trabalhar mais a vontade com os dados do CEP.
 
## Nome alternativo ao CEP

As principais aplicações do CEP nos dias de hoje (2016) estão vinculadas às finalidades lojísticas. Neste sentido o termo "roteamento" (empregado por exemplo na Alemanha e na Suíça) pode ser adotado no lugar de "endereçamento". Este é o nexo para a sugestão do  nome alternativo **`CRP`**, abreviação de **Código de Roteamento Postal**.
<br/><small>NOTA: a ideia de "roteamento" no lugar de "endereçamento" ajuda inclusive a evitar confusão com termos como "endereço do lote" e "ponto de endereçamento" &mdash; o lote ou seu centroide pode ter mais de um CEP, ou seja, o lote pode ter mais de um portão para receber entregas.</small>

## String alternativa ao CEP
A representação do CEP não precisa ser um inteiro único para o Brasil, podem ser inteiros com unicidade garantida por unidade da federação (UF), de modo a termos em uma [base SQL](https://en.wikipedia.org/wiki/SQL) cada string de CEP representada pela UF e um codigo dentro da UF, ou seja, com a unicidade garantida por `UNIQUE(uf,codigo)`. Podemos convencionar que a string alternativa é então algo como o código da UF concatenado ao código restante do CEP. Por exemplo "SP12345-678".

Como na base de dados é mais econômico representar uma sequência de dígitos na forma de inteiro (uma [máscara simples de `printf`](https://en.wikipedia.org/wiki/Printf_format_string) converte de volta na string padrão), uma tabela SQL de *códigos CRP* pode ser expressa como tabela PostgreSQL como por exemplo,


```sql
CREATE TABLE crp (
	prefix char(2) NOT NULL,	-- UF, letras CRP. REFERENCES state(uf).
	suffix int NOT NULL,   		-- dígitos CRP
	info  JSONb,        		-- demais informações desejadas.
	PRIMARY KEY (prefix,suffix)
);
```

Tendo isso em vista, a conversão entre CEP e CRP seria simples e facilmente reversível. Exemplos:

* Os CEPs de Minas Gerais ("30000-000" a "39999-999") seriam entradas com prefixo "MG" e sufixos variando de 0 a 9999999 (sete dígitos), ou seja, strings representadas como "MG0000-000" a "MG9999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MG" por "3".

* Os CEPs  do Maranhão ("65000-000" a "65999-999") seriam entradas com prefixo "MA" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, strings representadas como "MA000-000" a "MA999-999". <br/>Para converter de CRP de volta para CEP basta trocar "MA" por "65".

* Os CEPs  do Amazonas ("69000-000" a "69299-999" e "69400-000" a "69899-999") seriam entradas com prefixo "AM" e sufixos variando de 0 a 999999 (seis dígitos), ou seja, strings representadas como "AM000-000" a "AM299-999" e "AM400-000" a "AM899-999". <br/>Para converter de CRP de volta para CEP basta trocar "AM" por "69".

A única excessão à regra do "nome do estado no prefixo" seria São Paulo, que tem uma conjunto de CEPs só para a zona metropolitana ("01000-000" a "09999-999"), o qual poderia ser batizado de "ZM".

A tabela completa, baseada na [lista geral dos CEPs](https://en.wikipedia.org/wiki/List_of_postal_codes_in_Brazil#Eight-digit_form), está em [CEP-to-CRP.csv](data/CEP-to-CRP.csv).







