<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Class BaseHandler
 * @package FernleafSystems\Wordpress\Plugin\Shield\Databases\Base
 */
class Handler {

	use ModConsumer;

	/**
	 * The defined table columns.
	 * @var array
	 */
	protected $aColDef;

	/**
	 * The actual table columns.
	 * @var array
	 */
	protected $aColActual;

	/**
	 * @var string
	 */
	protected $sTable;

	/**
	 * @var bool
	 * @deprecated 9.0.4
	 */
	private $bTableExist;

	/**
	 * @var bool
	 */
	private $bIsReady;

	/**
	 * @var string
	 */
	private $sSqlCreate;

	public function __construct() {
	}

	public function autoCleanDb() {
	}

	/**
	 * @param int $nAutoExpireDays
	 * @return $this;
	 */
	public function cleanDb( $nAutoExpireDays ) {
		$nAutoExpire = $nAutoExpireDays*DAY_IN_SECONDS;
		if ( $nAutoExpire > 0 ) {
			$this->deleteRowsOlderThan( Services::Request()->ts() - $nAutoExpire );
		}
		return $this;
	}

	/**
	 * @param int $nTimeStamp
	 * @return bool
	 */
	public function deleteRowsOlderThan( $nTimeStamp ) {
		return $this->isReady() && $this->getQueryDeleter()
										->addWhereOlderThan( $nTimeStamp )
										->query();
	}

	/**
	 * @return string[]
	 */
	public function getColumnsActual() {
		if ( empty( $this->aColActual ) ) {
			$this->aColActual = Services::WpDb()->getColumnsForTable( $this->getTable(), 'strtolower' );
		}
		return is_array( $this->aColActual ) ? $this->aColActual : [];
	}

	/**
	 * @return string[]
	 */
	public function getColumnsDefinition() {
		return is_array( $this->aColDef ) ? $this->aColDef : $this->getDefaultColumnsDefinition();
	}

	/**
	 * @return string
	 */
	public function getTable() {
		return Services::WpDb()->getPrefix().esc_sql( $this->getTableSlug() );
	}

	/**
	 * @return string
	 */
	protected function getTableSlug() {
		return empty( $this->sTable ) ? $this->getDefaultTableName() : $this->sTable;
	}

	/**
	 * @return Insert|mixed
	 */
	public function getQueryInserter() {
		$sClass = $this->getNamespace().'Insert';
		/** @var Insert $o */
		$o = new $sClass();
		return $o->setDbH( $this );
	}

	/**
	 * @return Delete|mixed
	 */
	public function getQueryDeleter() {
		$sClass = $this->getNamespace().'Delete';
		/** @var Delete $o */
		$o = new $sClass();
		return $o->setDbH( $this );
	}

	/**
	 * @return Select|mixed
	 */
	public function getQuerySelector() {
		$sClass = $this->getNamespace().'Select';
		/** @var Select $o */
		$o = new $sClass();
		return $o->setDbH( $this )
				 ->setResultsAsVo( true );
	}

	/**
	 * @return Update|mixed
	 */
	public function getQueryUpdater() {
		$sClass = $this->getNamespace().'Update';
		/** @var Update $o */
		$o = new $sClass();
		return $o->setDbH( $this );
	}

	/**
	 * @return EntryVO|mixed
	 */
	public function getVo() {
		$sClass = $this->getNamespace().'EntryVO';
		return new $sClass();
	}

	/**
	 * @return string
	 */
	public function getSqlCreate() {
		return empty( $this->sSqlCreate ) ? $this->getDefaultCreateTableSql() : $this->sSqlCreate;
	}

	/**
	 * @param string $sCol
	 * @return bool
	 */
	public function hasColumn( $sCol ) {
		return in_array( strtolower( $sCol ), array_map( 'strtolower', $this->getColumnsActual() ) );
	}

	/**
	 * @return $this
	 * @throws \Exception
	 */
	protected function tableCreate() {
		$sSql = $this->getSqlCreate();
		if ( empty( $sSql ) ) {
			throw new \Exception( 'Table Create SQL is empty' );
		}
		$oDB = Services::WpDb();
		$sSql = sprintf( $sSql, $this->getTable(), $oDB->getCharCollate() );
		$this->isTable() ? $oDB->dbDelta( $sSql ) : $oDB->doSql( $sSql );
		return $this;
	}

