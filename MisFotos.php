<?php
/*
Plugin Name: EHT Mis Fotos
Plugin URI: http://emiliogonzalez.sytes.net/index.php/2007/07/06/misfotos-plugin-para-wordpress/
Description: Plugin para incrustar galerias de imagenes en Wordpress de manera autom&aacute;tica.
Author: <a href="mailto:egoeht69@hotmail.com">Emilio Gonz&aacute;lez Monta&ntilde;a</a>
Version: 0.2
Author URI: http://emiliogonzalez.sytes.net/
*/


define ("TABLA_PREFIJO", "mis_fotos_"); 
define ("CONFIGURACION_TAMANO_MINIATURA", "miniatura");
define ("CONFIGURACION_TAMANO_NORMAL", "normal");
define ("CONFIGURACION_ANCHO", "ancho");

define ("MIS_FOTOS_MINIATURA", 120);
define ("MIS_FOTOS_NORMAL", 420);
define ("MIS_FOTOS_ANCHO", 3);

define ("MOSTRAR_CONSULTAS", false);


$TABLA_CONFIGURACION = $table_prefix . TABLA_PREFIJO . "configuracion";
$TABLA_FOTOS = $table_prefix . TABLA_PREFIJO . "fotos";
$TABLA_COMENTARIOS = $table_prefix . TABLA_PREFIJO . "comentarios";


require_once ("clases/Galeria.php");


function ConsultaLeerConfiguracion ($nombre)
{
	global $table_prefi, $TABLA_CONFIGURACION;
	
	$sql = "SELECT valor FROM $TABLA_CONFIGURACION  WHERE nombre = '$nombre'";
	
	if (MOSTRAR_CONSULTAS)
	{
		echo "\"$sql\"<br>\n";	
	}
	
	return ($sql);
}

function LeerConfiguracion ($nombre)
{
	global $wpdb;
	
	$resultado = $wpdb->get_var (ConsultaLeerConfiguracion ($nombre));

	if (MOSTRAR_CONSULTAS)
	{
		echo "\"$resultado\"<br>\n";	
	}
	
	return ($resultado);
}

function ConsultaEscribirConfiguracion ($nombre, $valor, $crear = false)
{
	global $table_prefix, $TABLA_CONFIGURACION;
	
	if ($crear)
	{
		$sql = "INSERT INTO $TABLA_CONFIGURACION  (nombre, valor) VALUES ('$nombre', '$valor');";
	}
	else
	{
		$sql = "UPDATE $TABLA_CONFIGURACION SET valor = '$valor' WHERE nombre = '$nombre';";
	}

	if (MOSTRAR_CONSULTAS)
	{
		echo "\"$sql\"<br>\n";	
	}
	
	return ($sql);	
}

function EscribirConfiguracion ($nombre, $valor, $crear = false)
{
	global $wpdb;
	
	$resultado = $wpdb->query (ConsultaEscribirConfiguracion ($nombre, $valor, $crear));

	if (MOSTRAR_CONSULTAS)
	{
		echo "\"$resultado\"<br>\n";	
	}
	
	return ($resultado);
}

function Iniciar ()
{
	global $wpdb, $TABLA_CONFIGURACION, $TABLA_FOTOS, $TABLA_COMENTARIOS;
	
	$sql = "CREATE TABLE IF NOT EXISTS $TABLA_CONFIGURACION" .
		   " (nombre VARCHAR(50)  NOT NULL," .
		   "  valor  VARCHAR(100) NOT NULL DEFAULT ''," .
		   "  PRIMARY KEY (nombre));";
	$wpdb->query ($sql);
	$sql = "CREATE TABLE IF NOT EXISTS $TABLA_FOTOS" .
		   " (id INT              NOT NULL AUTO_INCREMENT," .
		   "  ruta VARCHAR(1024)  NOT NULL," .
		   "  PRIMARY KEY (id));";
	$wpdb->query ($sql);
	$sql = "CREATE TABLE IF NOT EXISTS $TABLA_COMENTARIOS" .
		   " (id INT              NOT NULL AUTO_INCREMENT," .
		   "  foto INT            NOT NULL," .
		   "  texto TEXT NOT NULL DEFAULT ''," .
		   "  PRIMARY KEY (id));";
	$wpdb->query ($sql);
	
	if (!LeerConfiguracion (CONFIGURACION_TAMANO_MINIATURA))
	{
		EscribirConfiguracion (CONFIGURACION_TAMANO_MINIATURA, 120, true);
	}
	if (!LeerConfiguracion (CONFIGURACION_TAMANO_NORMAL))
	{
		EscribirConfiguracion (CONFIGURACION_TAMANO_NORMAL, 420, true);
	}
	if (!LeerConfiguracion (CONFIGURACION_ANCHO))
	{
		EscribirConfiguracion (CONFIGURACION_ANCHO, 3, true);
	}
}

function FiltroMisFotos ($contenido)
{
	Iniciar ();
	
	$busqueda = "/\[misfotos\s*fotos\s*=\s*([^\]]+)\s*miniaturas\s*=\s*([^\]]+)\s*ruta\s*=\s*([^\]]+)\s*\]/i";

	preg_match_all ($busqueda, $contenido, $aciertos);

	if (is_array ($aciertos[1]))
	{
		for ($m = 0; $m < count ($aciertos[0]); $m++)
		{
			if ($m == 0)
			{
				$fotos = trim ($aciertos[1][$m]);
				$miniaturas = trim ($aciertos[2][$m]);
				$mostrarRuta = (strcasecmp (trim ($aciertos[3][$m]), "si") == 0);
				$galeria = new Galeria ($fotos,
										$miniaturas, 
										LeerConfiguracion (CONFIGURACION_TAMANO_MINIATURA), 
										LeerConfiguracion (CONFIGURACION_TAMANO_NORMAL));
				$contenido = str_replace ($aciertos[0][$m], $galeria->Pintar ($mostrarRuta, LeerConfiguracion (CONFIGURACION_ANCHO)), $contenido);
			}
			else
			{
				$contenido = str_replace ($aciertos[0][$m], "<b>[MisFotos ERROR: S&oacute;lo una galer&iacute;a por p&aacute;gina permitida]</b>", $contenido);
			}
		}
	}

	return ($contenido);
}

add_filter ('the_content', 'FiltroMisFotos');

?>
