<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Iterators;

/**
 * Class WpUserIterator
 * @package FernleafSystems\Wordpress\Plugin\Shield\Users
 */
class WpUserIterator extends \Elliotchance\Iterator\AbstractPagedIterator {

	const PAGE_LIMIT = 50;

	/**
	 * @var int
	 */
	protected $nTotalSize;

	/**
	 * @var array
	 */
	private $aQueryFilters;

	/**
	 * @return \WP_User
	 */
	public function current() {
		return parent::current();
	}

	/**
	 * @param string $sMetaKey
	 * @param mixed  $sMetaValue
	 * @param string $sComparison
	 * @return $this
	 */
	public function filterByMeta( $sMetaKey, $sMetaValue, $sComparison = '=' ) {
		return $this->setCustomQueryFilter( 'meta_key', $sMetaKey )
					->setCustomQueryFilter( 'meta_value', $sMetaValue )
					->setCustomQueryFilter( 'meta_compare', $sComparison );
	}

	/**
	 * @return array
	 */
	public function getCustomQueryFilters() {
		return is_array( $this->aQueryFilters ) ? $this->aQueryFilters : [];
	}

	/**
	 * @return array
	 */
	protected function getDefaultQueryFilters() {
		return [
			'orderby' => 'ID',
			'order'   => 'ASC',
			'paged'   => 1,
			'number'  => $this->getPageSize(),
		];
	}

	/**
	 * @return array
	 */
	protected function getFinalQueryFilters() {
		return array_merge( $this->getDefaultQueryFilters(), $this->getCustomQueryFilters() );
	}

	/**
	 * @param array $aQuery
	 * @return $this
	 */
	public function setCustomQueryFilters( $aQuery ) {
		if ( is_array( $aQuery ) ) {
			if ( isset( $aQuery[ 'number' ] ) && (int)$aQuery[ 'number' ] < 0 ) {
				unset( $aQuery[ 'number' ] );
			}
			$this->aQueryFilters = $aQuery;
		}
		return $this;
	}

	/**
	 * @param string $sKey
	 * @param mixed  $mValue
	 * @return $this
	 */
	public function setCustomQueryFilter( $sKey, $mValue ) {
		$aQ = $this->getCustomQueryFilters();
		$aQ[ $sKey ] = $mValue;
		return $this->setCustomQueryFilters( $aQ );
	}

	/**
	 * @return int
	 */
	public function getTotalSize() {
		if ( !isset( $this->nTotalSize ) ) {
			$this->nTotalSize = $this->setCustomQueryFilter( 'count_total', true )
									 ->runQuery()
									 ->get_total();
			$this->setCustomQueryFilter( 'count_total', null );
		}
		return $this->nTotalSize;
	}

	/**
	 * @param int $nPage - starts at zero
	 * @return \WP_User[]
	 */
	public function getPage( $nPage ) {
		return $this->setCustomQueryFilter( 'paged', $nPage + 1 )
					->runQuery()
					->get_results();
	}

	/**
	 * @return \WP_User_Query
	 */
	protected function runQuery() {
		return new \WP_User_Query( $this->getFinalQueryFilters() );
	}

	/**
	 * @return int
	 */
	public function getPageSize() {
		return static::PAGE_LIMIT;
	}

	public function rewind() {
		parent::rewind();
		unset( $this->nTotalSize );
	}
}