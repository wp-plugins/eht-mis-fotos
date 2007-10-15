<?php

require_once ("Utilidades.php");
require_once ("Directorio.php");

define ("MINIATURA_DIRECTORIO", 1);
define ("MINIATURA_FICHERO", 2);
define ("MINIATURA_VACIA", 3);

class Galeria
{
    
	public function __construct ($rutaImagenes,
								 $rutaMiniaturas,
								 $miniatura = 120,
								 $normal = 420)
	{
		$ruta = GetVariable ("ruta");
		$this->modo = GetVariable ("modo");
		$this->foto = GetVariable ("foto");

		$this->imagenes = new Directorio ($rutaImagenes);
		$this->imagenes->AgregarDirectorio ($ruta);
		$this->miniaturas = new Directorio ($rutaMiniaturas);
		$this->miniaturas->AgregarDirectorio ($ruta, true);
		$this->ruta = new Directorio ($ruta);
		$this->miniatura = $miniatura;
		$this->normal = $normal;
	}

	public function Pintar ($mostrarRuta, $ancho = 3)
	{		
		$texto .= "<a name=\"galeria\"></a>\n";
		if ($mostrarRuta)
		{
			$texto .= $this->PintarRuta ();
		}
		if (($this->modo == "normal") && (strlen ($this->foto) > 0))
		{
			$texto .= $this->PintarNormal ($this->foto);
		}
		else
		{
			$texto .= $this->PintarMiniaturas ($ancho);
		}

		return ($texto);
	}
	
	private function PintarRuta ()
	{
		$trozos = explode (DIRECTORY_SEPARATOR, $this->ruta->GetRuta ());
		$ruta = "";
		$texto .= "<p>";
		$texto .= "<a href=\"$PHP_SELF?ruta=/\">RA&Iacute;Z</a>";
		foreach ($trozos as $trozo)
		{
			if (strlen ($trozo) > 0)
			{
				$ruta .= DIRECTORY_SEPARATOR . $trozo;
				$texto .= "/<a href=\"$PHP_SELF?ruta=" . urlencode ($ruta) . "#galeria\">$trozo</a>";
			}
		}
		$texto .= "</p>\n";

		return ($texto);
	}
	private function PintarMiniaturas ($ancho = 3, $alto = 2, $pagina = 0)
	{
		$ficheros = $this->imagenes->Listar (array ("jpg", "jpeg", "png", "gif"));
		$numeroDirectorios = count ($ficheros[0]);
		$numeroFicheros = count ($ficheros[1]);
		$numeroTotal = $numeroDirectorios + $numeroFicheros;
		$this->ancho = $ancho;
		$this->x = 0;
		$this->total = $numeroTotal;

		if ($this->ruta->GetRuta () != "/")
		{
			$this->total++;
			$texto .= $this->PintarMiniatura (MINIATURA_DIRECTORIO, "..");
		}
		$this->limite = ($this->total <= $ancho) ? $ancho : ((floor (($this->total - 1) / $ancho) + 1) * $ancho);
		if ($numeroTotal > 0)
		{
			for ($i = 0;
				 $i < $numeroDirectorios;
				 $i++)
			{
				$texto .= $this->PintarMiniatura (MINIATURA_DIRECTORIO, $ficheros[0][$i]);
			}
			for ($i = 0;
				 $i < $numeroFicheros;
				 $i++)
			{
				$texto .= $this->PintarMiniatura (MINIATURA_FICHERO, $ficheros[1][$i]);
			}
			for ($i = $this->total;
				 $i < $this->limite;
				 $i++)
			{
				$texto .= $this->PintarMiniatura (MINIATURA_VACIA, "");
			}
		}

		return ($texto);
	}

