<?php

namespace Laroute;

use Document\Document;
use Document\Line;

////////////////////////////////////////////////////////////////

class Laroute
{
	/**
	 * Loading a route file and creating routes.
	 *
	 * @access public
	 *
	 * @param  string $filename
	 */
	public function construct( string$filename )
	{
		$this->document= new Document($filename);

		$this->parse();
	}

	/**
	 * Parsing route file.
	 *
	 * @access public
	 */
	public function parse()
	{
		while( $this->document->hasMore() ){
			$this->parseLine($this->document->line());
		}
	}

	/**
	 * Parsing a line
	 *
	 * @access public
	 *
	 * @param  Line $line
	 */
	private function parseLine( Line$line )
	{
		#
	}
}
