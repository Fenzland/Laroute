<?php

namespace Laroute\Route;

////////////////////////////////////////////////////////////////

trait TContainer
{

	/**
	 * Var items
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $items= [];

	/**
	 * Method addRoute
	 *
	 * @access public
	 *
	 * @param Laroute\Route\Contracts\IItem $item
	 *
	 * @return void
	 */
	public function addItem( Contracts\IItem$item )
	{
		$this->items[]= $item;
	}

}
