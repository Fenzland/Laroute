<?php

namespace Laroute;

use Laroute\Exceptions\LarouteSyntaxException as Exception;

////////////////////////////////////////////////////////////////

function route( string$filename )
{
	$routes= new Laroute($filename);
	try{
		$routes->parse();
	}catch( Exception$e ){
		throw $routes->makeException($e);
	}

	z($routes->list());

	$routes->execute();
}
