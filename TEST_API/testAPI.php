
<?php
	
	
	function appelAPI($apiUrl, $apiKey, &$http_status, $typeRequete="GET", $donnees=null) {
		// Interrogation de l'API
		// $apiUrl Url d'appel de l'API
		// $http_status Retourne le statut HTTP de la requete
		// $typeRequete = GET / POST / DELETE / PUT, GET par défaut si non précisé
		// $donnees = données envoyées au format JSON en PUT ET POST, rien si GET ou DELETE
		// La fonction retourne le résultat en format JSON
		
		$curl = curl_init();									// Initialisation

		curl_setopt($curl, CURLOPT_URL, $apiUrl);				// Url de l'API à appeler
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);			// Retour dans une chaine au lieu de l'afficher
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 		// Désactive test certificat
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		
		// Parametre pour le type de requete
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $typeRequete); 
		
		// Si des données doivent être envoyées
		if (!empty($donnees)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $donnees);
			curl_setopt($curl, CURLOPT_POST, true);
		}
		
		$httpheader []= "Content-Type:application/json";
		
		if (!empty($apiKey)) {
			// Ajout de la clé API dans l'entete si elle existe (pour tous les appels sauf login)
			$httpheader = ['APIKEYDEMONAPPLI: '.$apiKey];
		}
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
		
		// A utiliser sur le réseau des PC IUT, pas en WIFI, pas sur une autre connexion
		// Uniquement sur les URL externes (pas en utilisant une API en localhost)
		//$proxy="http://cache.iut-rodez.fr:8080";
		//curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
		//curl_setopt($curl, CURLOPT_PROXY,$proxy ) ;
		///////////////////////////////////////////////////////////////////////////////
		
		$result = curl_exec($curl);								// Exécution
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);	// Récupération statut 
		
		curl_close($curl);										// Cloture curl

		if ($http_status=="200" or $http_status=="201" ) {		// OK, l'appel s'est bien passé
			return json_decode($result,true); 					// Retourne la collection 
		} else {
			$result=[]; 										// retourne une collection Vide
			return $result;
		}
	}
	
	function trisAlpha($laCollection) {
		// Retourne la collection triée dans l'odre alphabétique des valeurs
		if ($laCollection!=null) {
			$tabtris = new ArrayObject($laCollection);
			$tabtris->asort();
		} else {
			$tabtris=[];
		}
		return $tabtris;
	}
	
?>


