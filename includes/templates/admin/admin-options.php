<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options ', 'theatrewp' ); ?></h2>
	<p>
		v <?php echo get_option( 'twp_version' ); ?>
	</p>

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
	        	<th scope="row"><?php _e( 'Your Singular Performances name (Performance, Play, Gig...)', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_performance_name" value="<?php echo get_option( 'twp_performance_name' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Your Plural Performances name (Performances, Plays, Gigs...)', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_performances_name" value="<?php echo get_option( 'twp_performances_name' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Single Performance slug', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_performance_slug" value="<?php echo get_option( 'twp_performance_slug' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Plural Performances slug', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_performances_slug" value="<?php echo get_option( 'twp_performances_slug' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Number of Shows per page', 'theatrewp' );?> </th>
	        	<td><input type="text" size="2" name="twp_spectacles_number" value="<?php echo get_option( 'twp_spectacles_number' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Number of Performances per page', 'theatrewp' );?> </th>
	        	<td><input type="text" size="2" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" /></td>
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
		<?php submit_button(); ?>
	</form>
</div>

