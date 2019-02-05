<?php
/**
 * Hooks for vhv skin
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

class vhvHooks {

	protected static $anchorID = 0;

	/**
	 * Setting up parser functions
	 *
	 * @param $parser Parser current parser
	 */
	static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'TOC', 'vhvHooks::TOC' );
		$parser->setHook( 'legend', 'vhvHooks::legend' );
		$parser->setHook( 'footer', 'vhvHooks::footer' );
		$parser->setHook( 'accordion', 'vhvHooks::buildAccordion' );
		$parser->setHook( 'label', 'vhvHooks::buildLabel' );

		if ( true === $GLOBALS['wgvhvSkinUseBtnParser'] ) {
			$parser->setHook( 'btn', 'vhvHooks::buildButtons' );
		}

		$parser->setFunctionHook( 'vhvhide', 'vhvHooks::setHiddenElements' );
		$parser->setFunctionHook( 'vhvbodyclass', 'vhvHooks::addBodyclass' );

		return true;
	}
	
	/**
	 * Customizing registration
	 */
	public static function onRegistration() {
		global $wgvhvSkinCustomizedBootstrap, $wgResourceModules;
	
		/* Load customized bootstrap files */
		if( isset( $wgvhvSkinCustomizedBootstrap ) && ! is_null( $wgvhvSkinCustomizedBootstrap ) ) {
			$wgResourceModules['skins.vhv.bootstrap.styles']['localBasePath'] = $wgvhvSkinCustomizedBootstrap['localBasePath'];
			$wgResourceModules['skins.vhv.bootstrap.styles']['remoteExtPath'] = $wgvhvSkinCustomizedBootstrap['remoteExtPath'];
			unset( $wgResourceModules['skins.vhv.bootstrap.styles']['remoteSkinPath'] );
			$wgResourceModules['skins.vhv.bootstraptheme.styles']['localBasePath'] = $wgvhvSkinCustomizedBootstrap['localBasePath'];
			$wgResourceModules['skins.vhv.bootstraptheme.styles']['remoteExtPath'] = $wgvhvSkinCustomizedBootstrap['remoteExtPath'];
			unset( $wgResourceModules['skins.vhv.bootstraptheme.styles']['remoteSkinPath'] );
			$wgResourceModules['skins.vhv.bootstrap.scripts']['localBasePath'] = $wgvhvSkinCustomizedBootstrap['localBasePath'];
			$wgResourceModules['skins.vhv.bootstrap.scripts']['remoteExtPath'] = $wgvhvSkinCustomizedBootstrap['remoteExtPath'];
			unset( $wgResourceModules['skins.vhv.bootstrap.scripts']['remoteSkinPath'] );
		}
	}

	/**
	 * GetPreferences hook
	 *
	 * Adds vhv-releated items to the preferences
	 *
	 * @param $user User current user
	 * @param $defaultPreferences array list of default user preference controls
	 */
	public static function onGetPreferences( $user, &$defaultPreferences ) {
		$defaultPreferences['vhv-advanced'] = array(
			'type' => 'toggle',
			'label-message' => 'prefs-vhv-advanced-desc',
			'section' => 'rendering/vhv-advanced',
			'help-message' => 'prefs-vhv-advanced-help'
		);
		return true;
	}

	/**
	 * Enable TOC
	 */
	static function TOC( $input, array $args, Parser $parser, PPFrame $frame ) {
		return array( '<div class="vhv-toc">' . $input . '</div>' );
	}

	/**
	 * Enable use of <legend> tag
	 */
	static function legend( $input, array $args, Parser $parser, PPFrame $frame ) {
		return array( '<legend>' . $input . '</legend>', "markerType" => 'nowiki' );
	}

	/**
	 * Enable use of <footer> tag
	 */
	static function footer( $input, array $args, Parser $parser, PPFrame $frame ) {
		return array( '<footer>' . $input . '</footer>', "markerType" => 'nowiki' );
	}

	/**
	 * Set elements that should be hidden
	 *
	 * @param $parser Parser current parser
	 * @return string
	 */
	static function setHiddenElements( Parser $parser ) {
		global $wgvhvSkinHideAll, $wgvhvSkinHideable;
		$parser->disableCache();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < func_num_args(); $i++ ) {
			if ( in_array ( func_get_arg( $i ), $wgvhvSkinHideable ) ) {
				$wgvhvSkinHideAll[] = func_get_arg( $i );
			}
		}
		return '';
	}

	/**
	 * Add classes to body
	 *
	 * @param $parser Parser current parser
	 * @return string
	 */
	static function addBodyclass( Parser $parser ) {
		$parser->disableCache();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < func_num_args(); $i++ ) {
			$GLOBALS['wgvhvSkinAdditionalBodyClasses'][] = func_get_arg( $i );
		}
		return '';
	}

	/**
	 * Build accordeon
	 *
	 * @param $input string
	 * @param $args array tag arguments
	 * @param $parser Parser current parser
	 * @param $frame PPFrame current frame
	 * @return string
	 */
	static function buildAccordion( $input, array $args, Parser $parser, PPFrame $frame ) {
		static::$anchorID++;
		$parent = $parser->recursiveTagParse( $args['parent'], $frame );
		$panel = '
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#' . $parent . '" href="#' . $parent . static::$anchorID . '">
							' . $parser->recursiveTagParse( $args['heading'], $frame ) . '
						</a>
					</h4>
				</div>
				<div id="' . $parent . static::$anchorID . '" class="panel-collapse collapse">
					<div class="panel-body">
			' . $parser->recursiveTagParse( $input, $frame ) . '
					</div>
				</div>
			</div>';
		return $panel;
	}

	/**
	 * Build label
	 * @param $input string
	 * @param $args array tag arguments
	 * @param $parser Parser current parser
	 * @param $frame PPFrame current frame
	 * @return string
	 */
	static function buildLabel( $input, array $args, Parser $parser, PPFrame $frame ) {
		return '<label>' . $parser->recursiveTagParse( $input ) . '</label>';
	}

	/**
	 * Build buttons, groups of buttons and dropdowns
	 *
	 * @param $input string
	 * @param $args array tag arguments
	 * @param $parser Parser current parser
	 * @param $frame PPFrame current frame
	 * @return string
	 */
	static function buildButtons( $input, array $args, Parser $parser, PPFrame $frame ) {
		$sizes = array(
			'large' => 'btn-lg',
			'lg' => 'btn-lg',
			'small' => 'btn-sm',
			'sm' => 'btn-sm',
			'mini' => 'btn-xs',
			'xs' => 'btn-xs'
			);
		$renderedButtons = '';

		$buttongroups = preg_split( '/\n{2,}/', $input );

		// set standard classes for all buttons in the group
		if ( !isset( $args['class'] ) ) {
			$args['class'][] = 'btn btn-default';
		}
		else {
			$args['class'] = explode( ' ', $args['class'] );
		}
		if ( isset( $args['size'] ) ) {
			if ( isset( $sizes[$args['size']] ) ) {
				$args['class'][] = $sizes[$args['size']];
			}
		}

		foreach ( $buttongroups as $buttongroup ) {
			$buttons = array();
			$buttons = vhvHooks::parseButtons( $buttongroup, $parser, $frame );
			$renderedButtons .= vhvHooks::renderButtons( $buttons, $args );
		}

		// more than one buttongroup build a toolbar
		if ( count( $buttongroups ) > 1 ) {
			$renderedButtons = '<div class="btn-toolbar">' . $renderedButtons . '</div>';
		}

		return $renderedButtons;
	}


	/**
	 * Parse string input into array
	 *
	 * @param $buttongroup string one or more buttons
	 * @param $parser Parser current parser
	 * @param $frame PPFrame current frame
	 * @return array
	 */
	static function parseButtons( $buttongroup, Parser $parser, $frame ) {
		$buttons = array();
		$lines = explode( "\n", $buttongroup );

		foreach ( $lines as $line ) {
			// empty line
			if ( trim( $line ) == "" ) { 
				continue;
			}
			
			// simple buttons
			if ( strpos( $line, '*' ) !== 0 ) {
				$buttons = array_merge( $buttons, vhvHooks::parseButtonLink( trim( $line ), $parser, $frame ) );
				end( $buttons );
				$currentparentkey = key($buttons);
			}
				
			// dropdown menus
			else {
				// no parent set?
				if ( count( $buttons ) == 0 ) {
					continue;
				}
				
				$cleanline = ltrim( $line, '*' );
				$cleanline = trim( $cleanline );
				if ( !isset( $buttons[$currentparentkey]['items'] ) ) {
					$buttons[$currentparentkey]['items'] = array();
				}
				$buttons[$currentparentkey]['items'] = array_merge( $buttons[$currentparentkey]['items'], vhvHooks::parseButtonLink( $cleanline, $parser, $frame ) );
			}
		}
		return $buttons;
	}


	/**
	 * Parse specific link
	 *
	 * @param $line string
	 * @param $parser Parser current parser
	 * @param $frame Frame current frame
	 * @return array
	 */
	static function parseButtonLink( $line, $parser, $frame ) {

		$extraAttribs = array();
		$href_implicit = false;
		$active = false;
				
		// semantic queries
		if ( strpos( $line, '{{#ask:' ) === 0 ) {
			if ( $parser->getTitle() instanceof Title ) {
				$semanticQuery = substr( $line, 7, -2 );
				$semanticHitNumber = $parser->recursiveTagParse( '{{#ask:' . $semanticQuery . '|format=count}}', false );
				if ( !is_numeric( $semanticHitNumber ) || $semanticHitNumber < 1 ) {
					return array( array( 'text' => $semanticQuery, 'href' => 'INVALID QUERY' ) );
				}
				$semanticHits = $parser->recursiveTagParse( '{{#ask:' . $semanticQuery . '|format=list|link=none}}', false );
				$semanticHits = explode( ',', $semanticHits );
				$semanticLinks = array();
				foreach ( $semanticHits as $semanticHit ) {
					$semanticLink = vhvHooks::parseButtonLink( $semanticHit, $parser, $frame );
					$semanticLinks[] = $semanticLink[0];
				}
				return $semanticLinks;
			}
			else {
				$text = 'broken';
			}
		}

		$line = explode( '|', $line );
		foreach ( $line as &$single_line ) {
			$single_line = trim( $single_line );
		}

		// is the text explicitly set?
		$href = $line[0];
		if ( isset( $line[1] ) && $line[1] != "" ) {
			$text = $line[1];
		}
		else {
			$href_implicit = true;
			$text = $line[0];
		}

		// parse text
		$msgText = wfMessage( $text )->inContentLanguage();
		if ( $msgText->exists() ) {
			$text = $msgText->parse();
		}
		else {
			if ( $parser->getTitle() instanceof Title ) {
				$text = $parser->recursiveTagParse( $text, $frame );
			}
			else {
				$text = 'INVALID-TITLE/PARSER-BROKEN';
			}
		}

		// parse href
		$msgLink = wfMessage( $href )->inContentLanguage();
		if ( $msgLink->exists() ) {
			$href = $msgLink->parse();
		}
		else {
			if ( $parser->getTitle() instanceof Title ) {
				$href = $parser->replaceVariables( $href, $frame );
			}
			else {
				$href = 'INVALID-HREF/PARSER-BROKEN';
			}
		}

		if ( preg_match( '/^(?i:' . wfUrlProtocols() . ')/', $href ) ) {
			// Parser::getExternalLinkAttribs won't work here because of the Namespace things
			global $wgNoFollowLinks, $wgNoFollowDomainExceptions;
			if ( $wgNoFollowLinks && !wfMatchesDomainList( $href, $wgNoFollowDomainExceptions ) ) {
				$extraAttribs['rel'] = 'nofollow';
			}

			global $wgExternalLinkTarget;
			if ( $wgExternalLinkTarget ) {
				$extraAttribs['target'] = $wgExternalLinkTarget;
			}
		} else {
			$title = Title::newFromText( $href );
			if ( $title ) {
				if( $title->equals( $parser->getTitle() ) ) {
					$active = true;
				}
				$title = $title->fixSpecialName();
				$href = $title->getLinkURL();
			} else {
				// allow empty first argument
				if( $href != '' ) {
					$href = 'INVALID-TITLE:' . $href;
				}
			}
		}
		if ( isset( $line[2] ) && $line[2] != "" ) {
			$extraAttribs['class'] = $line[2];
		}

		$link = array(
				'html' => $text,
				'href' => $href,
				'href_implicit' => $href_implicit,
				'active' => $active
			);
		if( $line[0] != '' ) {
			$link['id'] = 'n-' . Sanitizer::escapeId( strtr( $line[0], ' ', '-' ), 'noninitial' );
		}
		$link = array_merge( $link, $extraAttribs );
		return array( $link );
	}

	/**
	 * Render Buttons
	 *
	 * @param $buttons array
	 * @param $options Array
	 * @return String
	 */
	static function renderButtons( $buttons, $options = array() ) {
		$renderedButtons = '';
		$groupclass = array();
		if ( isset( $options['class'] ) ) {
			if ( !is_array( $options['class'] ) ) {
				$options['class'] = explode( ' ', $options['class'] );
			}
			$groupclass = $options['class'];
		}
		$currentwrapperclass = '';

		// set wrapper
		$wrapper = 'div';
		if ( isset( $options['wrapper'] ) ) { 
			$wrapper = $options['wrapper'];
		}

		foreach ( $buttons as $button ) {
			$btnoptions = array();
			// set classes for specific button
			// explicit classes for the specific line?
			if ( isset( $button['class'] ) ) {
				$button['class'] = explode( ' ', $button['class'] );
			}
			else {
				$button['class'] = $groupclass;
			}
			foreach ( $button['class'] as $btnclass ) {
				if ( strpos( $btnclass, 'btn' ) === 0 ) {
					$button['class'][] = 'btn';
					break;
				}
			}

			// set wrapper class
			if ( isset( $options['wrapperclass'] ) ) {
				$wrapperclass = $options['wrapperclass'];
			}
			else {
				if ( in_array( 'btn', $button['class'] ) === false ) {
					$wrapperclass = 'dropdown';
				}
				else {
					$wrapperclass = 'btn-group';
				}
			}

			$button['class'] = implode( ' ', array_unique( $button['class'] ) );

			// if aria-attributes are set, add them
			if ( isset( $options['aria-controls'] ) ) {
				$button['aria-controls'] = $options['aria-controls'];
			}
				
			if ( isset( $options['aria-expanded'] ) ) {
				$button['aria-expanded'] = $options['aria-expanded'];
			}
				
			// if data-target attribute is set, add it
			if ( isset( $options['data-target'] ) ) {
				$button['data-target'] = $options['data-target'];
			}
				
			// if data-dismiss attribute is set, add it
			if ( isset( $options['data-dismiss'] ) ) {
				$button['data-dismiss'] = $options['data-dismiss'];
			}
				
			// if data-placement attribute is set, add it
			if ( isset( $options['data-placement'] ) ) {
				$button['data-placement'] = $options['data-placement'];
			}
				
			// if data-slide attribute is set, add it
			if ( isset( $options['data-slide'] ) ) {
				$button['data-slide'] = $options['data-slide'];
			}

			// if title attribute is set, add it
			if ( isset( $options['title'] ) ) {
				$button['title'] = $options['title'];
			}
				
			// if data-toggle attribute is set, unset wrapper and add attribute and toggle-class
			if ( isset( $options['data-toggle'] ) ) {
				$wrapper = '';
				$button['data-toggle'] = $options['data-toggle'];
				$button['class'] .= ' ' . $options['data-toggle'] . '-toggle';
			}
			
			// if html is not set, use text and sanitize it
			if ( !isset( $button['html'] ) ) {
				if( isset( $button['text'] ) ) {
					$button['html'] = htmlspecialchars( $button['text'] );
				}
				else {
					$button['html'] = '#';
				}
			}
			
			// if fa attribute is set, add fa-icon to buttons
			if ( isset( $options['fa'] ) ) {
				$button['html'] = '<span class="fa fa-' . $options['fa'] . '"></span> ' . $button['html'];
			}

			// if glyphicon or icon attribute is set, add icon to buttons
			if ( isset( $options['icon'] ) ) {
				$options['glyphicon'] = $options['icon'];
			}
			if ( isset( $options['glyphicon'] ) ) {
				$button['html'] = '<span class="glyphicon glyphicon-' . $options['glyphicon'] . '"></span> ' . $button['html'];
			}

			// render wrapper
			if ( 
				( ( $currentwrapperclass != $wrapperclass || isset( $button['items'] ) ) && $wrapper != '' ) 
				|| $wrapper == 'li' 
			) {
				if ( $currentwrapperclass != '' ) {
					$renderedButtons .= '</' . $wrapper . '>';
				}
				$renderedButtons .= '<' . $wrapper . ' class="' . $wrapperclass;
				if ( isset( $button['active'] ) && $button['active'] === true ) {
					$renderedButtons .= ' active';
				}
				if ( isset( $options['wrapperid'] ) ) {
					$renderedButtons .= '" id="' . $options['wrapperid'];
				}
				$renderedButtons .= '">';
				$currentwrapperclass = $wrapperclass;
			}

			// dropdown
			if ( isset( $button['items'] ) ) {
				if ( isset( $options['dropdownclass'] ) ) {
					$renderedButtons .= vhvHooks::buildDropdown( $button, $options['dropdownclass'] );
				}
				else {
					$renderedButtons .= vhvHooks::buildDropdown( $button );
				}
			}

			// simple button
			else {
				$renderedButtons .= vhvHooks::makeLink( $button, $btnoptions );
			}
		}
		// close wrapper
		if ( $wrapper != '' ) {
			$renderedButtons .= '</' . $wrapper . '>';
		}
		return $renderedButtons;
	}


	/**
	 * Build dropdown
	 *
	 * @param $dropdown array
	 * @return String
	 */
	static function buildDropdown( $dropdown, $dropdownclass = '' ) {
		$renderedDropdown = '';

		// split dropdown
		if ( isset( $dropdown['href_implicit'] ) && $dropdown['href_implicit'] === false ) {
			$renderedDropdown .= vhvHooks::makeLink( $dropdown );
			$caret = array(
				'class' => 'dropdown-toggle ' . $dropdown['class'],
				'href' => '#',
				'html' => '&zwnj;<b class="caret"></b>',
				'data-toggle' => 'dropdown'
				);
			$renderedDropdown .= vhvHooks::makeLink( $caret );
		}

		// ordinary dropdown
		else {
			$dropdown['class'] .= ' dropdown-toggle';
			$dropdown['data-toggle'] = 'dropdown';
			$dropdown['html'] = $dropdown['html'] . ' <b class="caret"></b>';
			$dropdown['href'] = '#';
			$renderedDropdown .= vhvHooks::makeLink( $dropdown );
		}

		$renderedDropdown .= vhvHooks::buildDropdownMenu( $dropdown['items'], $dropdownclass );
		return $renderedDropdown;
	}


	/**
	 * Build dropdown-menu (ul)
	 *
	 * @param $dropdownmenu array
	 * @return String
	 */
	static function buildDropdownMenu( $dropdownmenu, $dropdownclass ) {
		$renderedMenu = '<ul class="dropdown-menu ' . $dropdownclass . '" role="menu">';

		foreach ( $dropdownmenu as $entry ) {
			// divider
			if ( ( !isset( $entry['text'] ) || $entry['text'] == "" ) // no 'text'
				&& ( !isset( $entry['html'] ) || $entry['html'] == "" ) // and no 'html'
			) {
				$renderedMenu .= '<li class="divider" />';
			}

			// standard menu entry
			else {
				$entry['tabindex'] = '-1';
				$renderedMenu .= vhvHooks::makeListItem( $entry );
			}
		}

		$renderedMenu .= '</ul>';
		return $renderedMenu;
	}


	/**
	 * Produce HTML for a link
	 * 
	 * This is a slightly adapted copy of the makeLink function in SkinTemplate.php
	 * -> some of the changed parts are marked by comments //
	 *
	 * @param $item array
	 * @param $options array
	 *
	 * @TODO SkinTemplate's makeLink function has been replaced by Linker::link()
	 * this function should be adapted accordingly or it will likely produce further
	 * misbehavior in the future (see github issue #68)
	 */
	static function makeLink( $item, $options = array() ) {
		// nested links?
		if ( isset( $item['links'] ) ) {
			$item = $item['links'][0];
		}

		if ( isset( $item['text'] ) ) {
			$text = $item['text'];
		} else {
//			$text = $this->translator->translate( isset( $item['msg'] ) ? $item['msg'] : $key );
			$text = '';
		}

		$html = htmlspecialchars( $text );

		// set raw html
		if ( isset( $item['html'] )) {
			$html = $item['html'];
		}

		// set icons for individual buttons (used by some navigational elements)
		if ( isset( $item['icon'] )) {
			$html = '<span class="glyphicon glyphicon-' . $item['icon'] . '"></span> ' . $html;
		}

		if ( isset( $options['text-wrapper'] ) ) {
			$wrapper = $options['text-wrapper'];
			if ( isset( $wrapper['tag'] ) ) {
				$wrapper = array( $wrapper );
			}
			while ( count( $wrapper ) > 0 ) {
				$element = array_pop( $wrapper );
				$html = Html::rawElement( $element['tag'], isset( $element['attributes'] ) ? $element['attributes'] : null, $html );
			}
		}

		// allow empty first argument in the <btn> tag
		if( $item['href'] == '' ) {
			unset( $item['href'] );
			$options['link-fallback'] = 'span';
		}

		if ( isset( $item['href'] ) || isset( $options['link-fallback'] ) ) {
			$attrs = $item;
//			foreach ( array( 'single-id', 'text', 'msg', 'tooltiponly' ) as $k ) {
			foreach ( array( 'single-id', 'text', 'msg', 'tooltiponly', 'href_implicit', 'items', 'icon', 'html', 'tooltip-params' ) as $k ) {
				unset( $attrs[$k] );
			}

			if ( isset( $item['id'] ) && !isset( $item['single-id'] ) ) {
				$item['single-id'] = $item['id'];
			}
			if ( isset( $item['single-id'] ) ) {
				if ( isset( $item['tooltiponly'] ) && $item['tooltiponly'] ) {
					$title = Linker::titleAttrib( $item['single-id'] );
					if ( $title !== false ) {
						$attrs['title'] = $title;
					}
				} else {
					$tip = Linker::tooltipAndAccesskeyAttribs( $item['single-id'] );
					if ( isset( $tip['title'] ) && $tip['title'] !== false ) {
						$attrs['title'] = $tip['title'];
					}
					if ( isset( $tip['accesskey'] ) && $tip['accesskey'] !== false ) {
						$attrs['accesskey'] = $tip['accesskey'];
					}
				}
			}
			if ( isset( $options['link-class'] ) ) {
				if ( isset( $attrs['class'] ) ) {
					$attrs['class'] .= " {$options['link-class']}";
				} else {
					$attrs['class'] = $options['link-class'];
				}
			}
			if ( isset( $attrs['data'] ) && is_array( $attrs['data'] ) ) {
				foreach( $attrs['data'] as $datakey => $datavalue ) {
					$attrs['data-' . $datakey] = $datavalue;
				}
				unset( $attrs['data'] );
			}
			$html = Html::rawElement( isset( $attrs['href'] ) ? 'a' : $options['link-fallback'], $attrs, $html );
		}

		return $html;
	}

	/**
	 * Produce HTML for a list item
	 * 
	 * This is a slightly adapted copy of the makeListItem function in SkinTemplate.php
	 * -> some of the changed parts are marked by comments //
	 *
	 * @param $item array
	 * @param $options array
	 */
	static function makeListItem( $item, $options = array() ) {
		if ( isset( $item['links'] ) ) {
			$html = '';
			foreach ( $item['links'] as $linkKey => $link ) {
				$html .= vhvHooks::makeLink( $link, $options );
			}
		} else {
			$link = $item;
			// These keys are used by makeListItem and shouldn't be passed on to the link
			foreach ( array( 'id', 'class', 'active', 'tag' ) as $k ) {
				unset( $link[$k] );
			}
			if ( isset( $item['id'] ) && !isset( $item['single-id'] ) ) {
				// The id goes on the <li> not on the <a> for single links
				// but makeSidebarLink still needs to know what id to use when
				// generating tooltips and accesskeys.
				$link['single-id'] = $item['id'];
			}
			$html = vhvHooks::makeLink( $link, $options );
		}

		$attrs = array();
		foreach ( array( 'id', 'class' ) as $attr ) {
			if ( isset( $item[$attr] ) ) {
				$attrs[$attr] = $item[$attr];
			}
		}
		if ( isset( $item['active'] ) && $item['active'] ) {
			if ( !isset( $attrs['class'] ) ) {
				$attrs['class'] = '';
			}
			$attrs['class'] .= ' active';
			$attrs['class'] = trim( $attrs['class'] );
		}
		return Html::rawElement( isset( $options['tag'] ) ? $options['tag'] : 'li', $attrs, $html );
	}

	/**
	 * Customize edit section links
	 *
	 * @param $skin Skin current skin
	 * @param $title Title
	 * @param $section String section
	 * @param $tooltip
	 * @param $links Array link details
	 * @param $lang String language
	 *
	 * @todo: make this work with VisualEditor
	 */
	static function onSkinEditSectionLinks( $skin, $title, $section, $tooltip, &$links, $lang = false ) {
		if( 
			$skin->getSkinName() == 'vhv' 
			&& $GLOBALS['wgvhvSkinCustomEditSectionLink'] == true 
		) {
			$icon = wfMessage( 'vhv-editsection-icon' )->inLanguage( $lang )->parse();
			$text = wfMessage( 'vhv-editsection-text' )->inLanguage( $lang )->parse();
			$class = wfMessage( 'vhv-editsection-class' )->inLanguage( $lang )->parse();
			$text = $icon . ( ( $icon != '' ) ? ' ' : '' ) . $text;

			$links['editsection']['text'] = $text;
			$links['editsection']['attribs']['class'] = $class;
			return true;
		}
	}


	/**
	 * Change TOC and page content of file pages to togglable tabs
	 *
	 * @param $outputPage OutputPage
	 */
	public static function onAfterFinalPageOutput( $outputPage ) {
		if( $outputPage->getTitle()->getNamespace() == 6 && $GLOBALS['wgvhvSkinImagePageTOCTabs'] == true ) {
			$out = ob_get_clean();
			$out = str_replace( '<ul id="filetoc">', '<ul id="tw-filetoc" class="nav nav-tabs nav-justified">', $out );
			$out = str_replace( '<li><a href="#file">', '<li class="active"><a href="#file" class="tab-toggle" data-toggle="tab">', $out );
			$out = str_replace( '<a href="#filehistory">', '<a href="#filehistory" class="tab-toggle" data-toggle="tab">', $out );
			$out = str_replace( '<a href="#filelinks">', '<a href="#filelinks" class="tab-toggle" data-toggle="tab">', $out );
			$out = str_replace( '<a href="#metadata">', '<a href="#metadata" class="tab-toggle" data-toggle="tab">', $out );
			$out = str_replace( '<div class="fullImageLink" id="file"', '<div class="tab-content"><div id="file" class="tab-pane fade in active"><div class="fullImageLink"', $out );
			$out = str_replace( '<h2 id="filehistory"', '</div><div id="filehistory" class="tab-pane fade"><h2', $out );
			$out = str_replace( '<h2 id="filelinks"', '</div><div id="filelinks" class="tab-pane fade"><h2', $out );
			$out = str_replace( '<h2 id="metadata"', '</div><div id="metadata" class="tab-pane fade"><h2', $out );
			$out = $out . '</div></div>';
			ob_start();
			echo $out;
		}
		return true;
	}

	/** 
	 * 
	 */
	public static function onMagicWordMagicWords( &$magicWords ) {
		$magicWords[] = 'MAG_NUMBEREDHEADINGS';
		return true;
	}

	public static function onMagicWordwgVariableIDs( &$wgVariableIDs ) {
		$wgVariableIDs[] = 'MAG_NUMBEREDHEADINGS';
		return true;
	}

	public static function onInternalParseBeforeLinks( &$parser, &$text, &$strip_state ) {
		if ( MagicWord::get( 'MAG_NUMBEREDHEADINGS' )->matchAndRemove( $text ) ) {
			$parser->mOptions->setNumberHeadings( true );
		}
		return true;
	}

}
