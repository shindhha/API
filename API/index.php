<?php 
	// Routeur
	// Décomposition de l'URL via l'écriture de l'url grace au .htacess
		
	/*	
		-----------  GET ----------------------
		- index.php/typesClients  	-> Récup des types des clients
			devient : index.php?demande=typesClients  	(vue API) grace à une réécriture d'URL .HTaccess
			Exemple : index.php/typesClients
			
		- index.php/clients  		-> récup de tous les clients devient : 
			devient index.php?demande=clients  	(vue API) grace à une réécriture d'URL .HTaccess
			Exemple : index.php/clients
			
		- index.php/clients/:type  -> récup de tous les clients du type client
			devient index.php?demande=clients/typeClient  	(vue API) grace à une réécriture d'URL .HTaccess
			Exemple : index.php/clients/1
			
		- index.php/client/:id  		-> récup du client id X
			devient index.php?demande=client/X  	(vue API) grace à une réécriture d'URL .HTaccess
			Exemple : index.php/client/1
			
		- index.php/login/monLogin/monPassword -> Récupération d'une clé API

		- index.php/catalogue			-> Récup du catalogue des méthodes proposées par l'API
			devient index.php?demande=catalogue

	*/
	
	/*
		-----------  POST ----------------------
		- index.php/client  -> Création d'un client (envoi des données en JSON via postman ou curl)
		Exemple JSON : 
		{
			"CODE_CLIENT": "CLIENT API",
			"NOM_MAGASIN": "MAGASIN RODEZ API",
			"RESPONSABLE": "M. Pierre Api",
			"ADRESSE_1": "33 rue de l'API",
			"ADRESSE_2": "Local API3",
			"CODE_POSTAL": "12000",
			"VILLE": "Rodez",
			"TELEPHONE": "0565656565",
			"EMAIL": "misterapi@api.com",
			"TYPE_CLIENT": 4
		}
		
		- index.php/typeClient  -> Création d'un type client (envoi des données en JSON via postman ou curl)
		Exemple JSON : 
		{
			"TYPE_CLIENT_DESIGNATION": "Nouveau type client"
		}
		
		-----------  DELETE ----------------------
		- index.php/client/:idClient  -> Suppression du client idClient
		Exemple : index.php/client/16
		
		- index.php/typeClient/:idTypeClient  -> Suppression du type client  idTypeClient
		Exemple : index.php/typeClient/9
		
		-----------  PUT ----------------------
		- index.php/client/:idClient  -> Modification du client No IdClient
		Exemple JSON : 
		{
			"CODE_CLIENT": "CLIENT API",
			"NOM_MAGASIN": "MAGASIN RODEZ API",
			"RESPONSABLE": "M. Pierre Api",
			"ADRESSE_1": "33 rue de l'API",
			"ADRESSE_2": "Local API3",
			"CODE_POSTAL": "12000",
			"VILLE": "Rodez",
			"TELEPHONE": "0565656565",
			"EMAIL": "misterapi@api.com",
			"TYPE_CLIENT": 4
		}
		
		- index.php/typeClient/:idTypeClient  -> Modification du type de client
		Exemple JSON : 
			{
				"TYPE_CLIENT_DESIGNATION": "Centre Ville modifié"
			}
	*/
	
	// Récupération URL si besoin par exemple pour les chemins vers des images. Non utilisé dans cet exemple
	//define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http")."://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));

	require_once("json.php");
	require_once("donnees.php");
	require_once("authentification.php");

	//
	$request_method = $_SERVER["REQUEST_METHOD"];  // GET / POST / DELETE / PUT
	switch($_SERVER["REQUEST_METHOD"]) {
		case "GET" :
			if (!empty($_GET['demande'])) {
				// $encode=urlencode($_GET['demande']);
				// $decode=urldecode($encode);
				
				// décomposition URL par les / et  FILTER_SANITIZE_URL-> Supprime les caractères illégaux des URL
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				
				switch($url[0]) {
					case 'login' :
						// Retournera une clé API si le login et password sont OK
						// La clé API sera utilisée pour les prochaines requetes.
						if (isset($url[1])) {$login=$url[1];} else {$login="";}
						if (isset($url[2])) {$password=$url[2];} else {$password="";}
						verifLoginPassword($login,$password);  // retourne l'apiKey si les logins / pwd sont ok
					break;
					case 'typesClients' : 
						// Retourne les types de clients
						authentification(); // Test si on est bien authenfifié pour l'API
						getTypesClients();
						break ;
					case 'clients' : 
						// Retourne les clients
						if (empty($url[1])) { // Attention si valeur 0 = false ->  vrai
							// Retourne tous les clients
							authentification(); // Test si on est bien authenfifié pour l'API
							getClients();
						} else {
							// Retourne les clients d'une catégorie
							authentification(); // Test si on est bien authenfifié pour l'API
							getClientsByCategorie($url[1]);
						}
						break ;
					case 'client' : 
						// Retourne un client
						if (!empty($url[1])) {  // Attention si valeur 0 = false ->  vrai
							authentification(); // Test si on est bien authenfifié pour l'API
							getClientById($url[1]);
						} else {
							$infos['Statut']="KO";
							$infos['message']="Vous n'avez pas renseigné le No de client.";
							sendJSON($infos, 400) ;
						}
						break ;
					case "catalogue" :
						// authentification mise en commentaire car le catalogue est ouvert sans login password
						//authentification(); // Test si on est bien authenfifié pour l'API
						$donnees=[];
						$donnees['GET'][0]="/login/:monLogin/:monPassword -> Retourne une clé API si les logins et password sont OK";
						$donnees['GET'][1]="/typesClients -> Retourne les types des clients";
						$donnees['GET'][2]="/clients -> Retourne tous les clients";
						$donnees['GET'][3]="/clients/:categorie -> Retourne les clients de la catégorie";
						$donnees['GET'][4]="/client/:idClient -> Retourne le client";
						$donnees['GET'][5]="/catalogue-> Retourne le catalogue de l'API";
						$donnees['POST'][0]="/client -> Ajoute un client, id retourné par l'API";
						$donnees['POST'][0].='<br>{';
						$donnees['POST'][0].='<br> "CODE_CLIENT": "Valeur",';
						$donnees['POST'][0].='<br> "NOM_MAGASIN": "Valeur",';
						$donnees['POST'][0].='<br> "RESPONSABLE": "Valeur",';
						$donnees['POST'][0].='<br> "ADRESSE_1": "Valeur",';
						$donnees['POST'][0].='<br> "ADRESSE_2": "Valeur",';
						$donnees['POST'][0].='<br> "CODE_POSTAL": "Valeur",';
						$donnees['POST'][0].='<br> "VILLE": "Valeur",';
						$donnees['POST'][0].='<br> "TELEPHONE": "Valeur",';
						$donnees['POST'][0].='<br> "EMAIL": "Valeur",';
						$donnees['POST'][0].='<br> "TYPE_CLIENT": Valeur';
						$donnees['POST'][0].='<br>}';
						$donnees['POST'][1]="/typeClient -> Ajoute un type de client, id retourné par l'API";
						$donnees['POST'][1].="<br>{";
						$donnees['POST'][1].='<br> "TYPE_CLIENT_DESIGNATION": "Valeur"';
						$donnees['POST'][1].='<br>}"';
						$donnees['DELETE'][0]="/typeClient/:typeClient -> Supprime un type de client";
						$donnees['DELETE'][1]="/client/:idClient -> Supprime un client";
						$donnees['PUT'][0]="/typeClient/:typeClient -> Modifie un type de client";
						$donnees['PUT'][1]="/client/:idClient -> Modifie un client";
						sendJSON($donnees, 200) ;
						break ;
				
					default : 
						$infos['Statut']="KO";
						$infos['message']=$url[0]." inexistant";
						sendJSON($infos, 404) ;
				}
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break ;
		case "POST" :
			if (!empty($_GET['demande'])) {
				// Ajout d'un client / type de client
				// Récupération des données envoyées
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				switch($url[0]) {
					case 'client' : 
						// Ajout d'un client
						authentification(); // Test si on est bien authenfifié pour l'API
						$donnees = json_decode(file_get_contents("php://input"),true);
						ajoutClient($donnees);
						break ;
					case 'typeClient' : 
						// Ajout d'un type de client
						authentification(); // Test si on est bien authenfifié pour l'API
						$donnees = json_decode(file_get_contents("php://input"),true);
						ajoutTypeClient($donnees);
						break ;
					default : 
						$infos['Statut']="KO";
						$infos['message']="'".$url[0]."' inexistant";
						sendJSON($infos, 404) ;
				}	
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break;
		case "DELETE" :	
			if (!empty($_GET['demande'])) {
				// Suppression d'un client / type de client
				// Récupération des données envoyées
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				switch($url[0]) {
					case 'client' : 
						// Suppression d'un client
						if (!empty($url[1])) {  // Attention si valeur 0 = false ->  vrai
							authentification(); // Test si on est bien authenfifié pour l'API
							supprimeClient($url[1]);
						} else {
							$infos['Statut']="KO";
							$infos['message']="Vous n'avez pas renseigné le No de client.";
							sendJSON($infos, 400) ;
						}
						
						break ;
					case 'typeClient' : 
						// Suppression d'un type client
						if (!empty($url[1])) {  // Attention si valeur 0 = false ->  vrai
							authentification(); // Test si on est bien authenfifié pour l'API
							supprimeTypeClient($url[1]);
						} else {
							$infos['Statut']="KO";
							$infos['message']="Vous n'avez pas renseigné le Type.";
							sendJSON($infos, 400) ;
						}
						break ;
					default : 
						$infos['Statut']="KO";
						$infos['message']=$url[0]." inexistant";
						sendJSON($infos, 404) ;
				}	
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break;
		case "PUT" :
			if (!empty($_GET['demande'])) {
				// Modification d'un client / type de client
				// Récupération des données envoyées
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				switch($url[0]) {
					case 'client' : 
						// Modification d'un client
						if (!empty($url[1])) {  // Attention si valeur 0 = false ->  vrai
							authentification(); // Test si on est bien authenfifié pour l'API
							$donnees = json_decode(file_get_contents("php://input"),true);
							modificationClient($donnees,$url[1] );
						} else {
							$infos['Statut']="KO";
							$infos['message']="Vous n'avez pas renseigné le No de client.";
							sendJSON($infos, 400) ;
						}
						
						break ;
					case 'typeClient' : 
						// Modification d'un type de client
						if (!empty($url[1])) {  // Attention si valeur 0 = false ->  vrai
							authentification(); // Test si on est bien authenfifié pour l'API
							$donnees = json_decode(file_get_contents("php://input"),true);
							modificationTypeClient($donnees,$url[1] );
						} else {
							$infos['Statut']="KO";
							$infos['message']="Vous n'avez pas renseigné le Type.";
							sendJSON($infos, 400) ;
						}	
						break ;
					default : 
						$infos['Statut']="KO";
						$infos['message']=$url[0]." inexistant";
						sendJSON($infos, 404) ;
				}	
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break;
		
		default :
			$infos['Statut']="KO";
			$infos['message']="URL non valide";
			sendJSON($infos, 404) ;
	}
	
?>