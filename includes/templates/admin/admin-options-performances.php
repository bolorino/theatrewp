<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Theatre WP Options', 'theatrewp' ); ?></h2>
	<p>
		v <?php echo get_option( 'twp_version' ); ?>
	</p>

	<h3><?php _e( 'Performances', 'theatrewp' ); ?></h3>

	<div class="twp-options" style="display:inline-block;width:48%">
		<form action="options.php" method="post">
			<?php settings_fields( 'twp-main' );
			do_settings_sections( 'theatre-main' );
			?>
			<table class="form-table">
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
		        	<th scope="row"><?php _e( 'Number of Performances per page', 'theatrewp' );?> </th>
		        	<td><input type="text" size="2" name="twp_performances_number" value="<?php echo get_option( 'twp_performances_number' ); ?>" /></td>
		        </tr>
		    </table>
			<?php submit_button(); ?>
		</form>
	</div> <!-- twp-options -->
	<?php include( 'admin-options-sidebar.php' ); ?>
</div>

