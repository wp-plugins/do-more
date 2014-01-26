<?php
/*
 * Plugin Name: Do More
 * Plugin URI: http://www.pluginspodcast.com/plugins/do-more/
 * Description: Do more with the &lt;!--more--&gt; Tag. Adds the rel="nofollow" attribute to all links below the &lt;!--more--&gt; tag.
 * Version: 0.1
 * Author: Angelo Mandato, Plugins Podcast
 * Author URI: http://www.pluginspodcast.com
 * License: GPL2
 
Requires at least: 3.7
Tested up to: 3.8.1
Text Domain: do-more
Change Log: See readme.txt for complete change log
Contributors: Angelo Mandato, CIO RawVoice and host of the PluginsPodcast.com
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt

Copyright 2009-2012 Angelo Mandato, host of the Plugins Podcast (http://www.pluginspodcast.com)
 */

if( !function_exists('add_action') )
	die("access denied.");
	
// WP_PLUGIN_DIR (REMEMBER TO USE THIS DEFINE IF NEEDED)
define('DO_MORE_VERSION', '0.1' );

// Translation support:
if ( !defined('DO_MORE_ABSPATH') )
	define('DO_MORE_ABSPATH', dirname(__FILE__) );

// Translation support loaded:
load_plugin_textdomain('do-more', // domain / keyword name of plugin
		DO_MORE_ABSPATH .'/languages', // Absolute path
		basename(DO_MORE_ABSPATH).'/languages' ); // relative path in plugins folder

if( !defined('DO_MORE_CONTENT_ACTION_PRIORITY') )
	define('DO_MORE_CONTENT_ACTION_PRIORITY', 10 );
	
class DoMorePlugin {

		var $m_settings = array();
		
    public function __construct()  
    {  
			// Options , for future use when we create admin settings we can tweak this
			$this->m_settings['nofollow_before'] = false;
			$this->m_settings['nofollow_after'] = true;
			
			add_filter('the_content', array($this, 'the_content'), DO_MORE_CONTENT_ACTION_PRIORITY);
    }  
      
    public function the_content($content)  
    {
			$id = get_the_ID();
			$find = '<span id="more-'. $id .'"></span>';
			if( preg_match('/'. preg_quote($find, '/') .'/', $content, $matches) ) // Find the more tag!
			{
				$parts = explode( $matches[0], $content, 2 ); // Split the content in half
				
				if( !empty($this->m_settings['nofollow_before']) )
				{
					$parts[0] = $this->_rel_nofollow($parts[0]);
				}
				
				if( !empty($this->m_settings['nofollow_after']) )
				{
					$parts[1] = $this->_rel_nofollow($parts[1]);
				}
				
				$content = $parts[0] . $find . $parts[1];
			}
			else if( !empty($this->m_settings['nofollow_before']) )
			{
				$content = $this->_rel_nofollow($content);
			}
			
			return $content;
    }
		
		function _rel_nofollow($text)
		{
			return preg_replace_callback('|<a (.+?)>|i', array($this, '_rel_nofollow_callback'), $text);
		}
		
		function _rel_nofollow_callback($matches)
		{
			$text = $matches[1];
			$text = str_replace(array(' rel="nofollow"', " rel='nofollow'"), '', $text);
			return "<a $text rel=\"nofollow\">";
		}
};

$wp_do_more_plugin = new DoMorePlugin();

?>