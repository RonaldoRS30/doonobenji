<?php
class Database
{
    public static function Conectar()
    {        
        try
			{
			$conexionn = new PDO('mysql:host=localhost;dbname=amcsolution2_doonobenji;charset=utf8', 'amcsolution2_doonobenji', 'amcsolution2_doonobenji');
	        	$conexionn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	        	return $conexionn;  
			}
				catch(Exception $e)
			{
				die($e->getMessage());
			}
    }
}
?>