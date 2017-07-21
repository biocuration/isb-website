<?php
if ( !class_exists( 'ICWP_WPSF_Render', false ) ):

	class ICWP_WPSF_Render extends ICWP_WPSF_Foundation {

		const TEMPLATE_ENGINE_TWIG = 0;
		const TEMPLATE_ENGINE_PHP = 1;
		const TEMPLATE_ENGINE_HTML = 2;

		/**
		 * @var ICWP_WPSF_Render
		 */
		protected static $oInstance = NULL;

		private function __construct() {}

		/**
		 * @return ICWP_WPSF_Render
		 */
		public static function GetInstance() {
			if ( is_null( self::$oInstance ) ) {
				self::$oInstance = new self();
			}
			return self::$oInstance;
		}

		/**
		 * @var array
		 */
		protected $aRenderVars;

		/**
		 * @var string
		 */
		protected $sTemplatePath;

		/**
		 * @var string
		 */
		protected $sAutoloaderPath;

		/**
		 * @var string
		 */
		protected $sTemplate;

		/**
		 * @var int
		 */
		protected $nTemplateEngine;

		/**
		 * @var Twig_Environment
		 */
		protected $oTwigEnv;

		/**
		 * @var Twig_Loader_Filesystem
		 */
		protected $oTwigLoader;

		/**
		 * @return string
		 */
		public function render() {

			switch( $this->getTemplateEngine() ) {

				case self::TEMPLATE_ENGINE_TWIG :
					$sOutput = $this->renderTwig();
					break;

				case self::TEMPLATE_ENGINE_HTML :
					$sOutput = $this->renderHtml();
					break;

				default:
					$sOutput = $this->renderPhp();
					break;
			}
			return $sOutput;
		}

		/**
		 * @return string
		 */
		private function renderHtml() {
			ob_start();
			@include( $this->getTemplateRoot().ltrim( $this->getTemplate(), DIRECTORY_SEPARATOR ) );
			$sContents = ob_get_contents();
			ob_end_clean();
			return $sContents;
		}

		/**
		 * @return string
		 */
		private function renderPhp() {
			if ( count( $this->getRenderVars() ) > 0 ) {
				extract( $this->getRenderVars() );
			}

			$sTemplate = $this->getTemplateRoot() . ltrim( $this->getTemplate(), DIRECTORY_SEPARATOR );
			if ( $this->loadFileSystemProcessor()->isFile( $sTemplate ) ) {
				ob_start();
				include( $sTemplate );
				$sContents = ob_get_contents();
				ob_end_clean();
			}
			else {
				$sContents = 'Error: Template file not found: ' . $sTemplate;
			}

			return $sContents;
		}

		/**
		 * @deprecated
		 * @return string
		 * @throws Exception
		 */
		private function renderTwig() {
			throw new Exception( 'Twig codebase has been removed since version 5.3.3. Render using PHP instead.' );
			$oTwig = $this->getTwigEnvironment();
			return $oTwig->render( $this->getTemplate(), $this->getRenderVars() );
		}

		/**
		 */
		public function display() {
			echo $this->render();
			return $this;
		}

		/**
		 */
		protected function autoload() {
			if ( !class_exists( 'Twig_Autoloader', false ) ) {
				require_once( $this->sAutoloaderPath );
				Twig_Autoloader::register();
			}
		}

		/**
		 * @return $this
		 */
		public function clearRenderVars() {
			return $this->setRenderVars( array() );
		}

		/**
		 * @return Twig_Environment
		 */
		protected function getTwigEnvironment() {
			if ( !isset( $this->oTwigEnv )  ) {
				$this->autoload();
				$this->oTwigEnv = new Twig_Environment( $this->getTwigLoader(),
					array(
						'debug' => true,
						'strict_variables' => true,
					)
				);
			}
			return $this->oTwigEnv;
		}

		/**
		 * @return Twig_Loader_Filesystem
		 */
		protected function getTwigLoader() {
			if ( !isset( $this->oTwigLoader )  ) {
				$this->autoload();
				$this->oTwigLoader = new Twig_Loader_Filesystem( $this->getTemplateRoot() );
			}
			return $this->oTwigLoader;
		}

		/**
		 * @return string
		 */
		public function getTemplate() {
			$this->sTemplate = $this->loadDataProcessor()->addExtensionToFilePath( $this->sTemplate, $this->getEngineStub() );
			return $this->sTemplate;
		}

		/**
		 * @return int
		 */
		public function getTemplateEngine() {
			if ( !isset( $this->nTemplateEngine )
				 || !in_array( $this->nTemplateEngine, array( self::TEMPLATE_ENGINE_TWIG, self::TEMPLATE_ENGINE_PHP, self::TEMPLATE_ENGINE_HTML ) ) ) {
				$this->nTemplateEngine = self::TEMPLATE_ENGINE_PHP;
			}
			return $this->nTemplateEngine;
		}

		/**
		 * @param string $sTemplate
		 * @return string
		 */
		public function getTemplateExists( $sTemplate = '' ) {
			$sFullPath = $this->getTemplateFullPath( $sTemplate );
			return $this->loadFileSystemProcessor()->exists( $sFullPath );
		}

		/**
		 * @param string $sTemplate
		 * @return string
		 */
		public function getTemplateFullPath( $sTemplate = '' ) {
			if ( empty( $sTemplate ) ) {
				$sTemplate = $this->getTemplate();
			}
			$sTemplate = $this->loadDataProcessor()->addExtensionToFilePath( $sTemplate, $this->getEngineStub() );
			return path_join( $this->getTemplateRoot(), $sTemplate );
		}

		/**
		 * @return string
		 */
		public function getTemplateRoot() {
			$sPath = rtrim( $this->sTemplatePath, DIRECTORY_SEPARATOR );
			$sStub = $this->getEngineStub();
			if ( !preg_match( sprintf( '#%s$#', $sStub ), $sPath ) ) {
				$sPath = $sPath.DIRECTORY_SEPARATOR.$sStub;
			}
			return $sPath.DIRECTORY_SEPARATOR;
		}

		/**
		 * @return string
		 */
		public function getRenderVars() {
			return $this->aRenderVars;
		}

		/**
		 * @param array $aVars
		 * @return $this
		 */
		public function setRenderVars( $aVars ) {
			$this->aRenderVars = $aVars;
			return $this;
		}

		/**
		 * @param string $sPath
		 * @return $this
		 */
		public function setAutoloaderPath( $sPath ) {
			$this->sAutoloaderPath = $sPath;
			return $this;
		}

		/**
		 * @param string $sPath
		 * @return $this
		 */
		public function setTemplate( $sPath ) {
//			if ( !preg_match( '#\.twig$#', $sPath ) ) {
//				$sPath = $sPath . '.twig';
//			}
			$this->sTemplate = $sPath;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function setTemplateEngineHtml() {
			return $this->setTemplateEngine( self::TEMPLATE_ENGINE_HTML );
		}

		/**
		 * @return $this
		 */
		public function setTemplateEnginePhp() {
			return $this->setTemplateEngine( self::TEMPLATE_ENGINE_PHP );
		}

		/**
		 * @return $this
		 */
		public function setTemplateEngineTwig() {
			return $this->setTemplateEngine( self::TEMPLATE_ENGINE_TWIG );
		}

		/**
		 * @param int $nEngine
		 * @return $this
		 */
		protected function setTemplateEngine( $nEngine ) {
			$this->nTemplateEngine = $nEngine;
			return $this;
		}

		/**
		 * @param string $sPath
		 * @return $this
		 */
		public function setTemplateRoot( $sPath ) {
			$this->sTemplatePath = $sPath;
			return $this;
		}

		/**
		 * @return string
		 */
		private function getEngineStub() {
			switch( $this->getTemplateEngine() ) {

				case self::TEMPLATE_ENGINE_TWIG:
					$sStub = 'twig';
					break;

				case self::TEMPLATE_ENGINE_HTML:
					$sStub = 'html';
					break;

				case self::TEMPLATE_ENGINE_PHP:
					$sStub = 'php';
					break;

				default:
					$sStub = 'php';
					break;
			}
			return $sStub;
		}
	}

endif;