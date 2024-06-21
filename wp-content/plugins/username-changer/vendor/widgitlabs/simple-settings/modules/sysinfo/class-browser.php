<?php
/**
 * Browser detection library
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details at:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * Originally by Chris Schuld (http://chrisschuld.com/)
 * Modified by Chris Christoff
 * Maintained by Daniel J Griffiths (https://evertiro.com/)
 *
 * Typical Usage:
 *
 *   $browser = new Browser();
 *   if( $browser->get_browser() == Browser::browser_firefox && $browser->get_version() >= 2 ) {
 *    echo 'You have FireFox version 2 or greater';
 *   }
 *
 * User Agents Sampled from: http://www.useragentstring.com/
 *
 * This implementation is based on the original work from Gary White
 * http://apptools.com/phptools/browser/
 *
 * @package     Widgit\SimpleSettings\Browser
 * @since       2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class for determining browser information
 *
 * @since       1.0.0
 */
class Browser {


	/**
	 * The user agent
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $agent The user agent
	 */
	public $agent = '';


	/**
	 * The browser name
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_name The browser name
	 */
	public $browser_name = '';


	/**
	 * The browser version
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $version The browser version
	 */
	public $version = '';


	/**
	 * The users platform
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform The users platform
	 */
	public $platform = '';


	/**
	 * The users OS
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $os The users OS
	 */
	public $os = '';


	/**
	 * Whether or not the user is using AOL
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         bool $is_aol Whether or not the user is using AOL
	 */
	public $is_aol = false;


	/**
	 * Whether or not the user is using a mobile device
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         bool $is_mobile Whether or not the user is using a mobile device
	 */
	public $is_mobile = false;


	/**
	 * Whether or not the user is a robot
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         bool $is_robot Whether or not the user is a robot
	 */
	public $is_robot = false;


	/**
	 * The AOL version
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $aol_version The AOL version
	 */
	public $aol_version = '';


	/**
	 * The string for unknown browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_unknown The string for unknown browsers
	 */
	public $browser_unknown = 'unknown';


	/**
	 * The string for unknown versions
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $version_unknown The string for unknown versions
	 */
	public $version_unknown = 'unknown';


	/**
	 * The string for Opera browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_opera The string for Opera browsers
	 */
	public $browser_opera = 'Opera';


	/**
	 * The string for Opera mini browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_opera_mini The string for Opera browsers
	 */
	public $browser_opera_mini = 'Opera Mini';


	/**
	 * The string for WebTV browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_webtv The string for WebTV browsers
	 */
	public $browser_webtv = 'WebTV';


	/**
	 * The string for IE browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_ie The string for IE browsers
	 */
	public $browser_ie = 'Internet Explorer';


	/**
	 * The string for Pocket IE browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_pocket_ie The string for Pocket IE browsers
	 */
	public $browser_pocket_ie = 'Pocket Internet Explorer';


	/**
	 * The string for Konqueror browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_konqueror The string for Konqueror browsers
	 */
	public $browser_konqueror = 'Konqueror';


	/**
	 * The string for iCab browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_icab The string for iCab browsers
	 */
	public $browser_icab = 'iCab';


	/**
	 * The string for OmniWeb browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_omniweb The string for OmniWeb browsers
	 */
	public $browser_omniweb = 'OmniWeb';


	/**
	 * The string for Firebird browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_firebird The string for Firebird browsers
	 */
	public $browser_firebird = 'Firebird';


	/**
	 * The string for Firefox browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_firefox The string for Firefox browsers
	 */
	public $browser_firefox = 'Firefox';


	/**
	 * The string for Iceweasel browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_iceweasel The string for Iceweasel browsers
	 */
	public $browser_iceweasel = 'Iceweasel';


	/**
	 * The string for Shiretoko browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_shiretoko The string for Shiretoko browsers
	 */
	public $browser_shiretoko = 'Shiretoko';


	/**
	 * The string for Mozilla browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_mozilla The string for Mozilla browsers
	 */
	public $browser_mozilla = 'Mozilla';


	/**
	 * The string for Amaya browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_amaya The string for Amaya browsers
	 */
	public $browser_amaya = 'Amaya';


	/**
	 * The string for lynx browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_lynx The string for Lynx browsers
	 */
	public $browser_lynx = 'Lynx';


