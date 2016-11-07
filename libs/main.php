<?php

namespace Laroute;

use Laroute\Exceptions\LarouteSyntaxException as Exception;

////////////////////////////////////////////////////////////////

if( !function_exists('route') ){
	/**
	 * Creating routes from a laroute file.
	 *
	 * @param  string $filename
	 *
	 * @return void
	 */
	function route( string$filename )
	{
		$routes= new Laroute($filename);

		try{
			$routes->parse();
		}catch( Exception$e ){
			throw $routes->makeException($e);
		}

		$routes->execute();
	}
}

if( !function_exists('listing') ){
	/**
	 * Listing routes from a laroute file.
	 *
	 * @param  string $filename
	 *
	 * @return void
	 */
	function listing( string$filename )
	{
		$routes= new Laroute($filename);

		try{
			$routes->parse();
		}catch( Exception$e ){
			throw $routes->makeException($e);
		}

		return $routes->listing();
	}
}
