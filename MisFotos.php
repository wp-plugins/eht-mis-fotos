<?php
/*
Plugin Name: EHT Mis Fotos
Plugin URI: http://emiliogonzalez.sytes.net/index.php/2007/07/06/misfotos-plugin-para-wordpress/
Description: Plugin para incrustar galerias de imagenes en Wordpress de manera autom&aacute;tica.
Author: Emilio Gonz&aacute;lez Monta&ntilde;a
Version: 0.4
Author URI: http://emiliogonzalez.sytes.net/

History:	0.1		Primera versión.
			0.3		El tamaño de las miniautas, el tamaño normal de las fotos y el ancho en miniautras es configurable. Mejoras variadas en la presentación.
			0.4		Añadido panel de configuración de opciones.

Sintaxis del plugin:

[misfotos fotos={1} miniaturas={2} ruta={3}]

Donde:
   {1} carpeta con las fotos.
   {2} carpeta con las miniaturas (esta carpeta debe existir).
   {3} si|no para pintar o no los enlaces con la ruta actual.

Ejemplo:

[misfotos fotos=/galeria/fotos miniaturas=/galeria/miniaturas ruta=si]

*/

add_filter ('the_content', 'EHTMisFotosFiltroMisFotos');
add_action ("admin_menu", "EHTMisFotosAdminAddPages");

define ("EHT_MIS_FOTOS_OPCION_MINIATURA", "eht-mis-fotos-opcion-miniatura");
define ("EHT_MIS_FOTOS_OPCION_NORMAL", "eht-mis-fotos-opcion-normal");
define ("EHT_MIS_FOTOS_OPCION_ANCHO", "eht-mis-fotos-opcion-ancho");
define ("EHT_MIS_FOTOS_CAMPO_ACCION", "eht-mis-fotos-campo-accion");
define ("EHT_MIS_FOTOS_ACCION_ACTUALIZAR", "actualizar");
define ("EHT_MIS_FOTOS_DEFAULT_MINIATURA", 170);
define ("EHT_MIS_FOTOS_DEFAULT_NORMAL", 700);
define ("EHT_MIS_FOTOS_DEFAULT_ANCHO", 4);
define ("EHT_MIS_FOTOS_MINIMO_MINIATURA", 16);
define ("EHT_MIS_FOTOS_MINIMO_NORMAL", 128);
define ("EHT_MIS_FOTOS_MINIMO_ANCHO", 1);
define ("EHT_MIS_FOTOS_MAXIMO_MINIATURA", 512);
define ("EHT_MIS_FOTOS_MAXIMO_NORMAL", 2048);
define ("EHT_MIS_FOTOS_MAXIMO_ANCHO", 20);

require_once ("clases/Galeria.php");

function EHTMisFotosFiltroMisFotos ($contenido)
{
	$busqueda = "/\[misfotos\s*fotos\s*=\s*([^\]]+)\s*miniaturas\s*=\s*([^\]]+)\s*ruta\s*=\s*([^\]]+)\s*\]/i";

	preg_match_all ($busqueda, $contenido, $aciertos);

	if (is_array ($aciertos[1]))
	{
		for ($m = 0; $m < count ($aciertos[0]); $m++)
		{
			if ($m == 0)
			{
				$miniatura = get_option (EHT_MIS_FOTOS_OPCION_MINIATURA);
				$normal = get_option (EHT_MIS_FOTOS_OPCION_NORMAL);
				$ancho = get_option (EHT_MIS_FOTOS_OPCION_ANCHO);
				if ($miniatura == "")
				{
					$miniatura = EHT_MIS_FOTOS_DEFAULT_MINIATURA;
				}
				if ($normal == "")
				{
					$normal = EHT_MIS_FOTOS_DEFAULT_NORMAL;
				}
				if ($ancho == "")
				{
					$ancho = EHT_MIS_FOTOS_DEFAULT_ANCHO;
				}
		
				$fotos = trim ($aciertos[1][$m]);
				$miniaturas = trim ($aciertos[2][$m]);
				$mostrarRuta = (strcasecmp (trim ($aciertos[3][$m]), "si") == 0);
				$galeria = new Galeria ($fotos,
										$miniaturas, 
										$miniatura, 
										$normal);
				$textoGaleria = $galeria->Pintar ($mostrarRuta, $ancho);
                		$textoGaleria .= "<p align=\"center\">Plugin <a href=\"http://emiliogonzalez.sytes.net/index.php/2007/07/06/misfotos-plugin-para-wordpress/\" target=\"_blank\">EHT Mis Fotos</a> - Creado por <a href=\"http://emiliogonzalez.sytes.net\" target=\"_blank\">Emilio Gonz&aacute;lez Monta&ntilde;a</a></p>";

				$contenido = str_replace ($aciertos[0][$m], $textoGaleria, $contenido);
			}
			else
			{
				$contenido = str_replace ($aciertos[0][$m], "<b>[MisFotos ERROR: S&oacute;lo una galer&iacute;a por p&aacute;gina permitida]</b>", $contenido);
			}
		}
	}

	return ($contenido);
}

