<?php
/*
 * Define actions for converting mysql file
 *
 * @file    indexControlleur.php
 * @author  Yohann REVERDY
 */

class indexControlleur
{
	private $nomTable;
	private $nomSequence;
	
	public function convertirfichierAction()
	{
		if ($_FILES)
		{
			$erreur = '';
			$uploads_dir = dirname(__FILE__).'/uploads';
			mkdir($uploads_dir, 0777);
			
			if ($_FILES['fileMYSQL']['error'] == UPLOAD_ERR_NO_FILE){
				$erreur = 'fichier manquant';
			}
			
			if (($_FILES['fileMYSQL']['error'] == UPLOAD_ERR_INI_SIZE) || ($_FILES['fileMYSQL']['error'] == UPLOAD_ERR_FORM_SIZE)) {
				$erreur = 'dépassement taille de fichier';
			}
			
			//check extension txt and sql
			$extensions_valides = array( 'txt' , 'sql');
			$extension_upload = strtolower(  substr(  strrchr($_FILES['fileMYSQL']['name'], '.')  ,1)  );
			if (!in_array($extension_upload,$extensions_valides) ){
				$erreur = 'extension non valide';
			}
			
			if (strlen($erreur) > 0){
				echo $erreur;
				exit;
			}
					
			$contenu = $this->traiterfichier();
			
			$tmp_name = $_FILES['fileMYSQL']['tmp_name'];
			$name = $_FILES['fileMYSQL']['name'];
			$upload_file = $uploads_dir.'/'.$name;
			move_uploaded_file($tmp_name, $upload_file);
			
			//download file
			if (file_exists($upload_file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($upload_file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($upload_file));
				ob_clean();
				flush();
				readfile($upload_file);
			}
			else{
				echo 'fin';
				exit;
			}
				
		}
	}
	
	/** 
	 * Deal with the mysql text file sent by the user
	 * @return $contenuFichier string
	 */
	private function traiterFichier()
	{
		$fichierMysql = $_FILES['fileMYSQL']['tmp_name'];
		$contenuFichier = '';
		$fp_mysql = fopen($fichierMysql, "rb");
		$commentaire = false;
		$nomTable='';
		$commandeDefault='';
		
		while (!feof($fp_mysql))
		{
			$ligne = fgets( $fp_mysql , 1024 );
			$contenuFichier = $contenuFichier.$this->nouvelleLigne($ligne);
		}
		fclose($fp_mysql);
		return $contenuFichier;
		
	}
	
	/** 
	 * Check a mysql line and convert it if necessary
	 * @param $ligne string
	 * @return $ligne string
	 */
	private function nouvelleLigne($ligne)
	{
		$commentaire = '';
	
		if (substr_count($ligne, '/*'))
		{
			$commentaire = true;
		}
					
		$ligne = str_replace('`', '', $ligne);
		$ligne = preg_replace('/\([^)]*\)/', '', $ligne);
					
		//delete ENGINE=InnoDB and ENGINE=MyISAM
		$ligne = str_replace('ENGINE=InnoDB', '', $ligne);
		$ligne = str_replace('ENGINE=MyISAM', '', $ligne);
		//replace dedicated types
		$ligne = str_replace('tinyint', 'smallint', $ligne);
		$ligne = str_replace('datetime', 'timestamp', $ligne);
					
		//delete AUTO_INCREMENT and AUTO_INCREMENT=1 and use a sequence
		$posCreateTable = stripos($ligne, 'CREATE TABLE');
					
		if ($posCreateTable!== false)
		{
			$posFin = stripos($ligne, '(');
			$this->nomTable = substr($ligne, 13, strlen($ligne)-17);
						
			//todo : create sequence when it is necessary
			$this->nomSequence = $this->nomTable.'_id_seq';
			$ligne = 'CREATE SEQUENCE '.$this->nomSequence.';'."\n"."\n".$ligne;
		}
					
		$posAutoIncrement = stripos($ligne, 'AUTO_INCREMENT,');
					
		if ($posAutoIncrement!==false)
		{
			$commandeDefault = "DEFAULT nextval('".$this->nomSequence."'::regclass),";
			$ligne = str_replace("AUTO_INCREMENT,", $commandeDefault, $ligne);
		}
								
		$ligne = str_replace(' AUTO_INCREMENT=1', '', $ligne);
					
		//todo : define value other than 1
		//example : perform setval('personne_id_seq', coalesce(current_seq_value,1));
										
		if ($commentaire)
		{
			if (substr_count($ligne, '*/'))
			{
				$commentaire = false;
			}
		}
		
		return $ligne;
	}

}