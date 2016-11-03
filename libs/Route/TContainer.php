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

	/**
	 * Method map
	 *
	 * @access public
	 *
	 * @param  callable $callback
	 *
	 * @return mixed
	 */
	public function mapItems( callable$callback )
	{
		return array_map($callback,$this->items);
	}

	/**
	 * Method mapRecursive
	 *
	 * @access public
	 *
	 * @param  callable $callback
	 *
	 * @return mixed
	 */
	public function mapRecursive( callable$callback )
	{
		return array_map(function($item){
			return $callback($item,( ( $item instanceof self )? $item->mapRecursive($callback) : null ));
		},$this->items);
	}

}
