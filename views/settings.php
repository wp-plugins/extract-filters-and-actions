<div class="wrap">
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
    		
    			<!-- Form Start -->
		        <form id="post" name="post" method="post" action="plugins.php?page=<?php echo $this->plugin->name; ?>">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
		                <div class="postbox">
		                    <h3 class="hndle"><?php _e( 'Extract', $this->plugin->name ); ?></h3>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e( 'Plugin', $this->plugin->name ); ?></strong>
		                    		<select name="<?php echo $this->plugin->name; ?>[plugin]" size="1">
			                    		<?php
			                    		foreach ( $plugins as $folder => $plugin ) {
				                    		?>
				                    		<option value="<?php echo $folder; ?>"<?php selected( $this->settings['plugin'], $folder, true ); ?>><?php echo $plugin; ?></option>
				                    		<?php
			                    		}
			                    		?>
		                    		</select>
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'The plugin to extract filters and actions from. Plugin\'s files and folders will be searched recursively to find all PHP files.', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e( 'Prefix', $this->plugin->name ); ?></strong>
		                    		<input type="text" name="<?php echo $this->plugin->name; ?>[prefix]" value="<?php echo ( isset( $this->settings['prefix'] ) ? $this->settings['prefix'] : '' ); ?>" />
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'Only return filters and actions which start with the given prefix. Useful if you only want a list of filters and actions specific to your Plugin.', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Include Filters', $this->plugin->name); ?></strong>
		                    		
		                    		<label for="filters">
		                    			<input type="checkbox" name="<?php echo $this->plugin->name; ?>[filters]" id="filters" value="1"<?php checked( ( isset( $this->settings['filters'] ) ? 1 : 0 ), 1, true ); ?>
			                    	</label>
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'Searches for all <b>apply_filters</b> function calls, including them in the output.', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Include Actions', $this->plugin->name); ?></strong>
		                    		
		                    		<label for="actions">
		                    			<input type="checkbox" name="<?php echo $this->plugin->name; ?>[actions]" id="actions" value="1"<?php checked( ( isset( $this->settings['actions'] ) ? 1 : 0 ), 1, true ); ?>
			                    	</label>
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'Searches for all <b>do_action</b> function calls, including them in the output.', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                </div>
		                <!-- /postbox -->
		                
		                <div class="postbox">
		                    <h3 class="hndle"><?php _e( 'Output', $this->plugin->name ); ?></h3>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e( 'Format', $this->plugin->name ); ?></strong>
		                    		<select name="<?php echo $this->plugin->name; ?>[format]" size="1">
			                    		<option value="html"<?php selected( $this->settings['format'], 'html', true); ?>><?php _e( 'HTML', $this->plugin->name ); ?></option>
			                    		<option value="array"<?php selected( $this->settings['format'], 'array', true); ?>><?php _e( 'PHP Array', $this->plugin->name ); ?></option>
		                    		</select>
		                    	</p>
		                    </div>

		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e( 'Table CSS Classes', $this->plugin->name ); ?></strong>
		                    		<input type="text" name="<?php echo $this->plugin->name; ?>[classes]" value="<?php echo ( isset( $this->settings['classes'] ) ? $this->settings['classes'] : '' ); ?>" />
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'If outputting as HTML, optionally specify CSS classes for the table element.', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e( 'Breakdown by File', $this->plugin->name ); ?></strong>
		                    		
		                    		<label for="byFile">
		                    			<input type="checkbox" name="<?php echo $this->plugin->name; ?>[byFile]" id="byFile" value="1"<?php checked( ( isset( $this->settings['byFile'] ) ? 1 : 0 ), 1, true ); ?>
			                    	</label>
		                    	</p>
		                    	<p class="description">
			                    	<?php _e( 'If enabled, filters and actions are output by file. Duplicates in the output are combined by file. If disabled, filters and actions are output, with duplicates in the output across files removed. ', $this->plugin->name ); ?>
		                    	</p>
		                    </div>
		                    
		                    <?php
			                // Output, if set
			                if ( isset( $output ) ) {
				                ?>
				                <div class="option">
			                    	<textarea id="output" style="width:100%;height:400px;"><?php echo $output; ?></textarea>
			                    </div>
				                <?php
			                }
			                ?>
		                </div>
		                <!-- /postbox -->
		                
		                <?php
			            // Ouptut, if set and = HTML
			            if ( isset( $output ) && $this->settings['format'] == 'html' ) {
				            ?>
				            <div class="postbox">
			                    <h3 class="hndle"><?php _e( 'Output Preview', $this->plugin->name ); ?></h3>
					            <div class="option">
						            <?php echo $output; ?>
					            </div>
				            </div>
				            <?php
			            }
		                ?>
		                
		            	<!-- Save -->
		                <div class="submit">
		                	<?php wp_nonce_field( $this->plugin->name, $this->plugin->name.'_nonce' ); ?>
		                    <input type="submit" name="submit" value="<?php _e( 'Extract', $this->plugin->name ); ?>" class="button button-primary" /> 
		                </div>
					</div>
					<!-- /normal-sortables -->
			    </form>
			    <!-- /form end -->
    			
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once( $this->plugin->folder.'/_modules/dashboard/views/sidebar-donate.php' ); ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div>        
</div>