	/**
	 * The string for Safari browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_safari The string for Safari browsers
	 */
	public $browser_safari = 'Safari';


	/**
	 * The string for iPhone browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_iphone The string for iPhone browsers
	 */
	public $browser_iphone = 'iPhone';


	/**
	 * The string for iPod browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_ipod The string for iPod browsers
	 */
	public $browser_ipod = 'iPod';


	/**
	 * The string for iPad browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_ipad The string for iPad browsers
	 */
	public $browser_ipad = 'iPad';


	/**
	 * The string for Chrome browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_chrome The string for Chrome browsers
	 */
	public $browser_chrome = 'Chrome';


	/**
	 * The string for Android browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_android The string for Android browsers
	 */
	public $browser_android = 'Android';


	/**
	 * The string for GoogleBot browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_googlebot The string for GoogleBot browsers
	 */
	public $browser_googlebot = 'GoogleBot';


	/**
	 * The string for Slurp browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_slurp The string for Slurp browsers
	 */
	public $browser_slurp = 'Yahoo! Slurp';


	/**
	 * The string for W3C Validator browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_w3cvalidator The string for W3C Validator browsers
	 */
	public $browser_w3cvalidator = 'W3C Validator';


	/**
	 * The string for BlackBerry browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_blackberry The string for BlackBerry browsers
	 */
	public $browser_blackberry = 'BlackBerry';


	/**
	 * The string for IceCat browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_icecat The string for IceCat browsers
	 */
	public $browser_icecat = 'IceCat';


	/**
	 * The string for Nokia S60 browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_nokia_s60 The string for Nokia S60 browsers
	 */
	public $browser_nokia_s60 = 'Nokia S60 OSS Browser';


	/**
	 * The string for Nokia browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_nokia The string for Nokia browsers
	 */
	public $browser_nokia = 'Nokia Browser';


	/**
	 * The string for MSN browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_msn The string for MSN browsers
	 */
	public $browser_msn = 'MSN Browser';


	/**
	 * The string for MSN Bot browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_msnbot The string for MSN Bot browsers
	 */
	public $browser_msnbot = 'MSN Bot';


	/**
	 * The string for Netscape Navigator browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_netscape_navigator The string for Netscape Navigator browsers
	 */
	public $browser_netscape_navigator = 'Netscape Navigator';


	/**
	 * The string for Galeon browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_googlebot The string for GoogleBot browsers
	 */
	public $browser_galeon = 'Galeon';


	/**
	 * The string for NetPositive browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_netpositive The string for NetPositive browsers
	 */
	public $browser_netpositive = 'NetPositive';


	/**
	 * The string for Phoenix browsers
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $browser_phoenix The string for Phoenix browsers
	 */
	public $browser_phoenix = 'Phoenix';


	/**
	 * The string for unknown platforms
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_unknown The string for unknown platforms
	 */
	public $platform_unknown = 'unknown';


	/**
	 * The string for Windows
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_windows The string for Windows
	 */
	public $platform_windows = 'Windows';


	/**
	 * The string for Windows CE
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_windows_ce The string for Windows CE
	 */
	public $platform_windows_ce = 'Windows CE';


	/**
	 * The string for Apple
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_apple The string for Apple
	 */
	public $platform_apple = 'Apple';


	/**
	 * The string for Linux
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_linux The string for Linux
	 */
	public $platform_linux = 'Linux';


	/**
	 * The string for OS2
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_os2 The string for OS2
	 */
	public $platform_os2 = 'OS/2';


	/**
	 * The string for BeOS
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_beos The string for BeOS
	 */
	public $platform_beos = 'BeOS';


	/**
	 * The string for iPhone
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_iphone The string for iPhone
	 */
	public $platform_iphone = 'iPhone';


	/**
	 * The string for iPod
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_ipod The string for iPod
	 */
	public $platform_ipod = 'iPod';


	/**
	 * The string for iPad
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_ipad The string for iPad
	 */
	public $platform_ipad = 'iPad';


	/**
	 * The string for BlackBerry
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_blackberry The string for BlackBerry
	 */
	public $platform_blackberry = 'BlackBerry';


	/**
	 * The string for Nokia
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_nokia The string for Nokia
	 */
	public $platform_nokia = 'Nokia';


	/**
	 * The string for FreeBSD
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_freebsd The string for FreeBSD
	 */
	public $platform_freebsd = 'FreeBSD';


