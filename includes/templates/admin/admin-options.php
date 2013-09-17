<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options ', 'theatrewp' ); ?></h2>
	<form action="options.php" method="post">
		<?php settings_fields( 'twp-main' );
		do_settings_sections( 'theatre-main' );
		?>
		<table class="form-table">
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Your Spectacles name (Spectacles, Choreographies, Shows...)', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_spectacle_name" value="<?php echo get_option( 'twp_spectacle_name' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Your Performances name (Performances, Plays, Gigs...)', 'theatrewp' );?> </th>
	        	<td><input type="text" name="twp_performance_name" value="<?php echo get_option( 'twp_performance_name' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Number of Shows per page', 'theatrewp' );?> </th>
	        	<td><input type="text" size="2" name="twp_spectacles_number" value="<?php echo get_option( 'twp_spectacles_number' ); ?>" /></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e( 'Number of Performances per page', 'theatrewp' );?> </th>
	        	<td><input type="text" size="2" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" /></td>
	        </tr>
	    </table>
		<?php submit_button(); ?>
	</form>
</div>
