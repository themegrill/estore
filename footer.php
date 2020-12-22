<?php
/**
 * Theme Footer Section for our theme.
 *
 * Displays all of the footer section and starting from <footer> tag.
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 0.1
 */
?>

	  <footer id="colophon">
		 <?php get_sidebar( 'footer' ); ?>
		 <div id="bottom-footer" class="clearfix">
			<div class="tg-container">
				<div class="copy-right">
					<?php printf( esc_html__( 'Copyright &copy; %1$s %2$s. All rights reserved.', 'estore' ), date( 'Y' ), '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" ><span>' . get_bloginfo( 'name', 'display' ) . '</span></a>' ); ?>
					<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'estore' ), '<a href="' . esc_url( 'https://themegrill.com/themes/estore/' ) . '" target="_blank" rel="nofollow">eStore</a>', 'ThemeGrill' ); ?>
					<?php printf( esc_html__( 'Powered by %s.', 'estore' ), '<a href="' . esc_url( 'https://wordpress.org/' ) . '" target="_blank" rel="nofollow">WordPress</a>' ); ?>
				</div>
				<?php
				$logos = array();
				for ( $i = 1; $i < 5; $i++ ) {
					$paymentlogo = get_theme_mod( 'estore_payment_logo' . $i );
					if ( $paymentlogo ) {
						array_push( $logos, $paymentlogo );
					}
				}
				$totallogo = count( $logos );
				if ( $totallogo > 0 ) {
					?>
					<div class="payment-partner-wrapper">
						<ul>
						<?php for ( $j = 0; $j < $totallogo; $j++ ) { ?>
							<li><img src="<?php echo esc_url( $logos[ $j ] ); ?>" /></li>
						<?php } ?>
						</ul>
					</div>
				<?php } ?>
			</div>
		</div>
	  </footer>
	  <a href="#" class="scrollup"><i class="fa fa-angle-up"> </i> </a>
   </div> <!-- Page end -->
   <?php wp_footer(); ?>
</body>
</html>
