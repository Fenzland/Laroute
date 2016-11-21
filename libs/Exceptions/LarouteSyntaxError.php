<?php

namespace Laroute\Exceptions;


////////////////////////////////////////////////////////////////

class LarouteSyntaxError extends \Exception implements \Throwable
{

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param \Laroute\Exceptions\LarouteSyntaxException $e
	 * @param string                                     $file
	 * @param int                                        $line
	 */
	public function __construct( LarouteSyntaxException$e, string$file, int$line )
	{
		parent::__construct($e->getMessage(),$e->getCode());

		$this->file= $file;
		$this->line= $line;
	}

}
