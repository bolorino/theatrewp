<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options', 'theatrewp' ); ?></h2>
	<p>
		v <?php echo get_option( 'twp_version' ); ?>
	</p>
	<h3><?php _e( 'Shows', 'theatrewp' ); ?></h3>

	<div class="twp-options" style="display:inline-block;width:48%">
		<form action="options.php" method="post">
			<?php settings_fields( 'twp-main' );
			do_settings_sections( 'theatre-main' );
			?>
			<table class="form-table">
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Your Singular Spectacles name (Spectacle, Choreography, Show, Production...)', 'theatrewp' );?> </th>
		        	<td><input type="text" name="twp_spectacle_name" value="<?php echo get_option( 'twp_spectacle_name' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Your Plural Spectacles name (Spectacles, Choreographies, Shows, Productions...)', 'theatrewp' );?> </th>
		        	<td><input type="text" name="twp_spectacles_name" value="<?php echo get_option( 'twp_spectacles_name' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Single Spectacle slug', 'theatrewp' );?> </th>
		        	<td><input type="text" name="twp_spectacle_slug" value="<?php echo get_option( 'twp_spectacle_slug' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Plural Spectacles slug', 'theatrewp' );?> </th>
		        	<td><input type="text" name="twp_spectacles_slug" value="<?php echo get_option( 'twp_spectacles_slug' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Number of Shows per page', 'theatrewp' );?> </th>
		        	<td><input type="text" size="2" name="twp_spectacles_number" value="<?php echo get_option( 'twp_spectacles_number' ); ?>" /></td>
		        </tr>
		    </table>
		    <input type="hidden" name="twp_performance_name" value="<?php echo get_option( 'twp_performance_name' ); ?>" />
		    <input type="hidden" name="twp_performances_name" value="<?php echo get_option( 'twp_performances_name' ); ?>" />
		    <input type="hidden" name="twp_performance_slug" value="<?php echo get_option( 'twp_performance_slug' ); ?>" />
		    <input type="hidden" name="twp_performances_slug" value="<?php echo get_option( 'twp_performances_slug' ); ?>" />
		    <input type="hidden" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" />
		    <input type="hidden" name="twp_single_sponsor" value="<?php echo get_option( 'twp_single_sponsor' ); ?>" />
		    <input type="hidden" name="twp_google_maps_api" value="<?php echo get_option( 'twp_google_maps_api' ); ?>" />
		    <input type="hidden" name="twp_clean_on_uninstall" value="<?php echo get_option( 'twp_clean_on_uninstall' ); ?>" />
			<?php submit_button(); ?>
		</form>
	</div> <!-- twp-options -->
	<?php include( 'admin-options-sidebar.php' ); ?>
</div>

