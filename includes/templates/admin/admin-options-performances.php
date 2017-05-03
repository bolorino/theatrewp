<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options', 'theatre-wp' ); ?></h2>
	<p>
		v <?php echo get_option( 'twp_version' ); ?>
	</p>

	<h3><?php _e( 'Performances', 'theatre-wp' ); ?></h3>

	<div class="twp-options" style="display:inline-block;width:48%">
		<form action="options.php" method="post">
			<?php settings_fields( 'twp-main' );
			do_settings_sections( 'theatre-main' );
			?>
			<table class="form-table">
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Your Singular Performances name (Performance, Play, Gig...)', 'theatre-wp' );?> </th>
		        	<td><input type="text" name="twp_performance_name" value="<?php echo get_option( 'twp_performance_name' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Your Plural Performances name (Performances, Plays, Gigs...)', 'theatre-wp' );?> </th>
		        	<td><input type="text" name="twp_performances_name" value="<?php echo get_option( 'twp_performances_name' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Single Performance slug', 'theatre-wp' );?> </th>
		        	<td><input type="text" name="twp_performance_slug" value="<?php echo get_option( 'twp_performance_slug' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Plural Performances slug', 'theatre-wp' );?> </th>
		        	<td><input type="text" name="twp_performances_slug" value="<?php echo get_option( 'twp_performances_slug' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Number of Performances per page', 'theatre-wp' );?> </th>
		        	<td><input type="text" size="2" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" /></td>
		        </tr>

		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Enable tickets info', 'theatre-wp' );?> </th>
		        	<td><input type="checkbox" name="twp_tickets_info" <?php echo ( get_option( 'twp_tickets_info' ) == '1' ? 'checked="checked"' : '' ); ?> value="1" /></td>
		        </tr>

		    </table>
		    <input type="hidden" name="twp_spectacle_name" value="<?php echo get_option( 'twp_spectacle_name' ); ?>" />
		    <input type="hidden" name="twp_spectacles_name" value="<?php echo get_option( 'twp_spectacles_name' ); ?>" />
		    <input type="hidden" name="twp_spectacle_slug" value="<?php echo get_option( 'twp_spectacle_slug' ); ?>" />
		    <input type="hidden" name="twp_spectacles_slug" value="<?php echo get_option( 'twp_spectacles_slug' ); ?>" />
		    <input type="hidden" name="twp_spectacles_number" value="<?php echo get_option( 'twp_spectacles_number' ); ?>" />
		    <input type="hidden" name="twp_single_sponsor" value="<?php echo get_option( 'twp_single_sponsor' ); ?>" />
		    <input type="hidden" name="twp_google_maps_api" value="<?php echo get_option( 'twp_google_maps_api' ); ?>" />
		    <input type="hidden" name="twp_clean_on_uninstall" value="<?php echo get_option( 'twp_clean_on_uninstall' ); ?>" />
			<?php submit_button(); ?>
		</form>
	</div> <!-- twp-options -->
	<?php include( 'admin-options-sidebar.php' ); ?>
</div>