	/**
	 * The string for OpenBSD
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_openbsd The string for OpenBSD
	 */
	public $platform_openbsd = 'OpenBSD';


	/**
	 * The string for NetBSD
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_netbsd The string for NetBSD
	 */
	public $platform_netbsd = 'NetBSD';


	/**
	 * The string for SunOS
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_sunos The string for SunOS
	 */
	public $platform_sunos = 'SunOS';


	/**
	 * The string for OpenSolaris
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_opensolaris The string for OpenSolaris
	 */
	public $platform_opensolaris = 'OpenSolaris';


	/**
	 * The string for Android
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $platform_android The string for Android
	 */
	public $platform_android = 'Android';


	/**
	 * The string for unknown operating systems
	 *
	 * @access      public
	 * @since       1.0.0
	 * @var         string $operating_system_unknown The string for unknown operating systems
	 */
	public $operating_system_unknown = 'unknown';


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $useragent The useragent to parse.
	 * @return      void
	 */
	public function __construct( $useragent = '' ) {
		$this->reset();

		if ( '' !== $useragent ) {
			$this->set_user_agent( $useragent );
		} else {
			$this->determine();
		}
	}


	/**
	 * Reset all properties
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function reset() {
		$this->agent        = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "";  // phpcs:ignore
		$this->browser_name = $this->browser_unknown;
		$this->version      = $this->version_unknown;
		$this->platform     = $this->platform_unknown;
		$this->os           = $this->operating_system_unknown;
		$this->is_aol       = false;
		$this->is_mobile    = false;
		$this->is_robot     = false;
		$this->aol_version  = $this->version_unknown;
	}


	/**
	 * Check to see if the specific browser is valid
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $browser_name The browser name to check.
	 * @return      bool Whether or not the browser is the specified browser
	 */
	public function is_browser( $browser_name ) {
		return 0 === strcasecmp( $this->browser_name, trim( $browser_name ) );
	}


	/**
	 * The name of the browser.  All return types are from the class constants
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string Name of the browser
	 */
	public function get_browser() {
		return $this->browser_name;
	}


	/**
	 * Set the name of the browser
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $browser The name of the browser.
	 * @return      string $browser The name of the browser
	 */
	public function set_browser( $browser ) {
		$this->browser_name = $browser;

		return $browser;
	}


	/**
	 * The name of the platform.  All return types are from the class constants
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string Name of the browser
	 */
	public function get_platform() {
		return $this->platform;
	}


	/**
	 * Set the name of the platform
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $platform The name of the platform.
	 * @return      string $platform The name of the platform
	 */
	public function set_platform( $platform ) {
		$this->platform = $platform;

		return $platform;
	}


	/**
	 * The version of the browser
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string Version of the browser (will only contain alpha-numeric characters and a period)
	 */
	public function get_version() {
		return $this->version;
	}


	/**
	 * Set the version of the browser
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $version The version of the browser.
	 * @return      void
	 */
	public function set_version( $version ) {
		$this->version = preg_replace( '/[^0-9,.,a-z,A-Z-]/', '', $version );
	}


	/**
	 * The version of AOL
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string The version of AOL (will only contain alpha-numeric characters and a period)
	 */
	public function get_aol_version() {
		return $this->aol_version;
	}


	/**
	 * Set the version of AOL
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $version The version of AOL.
	 * @return      void
	 */
	public function set_aol_version( $version ) {
		$this->aol_version = preg_replace( '/[^0-9,.,a-z,A-Z]/', '', $version );
	}


	/**
	 * Is the browser from AOL?
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is from AOL
	 */
	public function is_aol() {
		return $this->is_aol;
	}


	/**
	 * Is the browser from a mobile device?
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is from a mobile device
	 */
	public function is_mobile() {
		return $this->is_mobile;
	}


	/**
	 * Is the browser from a robot (ex Slurp,GoogleBot)?
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is from a robot
	 */
	public function is_robot() {
		return $this->is_robot;
	}


	/**
	 * Set the browser to be from AOL
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       bool $is_aol Whether or not the browser is from AOL.
	 * @return      void
	 */
	public function set_aol( $is_aol ) {
		$this->is_aol = $is_aol;
	}


	/**
	 * Set the browser to be mobile
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       bool $value Whether the browser a mobile browser or not.
	 * @return      void
	 */
	public function set_mobile( $value = true ) {
		$this->is_mobile = $value;
	}