<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<title>Test de l'API créée</title>

		<!-- Bootstrap CSS -->
		<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
		
	</head>
	<body>
	
	<?php 
		// Url API (A changer en fonction de votre hébergement)
		$urlDeLAPI="http://localhost/API_Demo/API" ;
		
		// URLS exemples
		// http://localhost/API_Demo/API/typesClients   Liste des types de clients
		// http://localhost/API_Demo/API/clients    	Liste des clients
		// http://localhost/API_Demo/API/clients/1		Liste des clients de la catégorie1
		// http://localhost/API_Demo/API/client/2		Client Id 1

		// Récupération de la clé API (au premier appel)
		// Demande d'une clé API pour l'accès à l'API.
		$monLogin="G40";
		$monPassword="W38T4";
		
		$apiUrl=$urlDeLAPI."/login/".$monLogin."/".$monPassword;
		$resultat=appelAPI($apiUrl, "", $status);

		if (isset($resultat["APIKEYDEMONAPPLI"])) {
			$apiKey=$resultat["APIKEYDEMONAPPLI"]; 
		}else {
			$apiKey="" ;
		}

		
		//$apiKey="JAQ2345RTtve";
	?>
	
		<div class="container">
			<div class="row">
				
				<div class="col-xs-12">
					<?php
						echo "<h1>Ma clé API récupérée grâce à la méthode login : ".$apiKey."<br/><br/></h1>";
					?>

					<h1>Liste types clients</h1>
					<?php 
						// Recherche liste des Types Clients
						$apiUrl=$urlDeLAPI."/typesClients";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);
						echo "<h2>http_Status=".$status."</h2><br>";
						$resultat=trisAlpha($resultat);
						echo "<ul>";
						foreach($resultat as $value) {
							echo "<li>".$value['TYPE_CLIENT']." ".$value['TYPE_CLIENT_DESIGNATION']."</li>";
						}
						echo "</ul>";
					?>
				</div>
				
				<div class="col-xs-12">
					<h1>Liste des Clients</h1>
					<?php 
					
						$apiUrl=$urlDeLAPI."/clients";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey,$status);
						echo "<h2>http_Status=".$status."</h2><br>";
						
						$premier=true;
						echo "<table class='table table-striped table-bordered'>";
						foreach($resultat as $cle=>$value) {
							if ($premier) {
								// Première ligne, Affichage des entetes récupérées dans l'entete du tableau
								echo "<tr>";
								foreach($value as $cle=>$valeur) {
									echo "<th>".$cle."</th>";
								}
								echo "</tr>"; 
								$premier=false;
							}
							echo "<tr>";
							foreach($value as $cle=>$value) {
								// Boucle sur les valeurs
								echo "<td>".$value."</td>";
							}
							echo "<tr>";
						}
						echo "</table>";
						
					?>
				</div>
				
				<div class="col-xs-12">
					<h1>Liste des clients d'une catégorie (2)</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/clients/2";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);
						
						$premier=true;
						echo "<h2>http_Status=".$status."</h2><br>";
						echo "<table class='table table-striped table-bordered'>";
						foreach($resultat as $cle=>$value) {
							if ($premier) {
								// Première ligne, Affichage des entetes récupérées dans l'entete du tableau
								echo "<tr>";
								foreach($value as $cle=>$valeur) {
									echo "<th>".$cle."</th>";
								}
								echo "</tr>"; 
								$premier=false;
							}
							echo "<tr>";
							foreach($value as $cle=>$value) {
								// Boucle sur les valeurs
								echo "<td>".$value."</td>";
							}
							echo "<tr>";
						}
						echo "</table>";
						
					?>
				</div>
				
				<div class="col-xs-12">
					<h1>Client (1)</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/client/1";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);
						echo "<h2>http_Status=".$status."</h2><br>";
						echo "<ul>";
						foreach($resultat as $item) {
							foreach($item as $cle=>$value) {
								echo "<li>".$cle." : ".$value."</li>";
							}
						}
						echo "</ul>";
						
					?>
				</div>

				<div class="col-xs-12">
					<h1>Client (123 qui n'existe pas)</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/client/123";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);

						echo "<h2>http_Status=".$status."</h2><br>";
						if (!empty($resultat)) {
							echo "<ul>";
							foreach($resultat[0] as $cle=>$value) {
								echo "<li>".$cle." : ".$value."</li>";
							}
							echo "</ul>";
						}
						
					?>
				</div>
				
				<div class="col-xs-12">
					<h1>Création d'un type de client "Créé par l'API"</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/typeClient";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						
						$monType=[];
						$monType["TYPE_CLIENT_DESIGNATION"]="Créé par l'API";
						$donnees=json_encode($monType);
						
						$resultat=appelAPI($apiUrl, $apiKey, $status, "POST", $donnees);
						echo "<h2>http_Status=".$status."</h2><br>";
						print_r($resultat);
						
						// Recherche liste des Types Clients
						$apiUrl=$urlDeLAPI."/typesClients";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);
						$resultat=trisAlpha($resultat);
						echo "<h2>http_Status=".$status."</h2><br>";
						echo "<ul>";
						foreach($resultat as $value) {
							echo "<li>".$value['TYPE_CLIENT']." ".$value['TYPE_CLIENT_DESIGNATION']."</li>";
						}
						echo "</ul>";
						
					?>
				</div>
				<div class="col-xs-12">
					<h1>Création d'un client "Créé par l'API"</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/client";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						
						$monClient=[];
						$monClient["CODE_CLIENT"]="CAPI" ;
						$monClient["NOM_MAGASIN"]="Créé par l'API" ;
						$monClient["RESPONSABLE"]="M. Pomme d'API" ;
						$monClient["ADRESSE_1"]="Adresse 1" ;
						$monClient["ADRESSE_2"]="Adresse 2" ;
						$monClient["CODE_POSTAL"]="12000" ;
						$monClient["VILLE"]="Rodez" ;
						$monClient["TELEPHONE"]="0565656565" ;
						$monClient["EMAIL"]="api@api.fr" ;
						$monClient["TYPE_CLIENT"]="4";
						
						$donnees=json_encode($monClient);
						
						$resultat=appelAPI($apiUrl, $apiKey, $status, "POST", $donnees);
						echo "<h2>http_Status=".$status."</h2><br>";
						print_r($resultat);
						
						$apiUrl=$urlDeLAPI."/clients";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey,$status);
						echo "<h2>http_Status=".$status."</h2><br>";
						
						$premier=true;
						echo "<table class='table table-striped table-bordered'>";
						foreach($resultat as $cle=>$value) {
							if ($premier) {
								// Première ligne, Affichage des entetes récupérées dans l'entete du tableau
								echo "<tr>";
								foreach($value as $cle=>$valeur) {
									echo "<th>".$cle."</th>";
								}
								echo "</tr>"; 
								$premier=false;
							}
							echo "<tr>";
							foreach($value as $cle=>$value) {
								// Boucle sur les valeurs
								echo "<td>".$value."</td>";
							}
							echo "<tr>";
						}
						echo "</table>";
						
		
					?>
				</div>
				<div class="col-xs-12">
					<h1>Catalogue</h1>
					<?php 
						
						$apiUrl=$urlDeLAPI."/catalogue";
						echo "<h2>".$apiUrl."</h2>";
						$status="" ;
						$resultat=appelAPI($apiUrl, $apiKey, $status);
						echo "<h2>http_Status=".$status."</h2><br>";
						echo "<ul>";
						foreach($resultat as $cle=>$value) {
							echo "<li>".$cle."</li>";
							echo "<ul>";
							foreach($value as $url) {
								echo "<li>".$url."</li>";
							}
							echo "</ul>";
						}
						echo "</ul>";
						
					?>
				</div>
			</div>
		</div>
		<br><br>
	</body>
</html>