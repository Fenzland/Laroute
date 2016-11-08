<?php

namespace Laroute\Route;

use Laroute\Document\Line;
use Laroute\Exceptions\LarouteSyntaxException as Exception;

////////////////////////////////////////////////////////////////

class ResourceRoute extends ARoute
{

	/**
	 * Var var
	 *
	 * @access public
	 *
	 * @var
	 */
	const ACTIONS= [ 'index', 'create', 'store', 'show', 'edit', 'update', 'destroy', ];

	/**
	 * Var actionsOnlyOrExcept
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $actionsOnlyOrExcept;

	/**
	 * Var actions
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $actions;

	/**
	 * Method parseFirstLine
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function parseFirstLine( Line$line )
	{
		$this
		->parsePath($line)
		->parseName($line)
		;
	}

	/**
	 * Method listing
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function listing():array
	{
		$list= [
			'resource'=> $this->name,
			'path'=> $this->path,
		];

		$this->actionsOnlyOrExcept and $list[$this->actionsOnlyOrExcept]= $this->actions;

		return $list;
	}

	/**
	 * Method makeCallback
	 *
	 * @access public
	 *
	 * @return callable
	 */
	public function makeCallback():callable
	{
		$options= [ 'names'=>[], ];

		$this->actionsOnlyOrExcept and $options[$this->actionsOnlyOrExcept]= $this->actions;

		foreach( self::ACTIONS  as $action ){
			$options['names'][]= "{$this->name}.$action";
		}

		return function()use( $options ){
			$route= app('router')->resource($this->path,$this->action->output(),$options);
		};
	}

	/**
	 * Method method
	 *
	 * @access public
	 *
	 * @return null
	 */
	public function getOpenedClosure()
	{
		return null;
	}

	/**
	 * Method isNameRequired
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function isNameRequired():bool
	{
		return true;
	}

	/**
	 * Method getFeedingMap
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function getFeedingMap():array
	{
		static $methodMap= [
			'>'=> [
				'>'=> 'Action',
			],
			'*'=> 'OnlyActions',
			'-'=> 'ExceptActions',
		];

		return $methodMap;
	}

	/**
	 * Method feedTheOnlyActions
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheOnlyActions( Line$line )
	{
		$this->feedOnlyOrExceptActions($line,'only');
	}

	/**
	 * Method feedTheExceptActions
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheExceptActions( Line$line )
	{
		$this->feedOnlyOrExceptActions($line,'except');
	}

	/**
	 * Method feedOnlyOrExceptActions
	 *
	 * @access private
	 *
	 *
	 * @param  \Laroute\Document\Line $line
	 * @param  string $type
	 *
	 * @return void
	 */
	private function feedOnlyOrExceptActions( Line$line, string$type )
	{
		$this->sureActionsNotDefined();

		$this->actionsOnlyOrExcept= $type;

		$this->actions= array_intersect(...[
			explode(...[
				' ',
				trim( $line->slice(1) ),
			]),
			self::ACTIONS,
		]);
	}

	/**
	 * Method sureActionsNotDefined
	 *
	 * @access private
	 *
	 * @return
	 */
	private function sureActionsNotDefined()
	{
		if( $this->actionsOnlyOrExcept ){
			throw new Exception('Actions only or except can defined only once.');
		}
	}

}