	/**
	 * Set the browser to be a robot
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       bool $value Whether the browser is a robot or not.
	 * @return      void
	 */
	public function set_robot( $value = true ) {
		$this->is_robot = $value;
	}


	/**
	 * Get the user agent value in use to determine the browser
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string The user agent from the HTTP header.
	 */
	public function get_user_agent() {
		return $this->agent;
	}


	/**
	 * Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $agent_string The value for the User Agent.
	 * @return      void
	 */
	public function set_user_agent( $agent_string ) {
		$this->reset();
		$this->agent = $agent_string;
		$this->determine();
	}


	/**
	 * Used to determine if the browser is actually "chromeframe"
	 *
	 * @access      public
	 * @since       1.7.0
	 * @return      bool Whether the browser is using chromeframe
	 */
	public function is_chrome_frame() {
		return strpos( $this->agent, 'chromeframe' ) !== false;
	}


	/**
	 * Returns a formatted string with a summary of the details of the browser.
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string The formatted string with a summary of the browser
	 */
	public function __toString() {
		$text1      = $this->get_user_agent();              // Grabs the UA (user agent) string.
		$ua_line1   = substr( $text1, 0, 32 );              // The first line we print should only be the first 32 characters of the UA string.
		$text2      = $this->get_user_agent();              // Now we grab it again and save it to a string.
		$to_wrap_ua = str_replace( $ua_line1, '', $text2 ); // The rest of the print off (other than first line) is equivalent
															// to the whole string minus the part we printed off. IE
															// User Agent:
															// The first 32 characters from ua_line1.
															// The rest of it is now stored in $text2 to be printed off.
															// We need to add spaces before each line that is split other than line 1.
		$space = '';

		for ( $i = 0; $i < 25; $i++ ) {
			$space .= ' ';
		}

		// Now we split the remaining string of UA ($text2) into lines that are prefixed by spaces for formatting.
		$word_wrapped = chunk_split( $to_wrap_ua, 32, "\n $space" );

		return "Platform:                 {$this->get_platform()} \n" .
			"Browser Name:             {$this->get_browser()}  \n" .
			"Browser Version:          {$this->get_version()} \n" .
			"User Agent String:        $ua_line1 \n\t\t\t  " .
			"$word_wrapped";
	}


	/**
	 * Protected routine to calculate and determine what the browser is in use (including platform)
	 *
	 * @access      protected
	 * @since       1.0.0
	 * @return      void
	 */
	protected function determine() {
		$this->check_platform();
		$this->check_browsers();
		$this->check_for_aol();
	}


	/**
	 * Protected routine to determine the browser type
	 *
	 * @access      protected
	 * @since       1.0.0
	 * @return      bool Whether or not the browser was detected
	 */
	protected function check_browsers() {
		return (
			// Well-known, well-used
			// Special Notes:
			// (1) Opera must be checked before FireFox due to the odd
			// user agents used in some older versions of Opera.
			// (2) WebTV is strapped onto Internet Explorer so we must
			// check for WebTV before IE.
			// (3) (deprecated) Galeon is based on Firefox and needs to be
			// tested before Firefox is tested.
			// (4) OmniWeb is based on Safari so OmniWeb check must occur
			// before Safari.
			// (5) Netscape 9+ is based on Firefox so Netscape checks
			// before FireFox are necessary.
			$this->check_browser_webtv() ||
			$this->check_browser_internet_explorer() ||
			$this->check_browser_opera() ||
			$this->check_browser_galeon() ||
			$this->check_browser_netscape_navigator_9plus() ||
			$this->check_browser_firefox() ||
			$this->check_browser_chrome() ||
			$this->check_browser_omniweb() ||

			// Common mobile.
			$this->check_browser_android() ||
			$this->check_browser_ipad() ||
			$this->check_browser_ipod() ||
			$this->check_browser_iphone() ||
			$this->check_browser_blackberry() ||
			$this->check_browser_nokia() ||

			// Common bots.
			$this->check_browser_googlebot() ||
			$this->check_browser_msnbot() ||
			$this->check_browser_slurp() ||

			// WebKit base check (post mobile and others).
			$this->check_browser_safari() ||

			// Everyone else.
			$this->check_browser_netpositive() ||
			$this->check_browser_firebird() ||
			$this->check_browser_konqueror() ||
			$this->check_browser_icab() ||
			$this->check_browser_phoenix() ||
			$this->check_browser_amaya() ||
			$this->check_browser_lynx() ||

			$this->check_browser_shiretoko() ||
			$this->check_browser_icecat() ||
			$this->check_browser_w3cvalidator() ||
			$this->check_browser_mozilla() /* Mozilla is such an open standard that you must check it last */
		);
	}


