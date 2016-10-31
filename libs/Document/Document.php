<?php

namespace Laroute\Document;

////////////////////////////////////////////////////////////////

class Document
{

	/**
	 * File handle.
	 *
	 * @access private
	 *
	 * @var    resource
	 */
	private $handle;

	/**
	 * Path of file.
	 *
	 * @access public
	 *
	 * @var    string
	 */
	public $filePath;

	/**
	 * Constructing a laroute document from a .laroute file.
	 *
	 * @access public
	 *
	 * @param  string $filePath
	 */
	public function __construct( string$filePath )
	{
		substr($filePath,-8)==='.laroute' or $filePath.= '.laroute';

		if( !file_exists($filePath) || !is_file($filePath) ){
			throw new \Exception("Route file $filePath not exists.");
		}

		$this->filePath= $filePath;

		$this->handle= fopen($filePath,'r');
	}

	/**
	 * Method getLine
	 *
	 * @access public
	 *
	 * @return Line
	 */
	public function getLine():Line
	{
		while( "\n" === $content= fgets($this->handle) );

		return new Line($content);
	}

}