function EHTMisFotosAdminAddPages ()
{
	add_options_page ('EHT Mis Fotos', 'EHT Mis Fotos', 8, 'eht-mis-fotos-options', 'EHTMisFotosAdminOptions');
}

function EHTMisFotosAdminOptions ()
{
	$accion = $_POST[EHT_MIS_FOTOS_CAMPO_ACCION];
	if ($accion == EHT_MIS_FOTOS_ACCION_ACTUALIZAR)
	{
		$miniatura = $_POST[EHT_MIS_FOTOS_OPCION_MINIATURA];
		if ($miniatura < EHT_MIS_FOTOS_MINIMO_MINIATURA)
		{
			echo "<div class=\"error\">El tama&ntilde;o de miniatura $miniatura es inferior al m&iacute;nimo " . EHT_MIS_FOTOS_MINIMO_MINIATURA . ", se usar&aacute; el valor m&iacute;nimo.</div>\n";
			$miniatura = EHT_MIS_FOTOS_MINIMO_MINIATURA;
		}
		else if ($miniatura > EHT_MIS_FOTOS_MAXIMO_MINIATURA)
		{
			echo "<div class=\"error\">El tama&ntilde;o de miniatura $miniatura es superior al m&aacute;ximo " . EHT_MIS_FOTOS_MAXIMO_MINIATURA . ", se usar&aacute; el valor m&aacute;ximo.</div>\n";
			$miniatura = EHT_MIS_FOTOS_MAXIMO_MINIATURA;
		}
		$normal = $_POST[EHT_MIS_FOTOS_OPCION_NORMAL];
		if ($normal < EHT_MIS_FOTOS_MINIMO_NORMAL)
		{
			echo "<div class=\"error\">El tama&ntilde;o de foto normal $normal es inferior al m&iacute;nimo " . EHT_MIS_FOTOS_MINIMO_NORMAL . ", se usar&aacute; el valor m&iacute;nimo.</div>\n";
			$normal = EHT_MIS_FOTOS_MINIMO_NORMAL;
		}
		else if ($normal > EHT_MIS_FOTOS_MAXIMO_NORMAL)
		{
			echo "<div class=\"error\">El tama&ntilde;o de foto normal $normal es superior al m&aacute;ximo " . EHT_MIS_FOTOS_MAXIMO_NORMAL . ", se usar&aacute; el valor m&aacute;ximo.</div>\n";
			$normal = EHT_MIS_FOTOS_MAXIMO_NORMAL;
		}
		$ancho = $_POST[EHT_MIS_FOTOS_OPCION_ANCHO];
		if ($ancho < EHT_MIS_FOTOS_MINIMO_ANCHO)
		{
			echo "<div class=\"error\">El tama&ntilde;o de fotos a lo ancho $ancho es inferior al m&iacute;nimo " . EHT_MIS_FOTOS_MINIMO_ANCHO . ", se usar&aacute; el valor m&iacute;nimo.</div>\n";
			$ancho = EHT_MIS_FOTOS_MINIMO_ANCHO;
		}
		else if ($ancho > EHT_MIS_FOTOS_MAXIMO_ANCHO)
		{
			echo "<div class=\"error\">El tama&ntilde;o de fotos a lo ancho $ancho es superior al m&aacute;ximo " . EHT_MIS_FOTOS_MAXIMO_ANCHO . ", se usar&aacute; el valor m&aacute;ximo.</div>\n";
			$ancho = EHT_MIS_FOTOS_MAXIMO_ANCHO;
		}
	}
	else
	{
		$miniatura = get_option (EHT_MIS_FOTOS_OPCION_MINIATURA);
		$normal = get_option (EHT_MIS_FOTOS_OPCION_NORMAL);
		$ancho = get_option (EHT_MIS_FOTOS_OPCION_ANCHO);
	}

	if ($miniatura == "")
	{
		$miniatura = EHT_MIS_FOTOS_DEFAULT_MINIATURA;
		$accion = EHT_MIS_FOTOS_ACCION_ACTUALIZAR;
	}
	if ($normal == "")
	{
		$normal = EHT_MIS_FOTOS_DEFAULT_NORMAL;
		$accion = EHT_MIS_FOTOS_ACCION_ACTUALIZAR;
	}
	if ($ancho == "")
	{
		$ancho = EHT_MIS_FOTOS_DEFAULT_ANCHO;
		$accion = EHT_MIS_FOTOS_ACCION_ACTUALIZAR;
	}
	
	if ($accion == EHT_MIS_FOTOS_ACCION_ACTUALIZAR)
	{
        update_option (EHT_MIS_FOTOS_OPCION_MINIATURA, $miniatura);
        update_option (EHT_MIS_FOTOS_OPCION_NORMAL, $normal);
        update_option (EHT_MIS_FOTOS_OPCION_ANCHO, $ancho);
		echo "<div class=\"updated\">Las opciones han sido actualizadas.</div>\n";
	}

	echo "<div class=\"wrap\">\n";
	echo "<h2>EHT Mis Fotos</h2>\n";
	echo "<form method=\"post\" action=\"" . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . "\">\n";
	echo "<input type=\"hidden\" name=\"" . EHT_MIS_FOTOS_CAMPO_ACCION . "\" value=\"" . EHT_MIS_FOTOS_ACCION_ACTUALIZAR . "\">\n";
	echo "<p>Tama&ntilde;o de miniatura (en pixels) [" . EHT_MIS_FOTOS_MINIMO_MINIATURA . ", " . EHT_MIS_FOTOS_MAXIMO_MINIATURA . "]:<br>\n";
	echo "<input type=\"text\" name=\"" . EHT_MIS_FOTOS_OPCION_MINIATURA . "\" value=\"" . $miniatura . "\"></p>\n";
	echo "<p>Tama&ntilde;o de foto normal (en pixels) [" . EHT_MIS_FOTOS_MINIMO_NORMAL . ", " . EHT_MIS_FOTOS_MAXIMO_NORMAL . "]:<br>\n";
	echo "<input type=\"text\" name=\"" . EHT_MIS_FOTOS_OPCION_NORMAL . "\" value=\"" . $normal . "\"></p>\n";
	echo "<p>N&uacute;mero de miniaturas a lo ancho [" . EHT_MIS_FOTOS_MINIMO_ANCHO . ", " . EHT_MIS_FOTOS_MAXIMO_ANCHO . "]:<br>\n";
	echo "<input type=\"text\" name=\"" . EHT_MIS_FOTOS_OPCION_ANCHO . "\" value=\"" . $ancho . "\"></p>\n";
	echo "<p class=\"submit\">\n";
	echo "<input type=\"submit\" value=\"Actualizar opciones\">\n";
	echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";
	echo "<p align=\"center\">Plugin <a href=\"http://emiliogonzalez.sytes.net/index.php/2007/07/06/misfotos-plugin-para-wordpress/\" target=\"_blank\">EHT Mis Fotos</a> - Created by <a href=\"http://emiliogonzalez.sytes.net\" target=\"_blank\">Emilio Gonz&aacute;lez Monta&ntilde;a</a></p>\n";
}

?>