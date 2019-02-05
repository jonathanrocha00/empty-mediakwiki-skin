<?php
/**
 * vhv - Tweaked version of Vector, using Twitter Bootstrap.

 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

/**
 * Skin subclass for vhv
 * @ingroup Skins
 */
class Skinvhv extends SkinTemplate {
	public $skinname = 'vhv';
	public $stylename = 'vhv';
	public $template = 'vhvTemplate';
	public $useHeadElement = true;
	/**
	 * @var Config
	 */
	private $vhvConfig;
	private $responsiveMode = false;

	public function __construct() {
		$this->vhvConfig = \MediaWiki\MediaWikiServices::getInstance()->getConfigFactory()
			->makeConfig( 'vhv' );
	}

	protected static $bodyClasses = array( 'vhv-animateLayout' );


	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param OutputPage $out Object to initialize
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1' );
		$out->addModules( 'skins.vhv.scripts' );
		if( $this->vhvConfig->get( 'vhvSkinUseTooltips' ) ) {
			$out->addModules( 'skins.vhv.tooltips' );
		}
		if( $out->getUser()->getOption( 'vhv-advanced' ) ) {
			static::$bodyClasses[] = 'advanced';
		}
		Hooks::run( 'SkinvhvAdditionalBodyClasses', array( $this, &$GLOBALS['wgvhvSkinAdditionalBodyClasses'] ) );
		static::$bodyClasses = array_merge( static::$bodyClasses, $GLOBALS['wgvhvSkinAdditionalBodyClasses'] );
	}

	/**
	 * Loads skin and user CSS files.
	 * @param OutputPage $out
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$styles = $GLOBALS['wgvhvSkinStyles'];
		if( $GLOBALS['wgvhvSkinUseAwesome'] === true ) {
			$styles[] = 'skins.vhv.awesome.styles';
		}
		if( $GLOBALS['wgvhvSkinUseBootstrapTheme'] === true ) {
			$styles[] = 'skins.vhv.bootstraptheme.styles';
		}
		if( isset( $GLOBALS['wgCookieWarningEnabled'] ) && $GLOBALS['wgCookieWarningEnabled'] === true ) {
			$styles[] = 'skins.vhv.cookiewarning.styles';
		}
		foreach( $GLOBALS['wgvhvSkinCustomCSS'] as $customstyle ) {
			$styles[] = $customstyle;
		}
		Hooks::run( 'SkinvhvStyleModules', array( $this, &$styles ) );
		$out->addModuleStyles( $styles );
	}

	/**
	 * Adds classes to the body element.
	 *
	 * @param $out OutputPage object
	 * @param &$bodyAttrs Array of attributes that will be set on the body element
	 */
	function addToBodyAttributes( $out, &$bodyAttrs ) {
		if ( isset( $bodyAttrs['class'] ) && strlen( $bodyAttrs['class'] ) > 0 ) {
			$bodyAttrs['class'] .= ' ' . implode( ' ', static::$bodyClasses );
		} else {
			$bodyAttrs['class'] = implode( ' ', static::$bodyClasses );
		}
	}

	/**
	 * Override to pass our Config instance to it
	 * @param string $classname
	 * @param bool|string $repository
	 * @param bool|string $cache_dir
	 * @return QuickTemplate
	 */
	public function setupTemplate( $classname, $repository = false, $cache_dir = false ) {
		return new $classname( $this->vhvConfig );
	}

	/**
	 * Whether the logo should be preloaded with an HTTP link header or not
	 * @since 1.29
	 * @return bool
	 */
	public function shouldPreloadLogo() {
		return true;
	}
}
