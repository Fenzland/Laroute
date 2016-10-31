<?php

namespace Laroute\Document;

////////////////////////////////////////////////////////////////

class Group
{

	/**
	 * Var line
	 *
	 * @access private
	 *
	 * @var    \Laroute\Document\Line
	 */
	private $line;

	/**
	 * Var middlewares
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $middlewares;


	/**
	 * Var domain
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $domain;


	/**
	 * Var prefix
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $prefix;


	/**
	 * Var namespace
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $namespace;


	/**
	 * Var namePrefix
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $namePrefix;

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  Line $line
	 *
	 * @return
	 */
	public function __construct( Line$line )
	{
		$this->line= $line;

		$this
		->parseMiddleware()
		->parseDomain()
		->parsePrefix()
		->parseNamespace()
		->parseNamePrefix()
		;
	}

	/**
	 * Method parseMiddleware
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseMiddleware():self
	{
		return $this->matchAndDo(function( string$match ){
			$this->middlewares= explode('>',$match);
		},'/ >([^\\s]+)/',1);
	}

	/**
	 * Method parseDomain
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseDomain():self
	{
		return $this->matchAndDo(function( string$match ){
			$this->domain= $match;
		},'/ @([^\\s]+)/',1);
	}

	/**
	 * Method parsePrefix
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parsePrefix():self
	{
		return $this->matchAndDo(function( string$match ){
			$this->prefix= $match;
		},'/ \\/([^\\s]+)/',1);
	}

	/**
	 * Method parseNamespace
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parseNamespace():self
	{
		return $this->matchAndDo(function( string$match ){
			$this->namespace= $match;
		},'/ &([^\\s]+)/',1);
	}

	/**
	 * Method parseNamePrefix
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parseNamePrefix():self
	{
		return $this->matchAndDo(function( string$match ){
			$this->namePrefix= $match;
		},'/ \\+([^\\s]+)/',1);
	}

	/**
	 * Method matchAndDo
	 *
	 * @access private
	 *
	 * @param  callable     $callback
	 * @param  string       $pattern
	 * @param  int | string $match   Group index or name
	 *
	 * @return self
	 */
	private function matchAndDo( callable$callback, string$pattern, /*int|string*/$matchGroup=0 ):self
	{
		if( $match= $this->line->pregGet($pattern,$matchGroup) ){
			$callback($match);
		}

		return $this;
	}

}
