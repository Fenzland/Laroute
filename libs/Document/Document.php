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
	 * @access private
	 *
	 * @var    string
	 */
	private $filePath;

	/**
	 * Var lineIndex
	 *
	 * @access private
	 *
	 * @var
	 */
	private $lineIndex= -1;

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
		while( "\n" === $content= fgets($this->handle) ){
			++$this->lineIndex;
		}
		++$this->lineIndex;

		return new Line($content);
	}

	/**
	 * Method getFilePath
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function getFilePath():string
	{
		return $this->filePath;
	}

	/**
	 * Method getLineIndex
	 *
	 * @access public
	 *
	 * @return int
	 */
	public function getLineIndex():int
	{
		return $this->lineIndex;
	}

	/**
	 * Method getLineNumber
	 *
	 * @access public
	 *
	 * @return int
	 */
	public function getLineNumber():int
	{
		return $this->lineIndex+1;
	}

}
