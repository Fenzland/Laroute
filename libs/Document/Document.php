<?php

namespace Laroute\Document;

use Laroute\Helper\TGetter;

////////////////////////////////////////////////////////////////

class Document
{
	use TGetter;

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
	 * Var parent
	 *
	 * @access private
	 *
	 * @var    self
	 */
	private $parent;

	/**
	 * Var indentLevel
	 *
	 * @access private
	 *
	 * @var    int
	 */
	private $indentLevel= 0;

	/**
	 * Constructing a laroute document from a .laroute file.
	 *
	 * @access public
	 *
	 * @param  string       $filePath
	 * @param  self | null  $parent
	 * @param  int          $indentLevel
	 */
	public function __construct( string$filePath, self$parent=null, int$indentLevel=0 )
	{
		$this->parent= $parent;
		$this->indentLevel= $indentLevel;

		substr($filePath,-8)==='.laroute' or $filePath.= '.laroute';

		if( !file_exists($filePath) || !is_file($filePath) ){
			throw new \Exception("Route file $filePath not exists.");
		}

		$this->filePath= $filePath;

		$this->handle= fopen($filePath,'r');
	}

	/**
	 * Method getParent
	 *
	 * @access public
	 *
	 * @return self | null
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Method getIndentLevel
	 *
	 * @access public
	 *
	 * @return int
	 */
	public function getIndentLevel():int
	{
		return $this->indentLevel;
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
