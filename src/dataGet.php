<?php
// PHP para validar SQL, apenas garantindo que geram mesmos resultados no diff.
// USAGE:
//   php src/dataGet.php
//

include('convert.php');

foreach (scandir('./data') as $ufDir) if (is_dir($upath = "./data/$ufDir") && preg_match('/^[A-Z][A-Z]$/',$ufDir)) {
	print "\n$ufDir:";
	foreach (scandir($upath) as $city) if (strlen($city)>4) {
		$f = "$upath/$city";
		print "\n\t$f";
		if (file_exists($f))
			$tab = array_map('str_getcsv', file($f)); // ler
		else
			die("\nERRO $f não existe.\n");
		$head = array_shift($tab);
		if ($head[0]!='cep') die("\n ERRO: arquivo $f foi alterado.");

		$c = new CRPconvert();
		$fpw = fopen($f, 'w'); // gravar

		//  MAIN
		$head[0]='CRP';
		fputcsv($fpw, $head);
		foreach ($tab as $linha) {
				if ( !preg_match('/^\d{8,8}$/',$linha[0]) ) die("\n ERRO em $linha[0]");
				$c->set($linha[0]);
				$linha[0] = $c->asCRP();
		    fputcsv($fpw, $linha);
		}
		fclose($fpw);
	}// \for city
}// \for UF

print "\nFIM\n";
