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

}
