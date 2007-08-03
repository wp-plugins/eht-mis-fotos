<?php

	function MatrizACadena ($array, $depth = 0)
	{
		if ($depth > 0)
		{
			$tab = implode ('', array_fill (0, $depth, "\t"));	
		}
		$text .= "array (\n";
		$count = count ($array);
		
		$x = 0;
		foreach ($array as $key => $value)
		{
			$x++;
			
			if (is_array ($value))
			{
				if (substr ($text, -1, 1) == ')')
				{
					$text .= ',';
				}
				$text .= $tab . "\t" . '"' . $key . '"' . " => " . MatrizACadena ($value, $depth + 1);
				continue;
			}
			
			$text .= $tab . "\t" . "\"$key\" => \"$value\"";
			
			if ($count != $x)
			{
				$text .= ",\n";
			}
		}
		
		$text .= "\n" . $tab . ")\n";
		
		if (substr ($text, -4, 4) == '),),')
		{
			$text .= '))';
		}
		
		return ($text);
	}
	
	function GetVariable ($nombre)
	{
		if (isset ($_GET[$nombre]))
		{
			$variable = $_GET[$nombre];
		}
		else if (isset ($_POST[$nombre]))
		{
			$variable = $_POST[$nombre];
		}
		else
		{
			$variable = "";
		}
		
		return ($variable);
	}
	function GetSesion ($variable)
	{
		return (isset ($_SESSION[DOMINIO_SESION . $variable]) ? $_SESSION[DOMINIO_SESION . $variable] : "");
	} 
	function SetSesion ($variable, $valor = "")
	{
		$valorAnterior = $_SESSION[DOMINIO_SESION . $variable];
		$_SESSION[DOMINIO_SESION . $variable] = $valor;
		
		return ($valorAnterior);
	} 

?>