	private function PintarNormal ($nombre)
	{
		$ficheros = $this->imagenes->Listar (array ("jpg", "jpeg", "png", "gif"));
		$numeroFicheros = count ($ficheros[1]);
		$indice = -1;
		$hayAnterior = false;
		$haySiguiente = false;

		for ($i = 0; ($i < $numeroFicheros) && ($indice < 0); $i++)
		{
			if ($ficheros[1][$i] == $nombre)
			{
				$indice = $i + 1;
				if (($hayAnterior = ($i > 0)))
				{
					$enlaceAnterior = $PHP_SELF .
									  "?ruta=" . urlencode ($this->ruta->GetRuta ()) .
									  "&foto=" . urlencode ($ficheros[1][$i - 1]) .
									  "&modo=normal";
				}
				if (($haySiguiente = ($i < ($numeroFicheros - 1))))
				{
					$enlaceSiguiente = $PHP_SELF .
									   "?ruta=" . urlencode ($this->ruta->GetRuta ()) .
									   "&foto=" . urlencode ($ficheros[1][$i + 1]) .
									   "&modo=normal";
				}
			}
		}


		if ($indice >= 0)
		{
			$enlace = DIRECTORY_SEPARATOR . $this->imagenes->GetRuta () . $nombre;
			$enlaceBase = "<a href=\"$PHP_SELF?ruta=" . urlencode ($this->ruta->GetRuta ()) . "#galeria\">[miniaturas]</a>";
			$normal = $this->GetReducida ($this->imagenes->GetRuta (),
										  $this->miniaturas->GetRuta (),
										  $nombre,
										  $this->normal);

			$texto .= "<center>\n";
			$texto .= "   <table width=\"100%\">\n";
			$texto .= "      <tr>\n";
			$texto .= "         <td align=\"left\">\n";
			if ($hayAnterior)
			{
				$texto .= "            <a href=\"$enlaceAnterior#galeria\">&lt;&lt;Anterior</a>\n";
			}
			else
			{
				$texto .= "            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>\n";
			}
			$texto .= "         </td>\n";
			$texto .= "         <td align=\"center\" width=\"100%\">\n";
			$texto .= "            Foto $indice de $numeroFicheros $enlaceBase\n";
			$texto .= "         </td>\n";
			$texto .= "         <td align=\"right\">\n";
			if ($haySiguiente)
			{
				$texto .= "            <a href=\"$enlaceSiguiente#galeria\">Siguiente&gt;&gt;</a>\n";
			}
			else
			{
				$texto .= "            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>\n";
			}
			$texto .= "         </td>\n";
			$texto .= "      </tr>\n";
			$texto .= "   </table><br>\n";
			$texto .= "   <a href=\"$enlace\" target=\"_blank\" border=\"0\"><img src=\"/" . $normal . "\" border=\"0\"></a>\n";
			$texto .= "   <p><a href=\"$enlace\" target=\"_blank\">" . htmlentities ($nombre) . "</a></p>\n";
			$texto .= "</center>\n";
		}

		return ($texto);
	}
	private function PintarMiniatura ($tipoImagen, $nombre)
	{
		global $wpdb, $TABLA_FOTOS;
		
		$nombreRetocado = wordwrap ($nombre, 20, " ", true);

		if ($nombre == "..")
		{
			$enlace = $PHP_SELF . "?ruta=" . urlencode ($this->ruta->GetRutaPadre ()) . "#galeria";
		}
		else
		{
			if ($tipoImagen == MINIATURA_FICHERO)
			{
				$enlace = $PHP_SELF .
						  "?ruta=" . urlencode ($this->ruta->GetRuta ()) .
						  "&foto=" . urlencode ($nombre) .
						  "&modo=normal#galeria";
			}
			else if ($tipoImagen == MINIATURA_DIRECTORIO)
			{
				$enlace = $PHP_SELF . "?ruta=" . urlencode ($this->ruta->GetRuta () . $nombre) . "#galeria";
			}

			if (($tipoImagen == MINIATURA_FICHERO) ||
				($tipoImagen == MINIATURA_DIRECTORIO))
			{
				$ruta = Directorio::Concatenar ($this->imagenes->GetRuta (), $nombre);
				$sql = "SELECT id FROM $TABLA_FOTOS WHERE ruta = '$ruta';";
				if (!$wpdb->get_var ($sql))
				{
					$sql = "INSERT INTO $TABLA_FOTOS (ruta) VALUES ('$ruta');";
					$wpdb->query ($sql);
				}
			}
		}

		$base = "wp-content/plugins/eht-mis-fotos/imagenes/";
		if ($tipoImagen == MINIATURA_FICHERO)
		{
			$miniatura = "/" . $this->GetReducida ($this->imagenes->GetRuta (),
												   $this->miniaturas->GetRuta (),
												   $nombre,
												   $this->miniatura);
		}
		else if ($tipoImagen == MINIATURA_DIRECTORIO)
		{
			$miniatura = "/" . $this->GetReducida ($base,
											 $base,
											 "Carpeta.jpg",
											 $this->miniatura);
		}
		else
		{
			$miniatura = "/" . $base . "Transparente.gif";
		}

		if ($this->x == 0)
		{
			$texto .= "<table>\n";
		}
		if ($this->x % $this->ancho == 0)
		{
			$texto .= "   <tr valign=\"top\">\n";
		}
		$texto .= "      <td align=\"center\">\n";
		if ($tipoImagen == MINIATURA_VACIA)
		{
			$texto .= "         <img src=\"" . $miniatura . "\" border=\"0\" width=\"" . $this->miniatura . "\" height=\"" . $this->miniatura . "\">\n";
		}
		else
		{
			$texto .= "         <a href=\"$enlace\" border=\"0\"><img src=\"" . $miniatura . "\" border=\"0\"></a>\n";
			$texto .= "         <p><a href=\"$enlace\" border=\"0\">" . htmlentities ($nombreRetocado) . "</a></p>\n";
		}
		$texto .= "      </td>\n";
		$this->x++;
		if ($this->x % $this->ancho == 0)
		{
			$texto .= "   </tr>\n";
		}
		if ($this->x == $this->limite)
		{
			$texto .= "</table>\n";
		}

		return ($texto);
	}

