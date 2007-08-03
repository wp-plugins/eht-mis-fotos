<?php

require_once ("Utilidades.php");

class Directorio
{

    public function __construct ($ruta)
    {
		$this->SetRuta ($ruta);
    }
	    
    public function GetRuta ()
    {
		return ($this->ruta);
    }
    public function GetRutaPadre ()
    {
		$rutaSinBarra = substr ($this->ruta, 0, strlen ($this->ruta) - 2);
		$posicion = strrpos ($rutaSinBarra, DIRECTORY_SEPARATOR);
		if ($posicion === false)
		{
		    $padre = "/";
		}
		else
		{
		    $padre = substr ($this->ruta, 0, $posicion);
		}
		
		return ($padre);
    }
    public function SetRuta ($ruta)
    {
		$this->ruta = $ruta;
		
		if ((strlen ($this->ruta) <= 0) ||
		    ($this->ruta{strlen ($this->ruta) - 1} != DIRECTORY_SEPARATOR))
		{
		    $this->ruta .= DIRECTORY_SEPARATOR;
		}
    }
    public function AgregarDirectorio ($directorio, $creando)
    {
    	if ($creando)
    	{
    		$trozos = explode (DIRECTORY_SEPARATOR, $directorio);
    		$ruta = $this->ruta;
    		if (file_exists ($ruta) && is_dir ($ruta) && is_array ($trozos))
    		{
    			for ($i = 0; $i < count ($trozos); $i++)
    			{
    				$ruta .= $trozos[$i] . DIRECTORY_SEPARATOR;
    				if (!file_exists ($ruta))
    				{
    					mkdir ($ruta);
    				}
    			}
    		}
    	}
		$this->SetRuta (Directorio::Concatenar ($this->ruta, $directorio));
    }
	    
    public function ListarFiltrado ($filtro = "*")
    {
		$directorio = opendir ($this->ruta);
		$ficheros[0] = array ();
		$ficheros[1] = array ();
		while (false !== ($entrada = readdir ($directorio)))
		{
		    if (($entrada != ".") && ($entrada != ".."))
		    {
				if (is_dir ($this->ruta . $entrada))
				{
				    $ficheros[0][] = $entrada;
				}
				else if (($filtro == "*") ||
					 (preg_match ("/" . $filtro . "/i", $entrada)))
				{
				    $ficheros[1][] = $entrada;
				}
		    }
		}
		sort ($ficheros[0]);
		sort ($ficheros[1]);
		
		return ($ficheros);
    }
    public function Listar ($extensiones)
    {
		if (count ($extensiones) <= 0)
		{
		    $filtro = "*";
		}
		else
		{
		    foreach ($extensiones as $extension)
		    {
			$filtro .= ((strlen ($filtro) > 0) ? "|" : "");
			$filtro .= "(.*\.$extension\Z)";
		    }
		}
	 
		return ($this->ListarFiltrado ($filtro));
	}

	public static function Concatenar ($izquierda, $derecha)
	{
		$resultado = "";
		
		if (strlen ($izquierda) > 0)
		{
			$resultado = $izquierda;
			if (strlen ($derecha) > 0)
			{
				$resultado = Directorio::QuitarBarrasFinal ($resultado);
				$resultado .= DIRECTORY_SEPARATOR;
				$resultado .= Directorio::QuitarBarrasInicio ($derecha);
			}
		}
		
		return ($resultado);
	}
	
	private static function QuitarBarrasInicio ($ruta)
	{
		$resultado = $ruta;
		while (substr ($resultado, 0, 1) == DIRECTORY_SEPARATOR)
		{
			$resultado = substr ($resultado, 1);
		}
		
		return ($resultado);
	}
	private static function QuitarBarrasFinal ($ruta)
	{
		$resultado = $ruta;
		while (substr ($resultado, strlen ($resultado) - 1) == DIRECTORY_SEPARATOR)
		{
			$resultado = substr ($resultado, 0, strlen ($resultado) - 1);
		}
		
		return ($resultado);
	}

}

?>