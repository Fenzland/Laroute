<?php

namespace Laroute\Route\Contracts;

////////////////////////////////////////////////////////////////

interface IContainer
{

	/**
	 * Method addRoute
	 *
	 * @param Laroute\Route\Contracts\IItem $item
	 *
	 * @return void
	 */
	function addItem( IItem$item );

	/**
	 * Method map
	 *
	 * @param  callable $callback
	 *
	 * @return mixed
	 */
	function mapItems( callable$callback );

	/**
	 * Method mapRecursive
	 *
	 * @param  callable $callback
	 *
	 * @return mixed
	 */
	function mapRecursive( callable$callback );
}
