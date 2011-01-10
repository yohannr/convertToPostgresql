<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<title>ConverttoPOSTGRESQL</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
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
                        <td><h1>&Eacute;tape 3</h1></td>
                    </tr>
                    <tr>
                        <td>Script MYSQL à convertir :</td>
                        <td>Fichier de destination :</td>
                        <td>Création du fichier :</td>
                    </tr>
                    <tr>
					    <form name="convert" method="post" action="" enctype="multipart/form-data">
                        <td><input type="file" name="fileMYSQL" id="filemysql" size="25" /></td>
						<td><input type="text" name="filePGSQL" id="filepostgresql" size="30" value="C:\extract.sql" /></td> 
                        <td><input type="submit" name="btnSubmit" value="Convertir !" id="btnSubmit" />	
                    </td>
					
                </table>

			</div>
		</div>
	</body>
</html>