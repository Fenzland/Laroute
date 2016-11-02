<?php

namespace Laroute\Route;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

class SimpleClosureAction extends ClosureAction
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
	 * @param  Line $line
	 */
	public function __construct( Line$line )
	{
		$this->param= $line->pregGet(self::PATTERN,1);

		$this->body= 'return '.$line->pregGet(self::PATTERN,2).';';
	}

}
