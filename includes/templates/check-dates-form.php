<div id="twp-check-dates">
    <form name="twpdates" action="<?php echo $action_form; ?>" method="POST">
        <select name="twpm" id="month">
            <?php foreach ( $calendar_data['month_names'] as $key => $value ) { ?>
                <option value="<?php echo $key; ?>"<?php if ( $key == $selected_month ) {?> selected="selected"<?php }?>><?php echo __( $value ); ?></option>
            <?php } ?>
        </select>

        <select name="twpy" id="year">
            <?php for ( $n = $calendar_data['current_year']; $n >= $calendar_data['first_available_year']; $n--) { ?>
                <option value="<?php echo $n;?>"<?php if ($n == $selected_year) {?> selected="selected"<?php }?>><?php echo $n; ?></option>
            <?php } ?>
        </select>

        <input type="submit" name="submit" value="<?php echo __( 'Check dates', 'theatrewp' ); ?>" />
    </form>
</div> <!--  twp-check-dates -->
