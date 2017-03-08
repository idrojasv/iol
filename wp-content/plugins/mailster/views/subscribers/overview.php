
<div class="wrap">
<h1><?php esc_html_e( 'Subscribers', 'mailster' ) ?>
<?php if ( current_user_can( 'mailster_add_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_subscribers&new" class="add-new-h2"><?php esc_html_e( 'Add New', 'mailster' );?></a>
<?php endif; ?>
<?php if ( current_user_can( 'mailster_import_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_subscriber-manage&tab=import" class="add-new-h2"><?php esc_html_e( 'Import', 'mailster' );?></a>
<?php endif; ?>
<?php if ( current_user_can( 'mailster_export_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_subscriber-manage&tab=export" class="add-new-h2"><?php esc_html_e( 'Export', 'mailster' );?></a>
<?php endif; ?>
<?php if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) : ?>
	<span class="subtitle"><?php printf( __( 'Search result for %s', 'mailster' ), '&quot;' . esc_html( $_GET['s'] ) . '&quot;' ) ?></span>
	<?php endif; ?>
</h1>
<?php

$table = new Mailster_Subscribers_Table();

$table->prepare_items();
$table->search_box( __( 'Search Subscribers', 'mailster' ), 's' );
$table->views();
?><form method="post" action="" id="subscribers-overview-form">
<?php

$counts = $this->get_count_by_status();
if ( isset( $_GET['status'] ) && isset( $counts[ $_GET['status'] ] ) ) :

	$count = intval( $counts[ $_GET['status'] ] );
	$status = $this->get_status( intval( $_GET['status'] ), true );
	$text = sprintf( __( 'Do you like to select all %1$d subscribers with status %2$s?', 'mailster' ), $count, '"' . $status . '"' );
	else :
		$count = array_sum( $counts );

		$text = sprintf( __( 'Do you like to select all %s subscribers?', 'mailster' ), number_format_i18n( $count ) );

endif;
?>
<input type="hidden" name="all_subscribers" id="all_subscribers" data-label="<?php echo esc_attr( $text ) ?>" data-count="<?php echo $count ?>" value="0">
<?php $table->display(); ?>
</form>
</div>
