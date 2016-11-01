<?php

namespace Laroute\Route;

use Laroute\RouteContainer;
use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

class Group implements Contracts\IItem, Contracts\IContainer
{
	use TContainer;

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
	private $middlewares= [];


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
		$this->line->pregMap('/ >([^\\s]+)/',function( string$matches ){
			$this->middlewares[]= $matches[1];
		});

		return $this;
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
		$this->line->pregMap('/ @([^\\s]+)/',function( string$matches ){
			$this->domain= $matches[1];
		});

		return $this;
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
		$this->line->pregMap('/ \\/([^\\s]+)/',function( string$matches ){
			$this->prefix= $matches[1];
		});

		return $this;
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
		$this->line->pregMap('/ &([^\\s]+)/',function( string$matches ){
			$this->namespace= $matches[1];
		});

		return $this;
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
		$this->line->pregMap('/ \\+([^\\s]+)/',function( string$matches ){
			$this->namePrefix= $matches[1];
		});

		return $this;
	}

}
