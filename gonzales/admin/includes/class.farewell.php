<?php

/**
 * Class WGZ_Farewell handles notices that are displayed to user about Assets Manager transition.
 *
 * @since      2.2.0
 * @author     Webcraftic
 * @package    AssetsManager
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2025, Webcraftic
 */
class WGZ_Farewell {

	/**
	 * Meta key where the option (of the time the Farewell notice was dismissed) is saved.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	const SLUG_DISMISS_TIME = 'wgz_farewell_dismissed';

	/**
	 * Super Page Cache plugin page URL.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	const URL_SUPER_PAGE_CACHE = 'https://wordpress.org/plugins/wp-cloudflare-page-cache/';

	/**
	 * WGZ_Farewell constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		add_action( 'admin_head', [ $this, 'process_notices' ] );
	}

	/**
	 * Decide whether to display notices or not.
	 *
	 * @since 2.2.0
	 */
	public function process_notices() {

		/**
		 * Current screen object.
		 *
		 * @var WP_Screen $screen
		 */
		$screen = get_current_screen();

		if ( ! empty( $screen->base ) && ($screen->base === 'dashboard' || $screen->base === 'settings_page_assets-manager-wbcr_gonzales' ) ) {

			// Verify nonce and sanitize input before processing dismissal
			if ( isset( $_GET['try_super_page_cache'] ) && isset( $_GET['_wpnonce'] ) ) {
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wgz_dismiss_farewell' ) ) {
					$farewell = empty( $_GET['try_super_page_cache'] ) ? time() : 0;
					update_user_meta( get_current_user_id(), 'wgz_farewell_dismissed', $farewell );
				}
			}

			$farewell = get_user_meta( get_current_user_id(), 'wgz_farewell_dismissed', true );

			if ( empty( $farewell ) ) {
				$this->display_detailed_notice();
			}
		}

