<?php
/*
 * Define actions for converting mysql file
 *
 * @file    indexControlleur.php
 * @date    08/01/2011
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
			$fichierPgsql = 'file:///'.$_POST['filePGSQL'];
						
			if(empty($_POST['filePGSQL']))
			{
				$fichierPgsql = 'file:///C:\extract.sql';
			}
			
			$contenu = $this->traiterfichier();
					
			$fp_pgsql = fopen($fichierPgsql, "wb");
			fwrite($fp_pgsql, $contenu);
			fclose($fp_pgsql);
				
		}
	}
	
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
				
				
				
				
				
				
				
				
				

                    