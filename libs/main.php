<?php

namespace Laroute;

////////////////////////////////////////////////////////////////

function route( string$filename )
{
	$routes= new Laroute($filename);

	$routes->execute();
}