	/**
	 * @param bool $bTruncate
	 * @return bool
	 */
	public function tableDelete( $bTruncate = false ) {
		$table = $this->getTable();
		$oDB = Services::WpDb();
		$mResult = !$this->isTable() ||
				   ( $bTruncate ? $oDB->doTruncateTable( $table ) : $oDB->doDropTable( $table ) );
		$this->reset();
		return $mResult !== false;
	}

	/**
	 * @return bool
	 */
	public function tableExists() {
		return Services::WpDb()->getIfTableExists( $this->getTable() );
	}

	/**
	 * @return $this
	 * @throws \Exception
	 */
	public function tableInit() {
		if ( !$this->isReady() ) {

			$this->tableCreate();

			if ( !$this->isReady( true ) ) {
				$this->deleteTable();
				$this->tableCreate();
			}
		}
		return $this;
	}

	/**
	 * @param int $nRowsLimit
	 * @return $this;
	 */
	public function tableTrimExcess( $nRowsLimit ) {
		try {
			$this->getQueryDeleter()
				 ->deleteExcess( $nRowsLimit );
		}
		catch ( \Exception $oE ) {
		}
		return $this;
	}

	/**
	 * @param bool $bReTest
	 * @return bool
	 */
	public function isReady( $bReTest = false ) {
		if ( $bReTest ) {
			$this->reset();
		}

		if ( !isset( $this->bIsReady ) ) {
			try {
				$this->bIsReady = $this->isTable() && $this->verifyTableStructure();
			}
			catch ( \Exception $e ) {
				$this->bIsReady = false;
			}
		}

		return $this->bIsReady;
	}

	/**
	 * @param string[] $aColumns
	 * @return $this
	 */
	public function setColumnsDefinition( $aColumns ) {
		$this->aColDef = $aColumns;
		return $this;
	}

	/**
	 * @param string $sSqlCreate
	 * @return $this
	 */
	public function setSqlCreate( $sSqlCreate ) {
		$this->sSqlCreate = $sSqlCreate;
		return $this;
	}

	/**
	 * @param string $sTable
	 * @return $this
	 */
	public function setTable( $sTable ) {
		$this->sTable = $sTable;
		return $this;
	}

	/**
	 * @return string[]
	 */
	protected function getDefaultColumnsDefinition() {
		return [];
	}

	/**
	 * @return string
	 */
	protected function getDefaultCreateTableSql() {
		return '';
	}

	/**
	 * @return string
	 */
	protected function getDefaultTableName() {
		return '';
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	protected function verifyTableStructure() {
		$aColDef = array_map( 'strtolower', $this->getColumnsDefinition() );
		if ( empty( $aColDef ) ) {
			throw new \Exception( 'Could not verify table structure as no columns definition provided' );
		}

		$aColActual = $this->getColumnsActual();
		return ( count( array_diff( $aColActual, $aColDef ) ) <= 0
				 && ( count( array_diff( $aColDef, $aColActual ) ) <= 0 ) );
	}

	/**
	 * @return string
	 */
	private function getNamespace() {
		try {
			$sNS = ( new \ReflectionClass( $this ) )->getNamespaceName();
		}
		catch ( \Exception $oE ) {
			$sNS = __NAMESPACE__;
		}
		return rtrim( $sNS, '\\' ).'\\';
	}

	/**
	 * @return $this
	 */
	private function reset() {
		unset( $this->bIsReady );
		unset( $this->aColActual );
		return $this;
	}

	/**
	 * @return bool
	 * @deprecated 9.0.4
	 */
	public function isTable() {
		return Services::WpDb()->getIfTableExists( $this->getTable() );
	}

	/**
	 * @param bool $bTruncate
	 * @return bool
	 * @deprecated 9.0.4
	 */
	public function deleteTable( $bTruncate = false ) {
		$table = $this->getTable();
		$oDB = Services::WpDb();
		$mResult = !$this->isTable() ||
				   ( $bTruncate ? $oDB->doTruncateTable( $table ) : $oDB->doDropTable( $table ) );
		$this->reset();
		return $mResult !== false;
	}
}