	/**
	 * Determine if the user is using a BlackBerry
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is the BlackBerry browser
	 */
	public function check_browser_blackberry() {
		if ( stripos( $this->agent, 'blackberry' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'BlackBerry' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->browser_name = $this->browser_blackberry;
			$this->set_mobile( true );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the user is using an AOL User Agent
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is from AOL
	 */
	public function check_for_aol() {
		$this->set_aol( false );
		$this->set_aol_version( $this->version_unknown );

		if ( stripos( $this->agent, 'aol' ) !== false ) {
			$a_version = explode( ' ', stristr( $this->agent, 'AOL' ) );

			$this->set_aol( true );
			$this->set_aol_version( preg_replace( '/[^0-9\.a-z]/i', '', $a_version[1] ) );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is the GoogleBot or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is the GoogleBot
	 */
	public function check_browser_googlebot() {
		if ( stripos( $this->agent, 'googlebot' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'googlebot' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( str_replace( ';', '', $a_version[0] ) );
			$this->browser_name = $this->browser_googlebot;
			$this->set_robot( true );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is the MSNBot or not (last updated 1.9)
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is the MSNBot
	 */
	public function check_browser_msnbot() {
		if ( stripos( $this->agent, 'msnbot' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'msnbot' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( str_replace( ';', '', $a_version[0] ) );
			$this->browser_name = $this->browser_msnbot;
			$this->set_robot( true );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is the W3C Validator or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is the W3C Validator
	 */
	public function check_browser_w3cvalidator() {
		if ( stripos( $this->agent, 'W3C-checklink' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'W3C-checklink' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->browser_name = $this->browser_w3cvalidator;

			return true;
		} elseif ( stripos( $this->agent, 'W3C_Validator' ) !== false ) {
			// Some of the Validator versions do not delineate w/ a slash - add it back in.
			$ua        = str_replace( 'W3C_Validator ', 'W3C_Validator/', $this->agent );
			$a_result  = explode( '/', stristr( $ua, 'W3C_Validator' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->browser_name = $this->browser_w3cvalidator;

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is the Yahoo! Slurp Robot or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is the Yahoo! Slurp Robot
	 */
	public function check_browser_slurp() {
		if ( stripos( $this->agent, 'slurp' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'Slurp' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->browser_name = $this->browser_slurp;
			$this->set_robot( true );
			$this->set_mobile( false );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Internet Explorer or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Internet Explorer
	 */
	public function check_browser_internet_explorer() {
		if ( stripos( $this->agent, 'microsoft internet explorer' ) !== false ) {
			// Test for v1 - v1.5 IE.
			$this->set_browser( $this->browser_ie );
			$this->set_version( '1.0' );

			$a_result = stristr( $this->agent, '/' );

			if ( preg_match( '/308|425|426|474|0b1/i', $a_result ) ) {
				$this->set_version( '1.5' );
			}

			return true;
		} elseif ( stripos( $this->agent, 'msie' ) !== false && stripos( $this->agent, 'opera' ) === false ) {
			// Test for versions > 1.5.
			// See if the browser is the odd MSN Explorer.
			if ( stripos( $this->agent, 'msnb' ) !== false ) {
				$a_result = explode( ' ', stristr( str_replace( ';', '; ', $this->agent ), 'MSN' ) );

				$this->set_browser( $this->browser_msn );
				$this->set_version( str_replace( array( '(', ')', ';' ), '', $a_result[1] ) );

				return true;
			}

			$a_result = explode( ' ', stristr( str_replace( ';', '; ', $this->agent ), 'msie' ) );

			$this->set_browser( $this->browser_ie );
			$this->set_version( str_replace( array( '(', ')', ';' ), '', $a_result[1] ) );

			return true;
		} elseif ( stripos( $this->agent, 'mspie' ) !== false || stripos( $this->agent, 'pocket' ) !== false ) {
			// Test for Pocket IE.
			$a_result = explode( ' ', stristr( $this->agent, 'mspie' ) );

			$this->set_platform( $this->platform_windows_ce );
			$this->set_browser( $this->browser_pocket_ie );
			$this->set_mobile( true );

			if ( stripos( $this->agent, 'mspie' ) !== false ) {
				$this->set_version( $a_result[1] );
			} else {
				$a_version = explode( '/', $this->agent );

				$this->set_version( $a_version[1] );
			}

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Opera or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Opera
	 */
	public function check_browser_opera() {
		if ( stripos( $this->agent, 'opera mini' ) !== false ) {
			$resultant = stristr( $this->agent, 'opera mini' );

			if ( preg_match( '/\//', $resultant ) ) {
				$a_result  = explode( '/', $resultant );
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$a_version = explode( ' ', stristr( $resultant, 'opera mini' ) );

				$this->set_version( $a_version[1] );
			}

			$this->browser_name = $this->browser_opera_mini;
			$this->set_mobile( true );

			return true;
		} elseif ( stripos( $this->agent, 'opera' ) !== false ) {
			$resultant = stristr( $this->agent, 'opera' );

			if ( preg_match( '/Version\/(10.*)$/', $resultant, $matches ) ) {
				$this->set_version( $matches[1] );
			} elseif ( preg_match( '/\//', $resultant ) ) {
				$a_result  = explode( '/', str_replace( '(', ' ', $resultant ) );
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$a_version = explode( ' ', stristr( $resultant, 'opera' ) );

				$this->set_version( isset( $a_version[1] ) ? $a_version[1] : '' );
			}

			$this->browser_name = $this->browser_opera;

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Chrome or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Chrome
	 */
	public function check_browser_chrome() {
		if ( stripos( $this->agent, 'Chrome' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'Chrome' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_chrome );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is WebTv or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is WebTv
	 */
	public function check_browser_webtv() {
		if ( stripos( $this->agent, 'webtv' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'webtv' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_webtv );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is NetPositive or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is NetPositive
	 */
	public function check_browser_netpositive() {
		if ( stripos( $this->agent, 'NetPositive' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'NetPositive' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( str_replace( array( '(', ')', ';' ), '', $a_version[0] ) );
			$this->set_browser( $this->browser_netpositive );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Galeon or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Galeon
	 */
	public function check_browser_galeon() {
		if ( stripos( $this->agent, 'galeon' ) !== false ) {
			$a_result  = explode( ' ', stristr( $this->agent, 'galeon' ) );
			$a_version = explode( '/', $a_result[0] );

			$this->set_version( $a_version[1] );
			$this->set_browser( $this->browser_galeon );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Konqueror or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Konqueror
	 */
	public function check_browser_konqueror() {
		if ( stripos( $this->agent, 'Konqueror' ) !== false ) {
			$a_result  = explode( ' ', stristr( $this->agent, 'Konqueror' ) );
			$a_version = explode( '/', $a_result[0] );

			$this->set_version( $a_version[1] );
			$this->set_browser( $this->browser_konqueror );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is iCab or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is iCab
	 */
	public function check_browser_icab() {
		if ( stripos( $this->agent, 'icab' ) !== false ) {
			$a_version = explode( ' ', stristr( str_replace( '/', ' ', $this->agent ), 'icab' ) );

			$this->set_version( $a_version[1] );
			$this->set_browser( $this->browser_icab );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is OmniWeb or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is OmniWeb
	 */
	public function check_browser_omniweb() {
		if ( stripos( $this->agent, 'omniweb' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'omniweb' ) );
			$a_version = explode( ' ', isset( $a_result[1] ) ? $a_result[1] : '' );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_omniweb );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Phoenix or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Phoenix
	 */
	public function check_browser_phoenix() {
		if ( stripos( $this->agent, 'Phoenix' ) !== false ) {
			$a_version = explode( '/', stristr( $this->agent, 'Phoenix' ) );

			$this->set_version( $a_version[1] );
			$this->set_browser( $this->browser_phoenix );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Firebird or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Firebird
	 */
	public function check_browser_firebird() {
		if ( stripos( $this->agent, 'Firebird' ) !== false ) {
			$a_version = explode( '/', stristr( $this->agent, 'Firebird' ) );

			$this->set_version( $a_version[1] );
			$this->set_browser( $this->browser_firebird );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Netscape Navigator 9+ or not
	 * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Netscape Navigator 9+
	 */
	public function check_browser_netscape_navigator_9plus() {
		if ( stripos( $this->agent, 'Firefox' ) !== false && preg_match( '/Navigator\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->set_version( $matches[1] );
			$this->set_browser( $this->browser_netscape_navigator );

			return true;
		} elseif ( stripos( $this->agent, 'Firefox' ) === false && preg_match( '/Netscape6?\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->set_version( $matches[1] );
			$this->set_browser( $this->browser_netscape_navigator );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko)
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Shiretoko
	 */
	public function check_browser_shiretoko() {
		if ( stripos( $this->agent, 'Mozilla' ) !== false && preg_match( '/Shiretoko\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->set_version( $matches[1] );
			$this->set_browser( $this->browser_shiretoko );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat)
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Ice Cat
	 */
	public function check_browser_icecat() {
		if ( stripos( $this->agent, 'Mozilla' ) !== false && preg_match( '/IceCat\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->set_version( $matches[1] );
			$this->set_browser( $this->browser_icecat );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Nokia or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Nokia
	 */
	public function check_browser_nokia() {
		if ( preg_match( '/Nokia([^\/]+)\/([^ SP]+)/i', $this->agent, $matches ) ) {
			$this->set_version( $matches[2] );

			if ( stripos( $this->agent, 'Series60' ) !== false || strpos( $this->agent, 'S60' ) !== false ) {
				$this->set_browser( $this->browser_nokia_s60 );
			} else {
				$this->set_browser( $this->browser_nokia );
			}

			$this->set_mobile( true );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Firefox or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Firefox
	 */
	public function check_browser_firefox() {
		if ( stripos( $this->agent, 'safari' ) === false ) {
			if ( preg_match( '/Firefox[\/ \(]([^ ;\)]+)/i', $this->agent, $matches ) ) {
				$this->set_version( $matches[1] );
				$this->set_browser( $this->browser_firefox );

				return true;
			} elseif ( preg_match( '/Firefox$/i', $this->agent, $matches ) ) {
				$this->set_version( '' );
				$this->set_browser( $this->browser_firefox );

				return true;
			}
		}

		return false;
	}


	/**
	 * Determine if the browser is Firefox or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Firefox
	 */
	public function check_browser_iceweasel() {
		if ( stripos( $this->agent, 'Iceweasel' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'Iceweasel' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_iceweasel );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Mozilla or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Mozilla
	 */
	public function check_browser_mozilla() {
		if ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->agent ) && stripos( $this->agent, 'netscape' ) === false ) {
			$a_version = explode( ' ', stristr( $this->agent, 'rv:' ) );

			preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->agent, $a_version );

			$this->set_version( str_replace( 'rv:', '', $a_version[0] ) );
			$this->set_browser( $this->browser_mozilla );

			return true;
		} elseif ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9]\.[0-9]/i', $this->agent ) && stripos( $this->agent, 'netscape' ) === false ) {
			$a_version = explode( '', stristr( $this->agent, 'rv:' ) );

			$this->set_version( str_replace( 'rv:', '', $a_version[0] ) );
			$this->set_browser( $this->browser_mozilla );

			return true;
		} elseif ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/mozilla\/([^ ]*)/i', $this->agent, $matches ) && stripos( $this->agent, 'netscape' ) === false ) {
			$this->set_version( $matches[1] );
			$this->set_browser( $this->browser_mozilla );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Lynx or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Lynx
	 */
	public function check_browser_lynx() {
		if ( stripos( $this->agent, 'lynx' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'Lynx' ) );
			$a_version = explode( ' ', ( isset( $a_result[1] ) ? $a_result[1] : '' ) );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_lynx );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Amaya or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Amaya
	 */
	public function check_browser_amaya() {
		if ( stripos( $this->agent, 'amaya' ) !== false ) {
			$a_result  = explode( '/', stristr( $this->agent, 'Amaya' ) );
			$a_version = explode( ' ', $a_result[1] );

			$this->set_version( $a_version[0] );
			$this->set_browser( $this->browser_amaya );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Safari or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Safari
	 */
	public function check_browser_safari() {
		if ( stripos( $this->agent, 'Safari' ) !== false && stripos( $this->agent, 'iPhone' ) === false && stripos( $this->agent, 'iPod' ) === false ) {
			$a_result = explode( '/', stristr( $this->agent, 'Version' ) );

			if ( isset( $a_result[1] ) ) {
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$this->set_version( $this->version_unknown );
			}

			$this->set_browser( $this->browser_safari );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is iPhone or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is iPhone
	 */
	public function check_browser_iphone() {
		if ( stripos( $this->agent, 'iPhone' ) !== false ) {
			$a_result = explode( '/', stristr( $this->agent, 'Version' ) );

			if ( isset( $a_result[1] ) ) {
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$this->set_version( $this->version_unknown );
			}

			$this->set_mobile( true );
			$this->set_browser( $this->browser_iphone );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is iPod or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is iPod
	 */
	public function check_browser_ipad() {
		if ( stripos( $this->agent, 'iPad' ) !== false ) {
			$a_result = explode( '/', stristr( $this->agent, 'Version' ) );

			if ( isset( $a_result[1] ) ) {
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$this->set_version( $this->version_unknown );
			}

			$this->set_mobile( true );
			$this->set_browser( $this->browser_ipad );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is iPod or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is iPod
	 */
	public function check_browser_ipod() {
		if ( stripos( $this->agent, 'iPod' ) !== false ) {
			$a_result = explode( '/', stristr( $this->agent, 'Version' ) );

			if ( isset( $a_result[1] ) ) {
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$this->set_version( $this->version_unknown );
			}

			$this->set_mobile( true );
			$this->set_browser( $this->browser_ipod );

			return true;
		}

		return false;
	}


	/**
	 * Determine if the browser is Android or not
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      bool Whether or not the browser is Android
	 */
	public function check_browser_android() {
		if ( stripos( $this->agent, 'Android' ) !== false ) {
			$a_result = explode( ' ', stristr( $this->agent, 'Android' ) );

			if ( isset( $a_result[1] ) ) {
				$a_version = explode( ' ', $a_result[1] );

				$this->set_version( $a_version[0] );
			} else {
				$this->set_version( $this->version_unknown );
			}

			$this->set_mobile( true );
			$this->set_browser( $this->browser_android );

			return true;
		}

		return false;
	}


	/**
	 * Determine the user's platform
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function check_platform() {
		if ( stripos( $this->agent, 'windows' ) !== false ) {
			$this->platform = $this->platform_windows;
		} elseif ( stripos( $this->agent, 'iPad' ) !== false ) {
			$this->platform = $this->platform_ipad;
		} elseif ( stripos( $this->agent, 'iPod' ) !== false ) {
			$this->platform = $this->platform_ipod;
		} elseif ( stripos( $this->agent, 'iPhone' ) !== false ) {
			$this->platform = $this->platform_iphone;
		} elseif ( stripos( $this->agent, 'mac' ) !== false ) {
			$this->platform = $this->platform_apple;
		} elseif ( stripos( $this->agent, 'android' ) !== false ) {
			$this->platform = $this->platform_android;
		} elseif ( stripos( $this->agent, 'linux' ) !== false ) {
			$this->platform = $this->platform_linux;
		} elseif ( stripos( $this->agent, 'Nokia' ) !== false ) {
			$this->platform = $this->platform_nokia;
		} elseif ( stripos( $this->agent, 'BlackBerry' ) !== false ) {
			$this->platform = $this->platform_blackberry;
		} elseif ( stripos( $this->agent, 'FreeBSD' ) !== false ) {
			$this->platform = $this->platform_freebsd;
		} elseif ( stripos( $this->agent, 'OpenBSD' ) !== false ) {
			$this->platform = $this->platform_openbsd;
		} elseif ( stripos( $this->agent, 'NetBSD' ) !== false ) {
			$this->platform = $this->platform_netbsd;
		} elseif ( stripos( $this->agent, 'OpenSolaris' ) !== false ) {
			$this->platform = $this->platform_opensolaris;
		} elseif ( stripos( $this->agent, 'SunOS' ) !== false ) {
			$this->platform = $this->platform_sunos;
		} elseif ( stripos( $this->agent, 'OS\/2' ) !== false ) {
			$this->platform = $this->platform_os2;
		} elseif ( stripos( $this->agent, 'BeOS' ) !== false ) {
			$this->platform = $this->platform_beos;
		} elseif ( stripos( $this->agent, 'win' ) !== false ) {
			$this->platform = $this->platform_windows;
		}
	}
}
