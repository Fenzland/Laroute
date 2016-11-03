<?php

namespace Laroute\Route\Action;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

class SimpleClosureAction extends AClosureAction
{

	/**
	 * Var var
	 *
	 * @access public
	 *
	 * @const    string
	 */
	const PATTERN= '/^\\((.*?)\\)=> (.*?)$/';

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	public function __construct( Line$line )
	{
		$this->setParamString($line->pregGet(self::PATTERN,1));

		$this->appendBody('return ',$line->pregGet(self::PATTERN,2),';');
	}

}
