<?php
/**
* Plugin Name: Extract Filters and Actions
* Plugin URI: http://www.wpcube.co.uk/plugins/extract-filters-plugins
* Version: 1.0.2
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Extract Filters and Actions lets you choose a WordPress Plugin on your installation (whether active or inactive), and find all references to apply_filters() and do_action() recursively, building output in either a HTML table or PHP array for you to then use in support documentation, personal reference etc.
* License: GPL2
*/

/*  Copyright 2015 WP Cube (email : support@wpcube.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ExtractFiltersActions {
	
    /**
    * Constructor.
    */
    function __construct() {
	    
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name         = 'extract-filters-actions'; // Plugin Folder
        $this->plugin->displayName  = 'Extract Filters and Actions'; // Plugin Name
        $this->plugin->version      = '1.0.2';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
		$this->plugin->adminScreens = array(
	    	'plugins_page_' . $this->plugin->name,  
        );
        
        // Dashboard Submodule
        if ( !class_exists( 'WPCubeDashboardWidget' ) ) {
			require_once( $this->plugin->folder . '_modules/dashboard/dashboard.php' );
		}
		$dashboard = new WPCubeDashboardWidget( $this->plugin ); 

		// Hooks
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts_css' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'plugins_loaded', array( &$this, 'load_language_files' ) );
        
    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function admin_scripts_css() {
	    
	    // Only load on relevant screen(s)
	    $screen = get_current_screen();
	    if ( ! isset( $screen->base ) || ! in_array( $screen->base, $this->plugin->adminScreens ) ) {
		    return;
	    }
	    
    	// JS
    	wp_enqueue_script( $this->plugin->name.'-admin', $this->plugin->url.'js/admin.js', array( 'jquery' ), $this->plugin->version, true );
    	 
    }
    
    /**
    * Register the plugin settings panel
    */
    function admin_menu() {
	    
        add_plugins_page( $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel') );
        
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
	    
        // Save Settings
        if ( isset($_POST['submit']) ) {
        	// Check nonce
        	if ( !isset( $_POST[$this->plugin->name.'_nonce'] ) ) {
	        	// Missing nonce	
	        	$this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', $this->plugin->name );
        	} elseif ( !wp_verify_nonce( $_POST[$this->plugin->name.'_nonce'], $this->plugin->name ) ) {
	        	// Invalid nonce
	        	$this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', $this->plugin->name );
        	} else {        	
	        	if ( isset( $_POST[$this->plugin->name] ) ) {
		        	// Save settings for next time
		        	delete_option( $this->plugin->name );
		        	update_option( $this->plugin->name, $_POST[ $this->plugin->name ] );
		        	
		        	// Get settings
		        	$this->settings = get_option($this->plugin->name);
		        	
		        	// Generate output
		        	try {
			        	$output = $this->run( 	
							WP_PLUGIN_DIR . '/' . $this->settings['plugin'], 
							( isset( $this->settings['filters'] ) ? 1 : 0 ),
							( isset( $this->settings['actions'] ) ? 1 : 0 ),
							$this->settings['format'],
							( !empty( $this->settings['prefix'] ) ? $this->settings['prefix'] : false ),
							( isset( $this->settings['byFile'] ) ? 1 : 0 ),
							( isset( $this->settings['classes'] ) ? $this->settings['classes'] : '' )
						);
			        	
			        	// Convert output to string
			        	if ( is_array( $output ) ) {
				        	$output = stripslashes( var_export( $output, true ) );
			        	}
			        						
			        	// Display message
						$this->message = __( 'Settings Saved and Filters/Actions Extracted.', $this->plugin->name );
		        	} catch (Exception $e) {
			        	$this->errorMessage = $e->getMessage();
		        	}
				}
			}
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);

        // Get plugin list
        $plugins = $this->get_plugins();
        
		// Load Settings Form
        include_once( $this->plugin->folder . 'views/settings.php' );  
    }
    
    /**
	* Helper for getting WordPress Plugins in a folder/name key/value pair array
	*
	* @since 1.0.0
	*
	* @return array Plugins
	*/
	function get_plugins() {
		
		// Get plugins
		$installedPlugins = get_plugins();	
		if ( count( $installedPlugins ) == 0 ) {
			// Something went wrong - we should get at least one plugin!
			return false;
		}
		
		// Iterate through found plugins, building folder/name key/value pair array
		$plugins = array();
		foreach ( $installedPlugins as $folderAndFile => $pluginData ) {
			$folderFileArr = explode( '/', $folderAndFile );
			$plugins[ $folderFileArr[0] ] = $pluginData['Name'];
		}
		
		return $plugins;
		
	}
    
    /**
	* Loads plugin textdomain
	*/
	function load_language_files() {
		
		load_plugin_textdomain( $this->plugin->name, false, $this->plugin->name . '/languages/' );
		
	}
	
	/**
	* Extracts all instances of apply_filters() and do_action() calls from the specified folders and subfolders
	* PHP files, returning either an array of filters and actions, or a pretty HTML table output.
	*
	* @since 1.0.0
	*
	* @param	string 	$folder 		WordPress Plugin/Theme Folder Path (e.g. /path/to/your/wp/wp-content/plugins/plugin-name)
	* @param	bool	$extractFilters	Extract apply_filters() calls
	* @param	bool	$extractActions	Extract do_action() calls
	* @param	bool	$returnFormat	Return Format (html|array)
	* @param	bool	$prefixRequired	Optional prefix string required on filters and actions for inclusion in resultset (false = don't filter any found filters/actions)
	* @param 	bool	$byFile			Denote filters and actions by filename (false = group filters and actions if they appear across multiple files)
	* @param 	string  $cssClasses 	HTML Table CSS Classes (optional)
	* @return	mixed					Output
	*/
	function run( $folder, $extractFilters = true, $extractActions = true, $returnFormat = 'html', $prefixRequired = false, $byFile = false, $cssClasses = '' ) {
		
		$phpFiles = array();
		
		// Iterate through the folder and subfolders, finding any PHP files
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $filename ) {
			
			// Ignore directories
			if ( is_dir( $filename ) ) {
				continue;
			}
			
			// Check if a PHP file
			if ( substr( $filename, -4) != '.php' ) {
				continue;
			}
			
			$phpFiles[] = $filename;
			
		}
		
		// Check if any PHP files were found
		if ( count( $phpFiles ) == 0 ) {
			return false;
		}
		
		// Iterate through PHP files, extracting apply_filters() and do_action() calls
		$filters = array();
		$actions = array();
		foreach ( $phpFiles as $file ) {
			// Read file contents
			$h = fopen( $file, 'r' );
			if ( !$h ) {
				continue;
			}
			$contents = fread( $h, filesize( $file ) );
			fclose( $h );
			
			// Get file name
			$fileOnly = str_replace( $folder, '', $file );
			
			// Find all instances of apply_filters() and do_action()
			if ( $extractFilters ) {
				$filters = $this->find_matches( "/apply_filters.*?\(.*?'(.*?)'.*?\)/s", $filters, $contents, $fileOnly, $prefixRequired, $byFile );
			}
			if ( $extractActions ) {
				$actions = $this->find_matches( "/do_action.*?\(.*?'(.*?)'.*?\)/s", $actions, $contents, $fileOnly, $prefixRequired, $byFile );
			}
		}
		
		// Return
		switch ( $returnFormat ) {
			// Array
			case 'array':
				return array(
					'filters' => $filters,
					'actions' => $actions,
				);
				break;
			
			// HTML (default)
			case 'html':
			default:
				$html = '';
				if ( $extractFilters && count( $filters ) > 0 ) {
					$html .= $this->html( $filters, $byFile, 'filters', $cssClasses );
				}
				if ( $extractActions && count( $actions ) > 0 ) {
					$html .= $this->html( $actions, $byFile, 'actions', $cssClasses );
				}
				
				return $html;
				break;
		}

	}
	
	/**
	* Performs a regex search on the given file contents, returning an array
	* of matches.
	*
	* @since 1.0.0
	*
	* @param	string		$regex 			Regular Expression
	* @param	string		$results		Existing Results
	* @param	string		$contents		File Contents
	* @param	string		$fileOnly		Filename (excluding full path)
	* @param	string		$prefixRequired	Optional prefix string required on filters and actions for inclusion in resultset (false = don't filter any found filters/actions)
	* @param	bool		$byFile			Deliminate array results by file
	* @return	array		Matches
	*/
	private function find_matches( $regex, $results, $contents, $fileOnly, $prefixRequired, $byFile ) {
		
		preg_match_all( $regex, $contents, $matches );
		
		if ( count( $matches[0] ) == 0 ) {		
			return $results;
		}
		
		// Iterate through matches, adding them to $results
		foreach ( $matches[0] as $key => $functionCall ) {
			// Get name
			$name = $matches[1][$key];
			
			// If prefix is required, check it matches
			if ( $prefixRequired !== false && strpos( $name, $prefixRequired ) === false ) {
				// Prefix not found - ignore this item
				continue;
			}
			
			// Add to array
			if ( $byFile ) {
				$results[ $fileOnly ][ $name ] = $functionCall;
			} else {
				$results[ $name ] = $functionCall;
			}
		}
		
		return $results;
		
	}
	
	/**
	* Returns a HTML table of the given results
	*
	* @since 1.0.0
	*
	* @param 	array	$results 		Results
	* @param	bool	$byFile	 		Deliminate results by file
	* @param 	string 	$type 	 		Type (actions|filters)
	* @param 	string 	$cssClasses 	Table CSS Classes (optional)
	* @return	string	HTML
	*/
	private function html( $results, $byFile, $type = 'actions', $cssClasses = '' ) {

		// Generate title based on type
		$title = ( ( $type == 'actions' ) ? __( 'Action Name', $this->plugin->name ) : __( 'Filter Name', $this->plugin->name ) );

		$html = '<table' . ( ! empty( $cssClasses ) ? ' class="' . $cssClasses . '"' : '' ) . '>
		<thead>
			<tr>
				' . ( $byFile ? '<th>File</th>' : '' ) . '
				<th>' . $title . '</th>
				<th>' . __( 'Arguments', $this->plugin->name ) . '</th>
			</tr>
		</thead>
		<tbody>';
		
			foreach ( $results as $key => $value ) {
				if ( $byFile ) {
					// File
					$html .= '<tr>
						<td colspan="3">' . $key . '</td>
					</tr>';
					
					// Filters/Actions
					foreach ( $value as $name => $functionCall ) {
						$html .= $this->html_row( $name, $functionCall, $byFile );
					}
				} else {
					// Filters/Actions
					$html .= $this->html_row( $key, $value );
				}
			}
		
		$html .=
		'</tbody>
		</table>';
		
		return $this->minify( $html );
	}
	
	/**
	* Returns a HTML table row of the given name and function call
	*
	* @since 1.0.0
	*
	* @param 	string	$name			Filter/Action Name
	* @param	string	$functionCall	Function Call
	* @param	bool	$byFile	 		Deliminate results by file
	* @return	string	HTML
	*/
	private function html_row( $name, $functionCall, $byFile ) {
		return '<tr>
			' . ( $byFile ? '<td>&nbsp;</td>' : '' ) . '
			<td>'.$name.'</td>
			<td>'.$functionCall.'</td>
		</tr>';
	}
	
	/**
     * Helper method to minify a string of data.
     *
     * @since 1.0.0
     *
     * @param string $string  String of data to minify.
     * @return string $string Minified string of data.
     */
    private function minify( $string ) {
	    
        $clean = preg_replace( '/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $string );
        $clean = str_replace( array( "\r\n", "\r", "\t", "\n", '  ', '    ', '     ' ), '', $clean );
        return $clean;

    }
	
}

// Init
$extractFiltersActions = new ExtractFiltersActions();
?>
