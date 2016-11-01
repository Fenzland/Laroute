<?php

namespace Laroute\Route\Contracts;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

interface IRoute
{

	/**
	 * Method __construct
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	function __construct( Line$line );

	/**
	 * Method feed
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	function feed( Line$line );

}
