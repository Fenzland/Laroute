<?php

namespace Laroute;

use Laroute\Exceptions\LarouteSyntaxException as Exception;
use Illuminate\Container\Container;

////////////////////////////////////////////////////////////////

if( !function_exists('\Laroute\route') ){
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

		$routes->execute(Container::getInstance()->make('router'));
	}
}

if( !function_exists('\Laroute\listing') ){
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
