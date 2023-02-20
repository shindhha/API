<?php
	// Données
		
	function getPDO(){
		// Retourne un objet connexion à la BD
		$host='localhost';	// Serveur de BD
		$db='mezabi3';		// Nom de la BD
		$user='root';		// User 
		$pass='root';		// Mot de passe
		$charset='utf8mb4';	// charset utilisé
		
		// Constitution variable DSN
		$dsn="mysql:host=$host;dbname=$db;charset=$charset";
		
		// Réglage des options
		$options=[																				 
			PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES=>false];
		
		try{	// Bloc try bd injoignable ou si erreur SQL
			$pdo=new PDO($dsn,$user,$pass,$options);
			return $pdo ;			
		} catch(PDOException $e){
			//Il y a eu une erreur de connexion
			$infos['Statut']="KO";
			$infos['message']="Problème connexion base de données";
			sendJSON($infos, 500) ;
			die();
		}
	}
	
	function getTypesClients() {
		// Retourne la liste des catégories des clients
		try {
			$pdo=getPDO();
			$maRequete='SELECT CODE_TYPE AS TYPE_CLIENT, DESIGNATION AS TYPE_CLIENT_DESIGNATION FROM c_types ORDER BY CODE_TYPE ' ; 
			
			$stmt = $pdo->prepare($maRequete);										// Préparation de la requête
			$stmt->execute();	
				
			$clients=$stmt ->fetchALL();
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;

			sendJSON($clients, 200) ;
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
	function getClients() {
		// Retourne la liste des clients
		try {
			$pdo=getPDO();
			$maRequete='SELECT c.ID_CLIENT, c.CODE_CLIENT,  c.NOM_MAGASIN, c.RESPONSABLE, c.ADRESSE_1, c.ADRESSE_2, c.CODE_POSTAL, c.VILLE, c.TELEPHONE, c.EMAIL, t.CODE_TYPE AS TYPE_CLIENT, t.DESIGNATION AS TYPE_CLIENT_DESIGNATION  
			FROM clients c join c_types t on c.TYPE_CLIENT = t.CODE_TYPE ORDER BY c.CODE_CLIENT ' ; 
			
			$stmt = $pdo->prepare($maRequete);										// Préparation de la requête
			$stmt->execute();	
				
			$clients=$stmt ->fetchALL();
			$nb = $stmt->rowCount();
			
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
	
			if ($nb!=0) {
				sendJSON($clients, 200) ;
			} else {
				sendJSON($clients, 404) ;
			}
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
	function getClientsByCategorie($categorie) {
		//liste des clients de la catégorie $categorie;
		try {
			$pdo=getPDO();
			$maRequete='SELECT c.ID_CLIENT, c.CODE_CLIENT,  c.NOM_MAGASIN, c.RESPONSABLE, c.ADRESSE_1, c.ADRESSE_2, c.CODE_POSTAL, c.VILLE, c.TELEPHONE, c.EMAIL, t.CODE_TYPE AS TYPE_CLIENT, t.DESIGNATION AS TYPE_CLIENT_DESIGNATION  
			FROM clients c join c_types t on c.TYPE_CLIENT = t.CODE_TYPE where t.CODE_TYPE = :categorie
            ORDER BY c.CODE_CLIENT ' ; 
			$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
			$stmt->bindParam("categorie", $categorie);				// Envoi du paramètre 1
			
			$stmt->execute();	
			$nb = $stmt->rowCount();
			
			$clients=$stmt ->fetchALL();
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
			if ($nb!=0) {
				sendJSON($clients, 200) ;
			} else {
				sendJSON($clients, 404) ;
			}

		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
	function getClientById($idClient) {
		//echo "informations d'un client ".$idClient;
		try {
			$pdo=getPDO();
			$maRequete='SELECT c.ID_CLIENT, c.CODE_CLIENT,  c.NOM_MAGASIN, c.RESPONSABLE, c.ADRESSE_1, c.ADRESSE_2, c.CODE_POSTAL, c.VILLE, c.TELEPHONE, c.EMAIL, t.CODE_TYPE AS TYPE_CLIENT, t.DESIGNATION AS TYPE_CLIENT_DESIGNATION  
			FROM clients c join c_types t on c.TYPE_CLIENT = t.CODE_TYPE where c.ID_CLIENT = :idClient
            ORDER BY c.CODE_CLIENT ' ; 
			$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
			$stmt->bindParam("idClient", $idClient);				// Envoi du paramètre 1
			
			$stmt->execute();	
			$nb = $stmt->rowCount();
			
			$clients=$stmt ->fetchALL();
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
			
			if ($nb!=0) {
				sendJSON($clients, 200) ;
			} else {
				sendJSON($clients, 404) ;
			}
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
	function ajoutClient($donneesJson) {
		if(!empty($donneesJson['CODE_CLIENT'])
			&& !empty($donneesJson['CODE_CLIENT']) 
			&& !empty($donneesJson['NOM_MAGASIN'])
			&& !empty($donneesJson['RESPONSABLE'])
			&& !empty($donneesJson['ADRESSE_1'])
			&& !empty($donneesJson['ADRESSE_2'])
			&& !empty($donneesJson['CODE_POSTAL'])
			&& !empty($donneesJson['VILLE'])
			&& !empty($donneesJson['TELEPHONE'])
			&& !empty($donneesJson['EMAIL'])
			&& !empty($donneesJson['TYPE_CLIENT'])
		  ){
			  // Données remplies, on insère dans la table client
			try {
				$pdo=getPDO();
				$maRequete='INSERT INTO clients(CODE_CLIENT, NOM_MAGASIN, ADRESSE_1, ADRESSE_2, CODE_POSTAL, VILLE, RESPONSABLE, TELEPHONE, EMAIL, TYPE_CLIENT) VALUES (:CODE_CLIENT, :NOM_MAGASIN, :ADRESSE_1, :ADRESSE_2, :CODE_POSTAL, :VILLE, :RESPONSABLE, :TELEPHONE, :EMAIL, :TYPE_CLIENT)';
				$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
				$stmt->bindParam("CODE_CLIENT", $donneesJson['CODE_CLIENT']);				
				$stmt->bindParam("NOM_MAGASIN", $donneesJson['NOM_MAGASIN']);
				$stmt->bindParam("ADRESSE_1", $donneesJson['ADRESSE_1']);
				$stmt->bindParam("ADRESSE_2", $donneesJson['ADRESSE_2']);
				$stmt->bindParam("CODE_POSTAL", $donneesJson['CODE_POSTAL']);
				$stmt->bindParam("VILLE", $donneesJson['VILLE']);
				$stmt->bindParam("RESPONSABLE", $donneesJson['RESPONSABLE']);
				$stmt->bindParam("TELEPHONE", $donneesJson['TELEPHONE']);
				$stmt->bindParam("EMAIL", $donneesJson['EMAIL']);
				$stmt->bindParam("TYPE_CLIENT", $donneesJson['TYPE_CLIENT']);
				$stmt->execute();	
				
				$IdInsere=$pdo->lastInsertId() ;
					
				$stmt=null;
				$pdo=null;
				
				// Retour des informations au client (statut + id créé)
				$infos['Statut']="OK";
				$infos['ID']=$IdInsere;

				sendJSON($infos, 201) ;
			} catch(PDOException $e){
				// Retour des informations au client 
				$infos['Statut']="KO";
				$infos['message']=$e->getMessage();

				sendJSON($infos, 500) ;
			}
		} else {
			// Données manquantes, Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']="Données incomplètes";
			sendJSON($infos, 400) ;
		}
	}
	
	function ajoutTypeClient($donneesJson) {
		// var_dump($donneesJson);
		if(!empty($donneesJson['TYPE_CLIENT_DESIGNATION'])){
			  // Données remplies, on insère dans la table types clients
			try {
				$pdo=getPDO();
				// var_dump($donneesJson);
				$maRequete='INSERT INTO c_types(DESIGNATION) VALUES (:DESIGNATION)';
				$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
				$stmt->bindParam("DESIGNATION", $donneesJson['TYPE_CLIENT_DESIGNATION']);				
				$stmt->execute();	
				
				$IdInsere=$pdo->lastInsertId() ;
					
				$stmt=null;
				$pdo=null;
				
				// Retour des informations au client (statut + id créé)
				$infos['Statut']="OK";
				$infos['ID']=$IdInsere;

				sendJSON($infos, 201) ;
			} catch(PDOException $e){
				// Retour des informations au client 
				$infos['Statut']="KO";
				$infos['message']=$e->getMessage();
				sendJSON($infos, 500) ;
			}
		}else {
			// Données manquantes, Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']="Données incomplètes";
			sendJSON($infos, 400) ;
		}
	}
	
	function supprimeClient($idClient) {
		try {
			$pdo=getPDO();
			$maRequete='delete from clients where ID_CLIENT = :IDClient';  

			$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
			$stmt->bindParam("IDClient", $idClient);				// Envoi du paramètre 1
			
			$stmt->execute();	
			$deleted = $stmt->rowCount();	

			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
			if ($deleted !=0) {
				$infos['Statut']="OK";
				$infos['message']="Client supprimé";
				sendJSON($infos, 200) ;
			} else {
				$infos['Statut']="KO";
				$infos['message']="ID inexistant";
				sendJSON($infos, 400) ;
			}
			
			
		} catch(PDOException $e){
			// Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();

			sendJSON($infos, 500) ;
		}
	}
	
	function supprimeTypeClient($idType) {
		try {
			$pdo=getPDO();
			$maRequete='delete from c_types where CODE_TYPE = :idType';  

			$stmt = $pdo->prepare($maRequete);					// Préparation de la requête
			$stmt->bindParam("idType", $idType);				// Envoi du paramètre 1
			
			$stmt->execute();	
			$deleted = $stmt->rowCount();
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
			if ($deleted !=0) {
				$infos['Statut']="OK";
				$infos['message']="Type supprimé";
				sendJSON($infos, 200) ;
			} else {
				$infos['Statut']="KO";
				$infos['message']="ID inexistant";
				sendJSON($infos, 400) ;
			}
			
		} catch(PDOException $e){
			// Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
	function modificationClient($donneesJson, $idClient) {
		if(!empty($donneesJson['CODE_CLIENT']) 
			&& !empty($donneesJson['NOM_MAGASIN'])
			&& !empty($donneesJson['RESPONSABLE'])
			&& !empty($donneesJson['ADRESSE_1'])
			&& !empty($donneesJson['ADRESSE_2'])
			&& !empty($donneesJson['CODE_POSTAL'])
			&& !empty($donneesJson['VILLE'])
			&& !empty($donneesJson['TELEPHONE'])
			&& !empty($donneesJson['EMAIL'])
			&& !empty($donneesJson['TYPE_CLIENT'])
		  ){
			  // Données remplies, on modifie le client
			try {
				$pdo=getPDO();
				$maRequete='UPDATE clients SET CODE_CLIENT=:CODE_CLIENT,NOM_MAGASIN=:NOM_MAGASIN,ADRESSE_1=:ADRESSE_1,ADRESSE_2=:ADRESSE_2,CODE_POSTAL=:CODE_POSTAL,VILLE=:VILLE,RESPONSABLE=:RESPONSABLE,TELEPHONE=:TELEPHONE,EMAIL=:EMAIL,TYPE_CLIENT=:TYPE_CLIENT WHERE ID_CLIENT=:ID_CLIENT';
				$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
				$stmt->bindParam("ID_CLIENT", $idClient);
				$stmt->bindParam("CODE_CLIENT", $donneesJson['CODE_CLIENT']);				
				$stmt->bindParam("NOM_MAGASIN", $donneesJson['NOM_MAGASIN']);
				$stmt->bindParam("ADRESSE_1", $donneesJson['ADRESSE_1']);
				$stmt->bindParam("ADRESSE_2", $donneesJson['ADRESSE_2']);
				$stmt->bindParam("CODE_POSTAL", $donneesJson['CODE_POSTAL']);
				$stmt->bindParam("VILLE", $donneesJson['VILLE']);
				$stmt->bindParam("RESPONSABLE", $donneesJson['RESPONSABLE']);
				$stmt->bindParam("TELEPHONE", $donneesJson['TELEPHONE']);
				$stmt->bindParam("EMAIL", $donneesJson['EMAIL']);
				$stmt->bindParam("TYPE_CLIENT", $donneesJson['TYPE_CLIENT']);
				$stmt->execute();	
				$nb = $stmt->rowCount(); // nbre d'items modifiés
				
				$stmt=null;
				$pdo=null;
				
				// Retour des informations au client (statut)
				if ($nb==0) {
					// Erreur lors du update
					$infos['Statut']="KO";
					$infos['Message']="Erreur dans la mise à jour";
					sendJSON($infos, 404) ;
				} else {
					// Modification réalisée
					$infos['Statut']="OK";
					$infos['Message']="Modification effectuée";
					sendJSON($infos, 201) ;
				}

				sendJSON($infos, 201) ;
			} catch(PDOException $e){
				// Retour des informations au client 
				$infos['Statut']="KO";
				$infos['message']=$e->getMessage();
				sendJSON($infos, 500) ;
			}
		}else {
			// Données manquantes, Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']="Données incomplètes";
			sendJSON($infos, 400) ;
		}
	}
	function modificationTypeClient($donneesJson, $idType) {
		if(!empty($donneesJson['TYPE_CLIENT_DESIGNATION']) ){
			  // Données remplies, on modifie dans la table client
			try {
				$pdo=getPDO();
				$maRequete='UPDATE c_types SET DESIGNATION=:DESIGNATION WHERE CODE_TYPE=:CODE_TYPE';
				$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
				$stmt->bindParam("CODE_TYPE", $idType);
				$stmt->bindParam("DESIGNATION", $donneesJson['TYPE_CLIENT_DESIGNATION']);				
				$stmt->execute();	
				$nb = $stmt->rowCount(); // nbre d'items modifiés
				
				$stmt=null;
				$pdo=null;
				
				if ($nb==0) {
					// Erreur lors du update
					$infos['Statut']="KO";
					$infos['Message']="Erreur dans la mise à jour";
					sendJSON($infos, 404) ;
				} else {
					// Modification réalisée
					$infos['Statut']="OK";
					$infos['Message']="Modification effectuée";
					sendJSON($infos, 201) ;
				}

			} catch(PDOException $e){
				// Retour des informations au client 
				$infos['Statut']="KO";
				$infos['message']=$e->getMessage();
				sendJSON($infos, 503) ;
			}
		} else {
			// Données manquantes, Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']="Données incomplètes";
			sendJSON($infos, 400) ;
		}
	}
?>