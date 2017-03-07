<?php
/**
 * Template for displaying user's orders
 *
 * @author ThimPress
 * @package LearnPress/Template
 * @version 1.0
 */
defined( 'ABSPATH' ) || exit();
?>
<?php if( $orders = $user->get_orders() ): ?>

<table class="table-orders">
	<thead>
		<th><?php _e( 'Order', 'eduma' );?></th>
		<th><?php _e( 'Date', 'eduma' );?></th>
		<th><?php _e( 'Status', 'eduma' );?></th>
		<th><?php _e( 'Total', 'eduma' );?></th>
		<th><?php _e( 'Action', 'eduma' );?></th>
	</thead>
	<tbody>
	<?php foreach( $orders as $order ): $order = learn_press_get_order( $order );?>
		<tr>
			<td><?php echo $order->get_order_number();?></td>
			<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></td>
			<td>
                            <?php echo $order->get_order_status();?>
                            <?php
                                if ( $order->has_status( 'pending' ) ) :
                                    printf( '(<small><a href="%s" class="%s">%s</a></small>)', $order->get_cancel_order_url(), 'cancel-order', __( 'Cancel', 'eduma' ) );
                                endif;
                            ?>
                        </td>
			<td><?php echo $order->get_formatted_order_total();?></td>
			<td>
				<?php
				$actions['view'] = array(
					'url'  => $order->get_view_order_url(),
					'text' => __( 'View', 'eduma' )
				);
				$actions = apply_filters( 'learn_press_user_profile_order_actions', $actions, $order );

				foreach( $actions as $slug => $option ){
					printf( '<a href="%s">%s</a>', $option['url'], $option['text'] );
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
	<?php learn_press_display_message( __( 'You have not got any orders yet!', 'eduma' ), 'notice' ); ?>
<?php endif; ?>