		if ( $this->is_grace_period_ended() ) {
			$this->display_short_notice();
		}
	}

	/**
	 * Compare dates since detailed notice dismissal date and now.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	protected function is_grace_period_ended() {

		$dismissed = (int) get_user_meta( get_current_user_id(), 'wgz_farewell_dismissed', true );
        if(empty($dismissed)) {
            return false;
        }
		return (time() - $dismissed) >  MONTH_IN_SECONDS;
	}

	/**
	 * Dismissable big dashboard notice about Assets Manager transition.
	 *
	 * @since 2.2.0
	 */
	public function display_detailed_notice() {

		// Only appropriate people should see it.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		?>

		<div id="try-super-page-cache-panel" class="try-super-page-cache-panel">
			<a
					class="try-super-page-cache-panel-close" href="<?php echo esc_url( wp_nonce_url( admin_url( '?try_super_page_cache=0' ), 'wgz_dismiss_farewell' ) ); ?>"
					aria-label="<?php esc_attr_e( 'Dismiss the Try Super Page Cache panel', 'gonzales' ); ?>">
				<?php esc_html_e( 'Dismiss', 'gonzales' ); ?>
			</a>

			<div class="try-super-page-cache-panel-content">
				<h2><?php esc_html_e( 'A More Powerful Assets Manager is Here!', 'gonzales' ); ?></h2>

				<p class="about-description">
					<?php esc_html_e( 'Assets Manager has been acquired and is now part of Super Page Cache. Get all your Assets Manager features plus powerful caching capabilities in one modern plugin.', 'gonzales' ); ?>
				</p>

				<hr/>

				<div class="try-super-page-cache-panel-column-container">
					<div class="try-super-page-cache-panel-column try-super-page-cache-panel-image-column">
						<picture>
							<source srcset="about:blank" media="(max-width: 1024px)">
							<img
									src="<?php echo esc_url( WGZ_PLUGIN_URL . '/admin/assets/img/super-page-cache-screenshot.png' ); ?>"
									alt="<?php esc_attr_e( 'Screenshot from the Super Page Cache interface', 'gonzales' ); ?>"/>
						</picture>
					</div>
					<div class="try-super-page-cache-panel-column plugin-card-super-page-cache">

						<div>
							<h3><?php esc_html_e( 'Switch to Super Page Cache today.', 'gonzales' ); ?></h3>

							<p>
								<?php esc_html_e( 'Super Page Cache includes everything you love about Assets Manager - control which CSS and JavaScript files load on any page with an intuitive frontend interface. Plus, you get powerful full-page caching with Cloudflare CDN integration, automatic cache purging, advanced lazy loading, and defer/delay JavaScript capabilities for even better performance.', 'gonzales' ); ?>
							</p>

							<p>
								<?php esc_html_e( 'Trusted by over 50,000 WordPress sites with 430+ five-star reviews. All your current Assets Manager workflows remain the same, with bonus caching features to make your site even faster.', 'gonzales' ); ?>
							</p>
						</div>

						<div class="try-super-page-cache-action">
							<p>
								<a
										class="button button-primary button-hero thickbox open-plugin-details-modal"
										href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-cloudflare-page-cache&TB_iframe=true&width=600&height=550' ) ); ?>">
									<?php esc_html_e( 'View Super Page Cache', 'gonzales' ); ?>
								</a>
							</p>

							<p>
								<a href="<?php echo esc_url( self::URL_SUPER_PAGE_CACHE ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Learn more about Super Page Cache', 'gonzales' ); ?>
								</a>
							</p>
						</div>
					</div>

					<div class="try-super-page-cache-panel-column plugin-card-classic-editor">

						<div>
							<h3><?php esc_html_e( 'We\'re retiring Assets Manager.', 'gonzales' ); ?></h3>

							<p>
								<?php esc_html_e( 'We\'re retiring the Assets Manager plugin in favor of the more powerful Super Page Cache plugin. This means that there will be no new feature updates. We will continue to maintain the Assets Manager plugin for any major security issues for the next 6 months.', 'gonzales' ); ?>
							</p>
							<p>
								<?php esc_html_e( 'We strongly recommend switching to Super Page Cache. It provides the same Assets Manager functionality you\'re using now, plus adds powerful full-page caching to make your site even faster. Active development with regular updates and new features.', 'gonzales' ); ?>
							</p>
							<?php if ( time() < strtotime( '2025-12-01 00:00:00' ) ) : ?>
								<p style="background: #f0f6fc; padding: 12px; border-left: 3px solid #2271b1; margin-top: 15px;">
									<strong><?php esc_html_e( '🎁 Special Migration Offer:', 'gonzales' ); ?></strong>
									<?php esc_html_e( 'Get 90% off Super Page Cache Pro Personal plan for the first year! Use code', 'gonzales' ); ?> 
									<code style="background: #fff; padding: 2px 6px; font-weight: bold;">MIGRATEFROMCLRF90</code>
									<?php esc_html_e( 'at checkout. Valid for 2 weeks only.', 'gonzales' ); ?>
								</p>
							<?php endif; ?>
						</div>

						<div class="try-super-page-cache-action">
							<p>
								<a
										class="button button-secondary button-hero"
										href="<?php echo esc_url( 'https://rviv.ly/rd7CAW' ); ?>" target="_blank"
										rel="noopener noreferrer">
									<?php esc_html_e( 'Get 90% Off Pro', 'gonzales' ); ?>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>

		</div>

		<script>
			jQuery( document ).ready( function() {
                if( jQuery('#WBCR').length>0){
                    jQuery( '#try-super-page-cache-panel' ).insertBefore( '#WBCR' ).show();
                }else{
                    jQuery( '#try-super-page-cache-panel' ).insertAfter( '#wpbody-content .wrap h1' ).show();
                }
			} );
		</script>

		<?php
	}

	/**
	 * Non-dismissable notice displayed to a user after detailed notice dismiss.
	 *
	 * @since        2.2.0
	 * @noinspection HtmlUnknownTarget
	 */
	public function display_short_notice() {

		echo '<div class="notice notice-error"><p>';
		printf(
			wp_kses(
				'<strong>Important:</strong> Assets Manager is being retired and will no longer receive updates or support. We recommend <a href="%1$s" class="thickbox open-plugin-details-modal">switching to Super Page Cache</a> - it includes all Assets Manager features plus adds powerful caching capabilities.',
				[
					'br'     => [],
					'strong' => [],
					'a'      => [
						'href'   => [],
						'target' => [],
                        'class'  => [],
						'rel'    => [],
					],
				]
			),
			esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-cloudflare-page-cache&TB_iframe=true&width=600&height=550' ) )
		);
		echo '</p></div>';
	}
}