	private function GetReducida ($rutaImagen, $rutaMiniatura, $nombre, $reduccion)
	{
		if (($posicion = strrpos ($nombre, ".")) === false)
		{
			$fichero = $nombre;
			$extension = "";
		}
		else
		{
			$fichero = substr ($nombre, 0, $posicion);
			$extension = substr ($nombre, $posicion + 1);
		}
		$nombreImagen = $rutaImagen . $nombre;
		$nombreMiniatura = $rutaMiniatura . $fichero .
						   "_" . $reduccion . "." . $extension;
		if (file_exists ($nombreImagen) && (!file_exists ($nombreMiniatura)))
		{
			if ((strcasecmp ($extension, "jpg") == 0) ||
				(strcasecmp ($extension, "jpeg") == 0))
			{
				$imagen = imagecreatefromjpeg ($nombreImagen);
			}
			else if (strcasecmp ($extension, "png") == 0)
			{
				$imagen = imagecreatefrompng ($nombreImagen);
			}
			else if (strcasecmp ($extension, "gif") == 0)
			{
				$imagen = imagecreatefromgif ($nombreImagen);
			}
			else
			{
				$imagen = false;
			}

			if ($imagen !== false)
			{
				$ancho = ImageSX ($imagen);
				$alto = ImageSY ($imagen);
				$radio= $ancho / $alto;
				if ($radio > 1)
				{
					$nuevoAncho = $reduccion;
					$nuevoAlto = $reduccion / $radio;
				}
				else
				{
					$nuevoAncho = $reduccion * $radio;
					$nuevoAlto = $reduccion;
				}
				$miniatura = imagecreatetruecolor ($nuevoAncho, $nuevoAlto);
				imagecopyresampled ($miniatura, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
				imagejpeg ($miniatura, $nombreMiniatura);
				imagedestroy ($imagen);
				imagedestroy ($miniatura);
			} 
		}
		
		return ($nombreMiniatura);
	}
	    
}

?>