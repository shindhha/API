<?php 
	// Authentification /////////////////////////////////////////////////////////////////////
	
	function authentification() {
		// fonction permettant de tester si la clé API est valide
		if (isset($_SERVER["HTTP_APIKEYDEMONAPPLI"])) {
			$cleAPI=$_SERVER["HTTP_APIKEYDEMONAPPLI"];
			// Test de la clé API fait en dur pour l'exemple mais devrait être fait avec la BD
			if ($cleAPI!="JAQ2345RTtve") {
				$infos['Statut']="KO";
				$infos['message']="APIKEY invalide.";
				sendJSON($infos, 403) ;
				die();
			}
		}else {
			// Pas de clé API envoyée, pas d'accès à l'Api
			$infos['Statut']="KO";
			$infos['message']="Authentification necessaire par APIKEY.";
			sendJSON($infos, 401) ;
			die();
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	function verifLoginPassword($login, $password) {
		// fonction qui vérifie si le login et le password sont ok.
		// Si ok, on génère une clé API qui sera normalement stockée dans la BD
		// Et on la retourne au client
		if ($login=="G40" and $password=="W38T4") {
			// Login et mot de passe correct, 
			// Genération de la clé, stockage en BD (non fait dans cet exemple)
			// Envoi de la clé au client.
			$infos['APIKEYDEMONAPPLI']="JAQ2345RTtve";
			sendJSON($infos, 200) ;
		} else {
			// Login incorrect
			$infos['Statut']="KO";
			$infos['message']="Logins incorrects.";
			sendJSON($infos, 401) ;
			die();
		}
	}
	?>