<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options', 'theatrewp' ); ?></h2>
	<p>
		v <?php echo get_option( 'twp_version' ); ?>
	</p>

	<h3><?php _e( 'Advanced', 'theatrewp' ); ?></h3>

	<div class="twp-options" style="display:inline-block;width:48%">
		<form action="options.php" method="post">
			<?php settings_fields( 'twp-main' );
			do_settings_sections( 'theatre-main' );
			?>
			<table class="form-table">
				<tr valign="top">
		        	<th scope="row"><?php _e( 'Google Maps API (to display performance maps)', 'theatrewp' );?> </th>
		        	<td><input type="text" name="twp_google_maps_api" value="<?php echo get_option( 'twp_google_maps_api' ); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Sponsors as list in Spectacle', 'theatrewp' );?> </th>
		        	<td><input type="checkbox" name="twp_single_sponsor" <?php echo ( get_option( 'twp_single_sponsor' ) == '1' ? 'checked="checked"' : '' ); ?> value="1" /></td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Delete all data when uninstall', 'theatrewp' );?> </th>
		        	<td><input type="checkbox" name="twp_clean_on_uninstall" <?php echo ( get_option( 'twp_clean_on_uninstall' ) == '1' ? 'checked="checked"' : '' ); ?> value="1" /></td>
		        </tr>
		    </table>
		    <input type="hidden" name="twp_spectacle_name" value="<?php echo get_option( 'twp_spectacle_name' ); ?>" />
		    <input type="hidden" name="twp_spectacles_name" value="<?php echo get_option( 'twp_spectacles_name' ); ?>" />
		    <input type="hidden" name="twp_spectacle_slug" value="<?php echo get_option( 'twp_spectacle_slug' ); ?>" />
		    <input type="hidden" name="twp_spectacles_slug" value="<?php echo get_option( 'twp_spectacles_slug' ); ?>" />
		    <input type="hidden" name="twp_spectacles_number" value="<?php echo get_option( 'twp_spectacles_number' ); ?>" />
		    <input type="hidden" name="twp_performance_name" value="<?php echo get_option( 'twp_performance_name' ); ?>" />
		    <input type="hidden" name="twp_performances_name" value="<?php echo get_option( 'twp_performances_name' ); ?>" />
		    <input type="hidden" name="twp_performance_slug" value="<?php echo get_option( 'twp_performance_slug' ); ?>" />
		    <input type="hidden" name="twp_performances_slug" value="<?php echo get_option( 'twp_performances_slug' ); ?>" />
		    <input type="hidden" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" />

			<?php submit_button(); ?>
		</form>
	</div> <!-- twp-options -->
	<?php include( 'admin-options-sidebar.php' ); ?>
</div>

