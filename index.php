<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<title>ConverttoPOSTGRESQL</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#convert").submit(function(){
					$("#erreur").html('');
					if ( jQuery.trim($("#filemysql").val()).length==0 ) {
						$("#erreur").append('fichier manquant');
						$("#erreur").fadeIn();
						return false;
					}
					else
					{
						$taille = $("#filemysql").val().length;
						$extension = $("#filemysql").val().substring($taille-3, $taille);
						if (($extension == "sql") || ($extension == "txt")) {
							return true;
						}
						else
						{
							$("#erreur").append('extension non valide');
							$("#erreur").fadeIn();
							return false;
						}
					}
				});
			});
		</script>
		
	</head>
	<body>
	    <div id="outer_content">
			<div id="inner_content">
				<table border="0" cellspacing="1" cellpadding="1">
					<?php
						require_once "indexControlleur.php";
						$controlleur = new indexControlleur();
						$controlleur->convertirfichierAction();
					?>
                    <tr>
                        <td><h1>&Eacute;tape 1</h1></td>
                        <td><h1>&Eacute;tape 2</h1></td>
                    </tr>
                    <tr>
                        <td>Script MYSQL à convertir :</td>
                        <td>Création du fichier :</td>
                    </tr>
                    <tr>
					    <form name="convert" id="convert" method="post" action="" enctype="multipart/form-data">
							<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
							<td><input type="file" name="fileMYSQL" id="filemysql" size="25" /></td>
							<td><input type="submit" name="btnSubmit" value="Convertir !" id="btnSubmit" />
						</form>
                    </td>
                </table>
			</div>
			<div id="erreur"></div>
		</div>
	</body>
</html>