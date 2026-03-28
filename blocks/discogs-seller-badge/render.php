<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$align = !empty( $attributes['align'] ) ? esc_attr( $attributes['align'] ) : '';
$anchor = !empty( $attributes['anchor'] ) ? esc_attr( $attributes['anchor'] ) : esc_attr('wp-block-' . wp_rand());
$blockID = !empty( $attributes['blockID'] ) ? esc_attr( $attributes['blockID'] ) : esc_attr('wp-block-' . wp_rand());
$is_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$wrapper_attributes = get_block_wrapper_attributes([
	'id' => $anchor,
	'class' => 'gf-blocks-for-discogs'
]);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$settings = get_option('gf_blocks_for_discogs__settings');

// USERNAME
if (!empty($attributes['username'])) :
	$username = preg_replace('/[^A-Za-z0-9._-]/', '', $attributes['username']);
else : 
	$username = !empty($settings['username']) ? $settings['username'] : '';
endif;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Build input-border string:
if ( !empty( $attributes['badge_wrap_border'] ) && is_array( $attributes['badge_wrap_border'] ) ) {
    $ib = $attributes['badge_wrap_border'];
    $css__badge_wrap_border = !empty( $ib['color'] ) ? 'border-color:' . esc_attr( $ib['color'] ) . ';'   : '';
    $css__badge_wrap_border .= !empty( $ib['style'] ) ? 'border-style:' . esc_attr( $ib['style'] ) . ';'   : '';
    $css__badge_wrap_border .= !empty( $ib['width'] ) ? 'border-width:' . esc_attr( $ib['width'] ) . ';'   : '';
    $css__badge_wrap_border .= !empty( $ib['radius'] ) ? 'border-radius:' . esc_attr( $ib['radius'] ) . ';' : '';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$discogs = new BlocksForDiscogsSellers_API();
$data = $discogs->fetch(['endpoint' => 'profile', 'username' => $username]);

if (!empty($data['error']) ) {
	echo wp_kses($discogs->render_error_html($data), 'post');
	return;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<div <?php echo wp_kses_post(!$is_editor ? $wrapper_attributes : ''); ?>>

	<section id="<?php echo esc_attr($blockID); ?>" class="discogs-seller-badge">

		<?php if (!empty($data)) : ?>

		<div class="discogs-badge-wrap" style="<?php echo esc_html($css__badge_wrap_border ?? 'border:1px solid #ccc'); ?>">

			<div class="discogs-badge-graphics">

				<div class="discogs-badge-avatar">
					<a title="<?php esc_html_e('View Seller Inventory','blocks-for-discogs-sellers'); ?>" href="<?php echo esc_url('https://www.discogs.com/seller/'.$data['username'].'/profile'); ?>" target="_blank">
						<img src="<?php echo esc_url($data['avatar_url']); ?>" width="100" height="100" alt="<?php echo esc_url($data['username'].' Profile Image'); ?>">
					</a>
				</div>

			</div>
			
			<div class="discogs-badge-content">

				<div class="discogs-badge-title">
					<h2 style="<?php if (!empty($attributes['badge_seller_name_color'])) { echo 'color:'.esc_html($attributes['badge_seller_name_color']); } ?>"><?php echo esc_html($attributes['display_profile_name_or_username'] == 'username' ? $data['username'] : $data['name']); ?></h2>
				</div>

				<div class="discogs-badge-stats">

					<div class="discogs-badge-seller-rating">

						<!-- ⭐ Pure SVG 5-Star Rating — set rect width = rating * 24 -->
						<svg viewBox="0 0 120 24" width="120" height="24" xmlns="http://www.w3.org/2000/svg" aria-label="Rating: <?php echo esc_html($data['seller_rating_stars']); ?> out of 5">
							<defs>
								<!-- Star shape -->
								<path id="star" d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/>
								<!-- Mask to reveal only a portion of the gold stars -->
								<mask id="fill-mask" maskUnits="userSpaceOnUse" maskContentUnits="userSpaceOnUse">
								<!-- White = visible; Black = hidden -->
								<rect x="0" y="0" width="<?php echo esc_html($data['seller_rating_stars'] * 24); ?>" height="24" fill="#fff"/>
								</mask>
							</defs>

							<!-- Empty (gray) stars -->
							<g fill="#ddd" stroke="#000" stroke-width="0">
								<g transform="translate(0,0)"> <use href="#star" width="24" height="24"/></g>
								<g transform="translate(24,0)"><use href="#star" width="24" height="24"/></g>
								<g transform="translate(48,0)"><use href="#star" width="24" height="24"/></g>
								<g transform="translate(72,0)"><use href="#star" width="24" height="24"/></g>
								<g transform="translate(96,0)"><use href="#star" width="24" height="24"/></g>
							</g>

							<!-- Filled (gold) stars, revealed only by the mask width -->
							<g fill="<?php echo esc_html($attributes['badge_stars_color'] ?? '#f5b301'); ?>" stroke="#000" stroke-width="0" mask="url(#fill-mask)">
								<g transform="translate(0,0)"> <path d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/></g>
								<g transform="translate(24,0)"><path d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/></g>
								<g transform="translate(48,0)"><path d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/></g>
								<g transform="translate(72,0)"><path d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/></g>
								<g transform="translate(96,0)"><path d="M12 .9l3.4 6.9 7.6 1.1-5.5 5.3 1.3 7.4L12 18.7 5.2 21.6l1.3-7.4L1 8.9l7.6-1.1L12 .9z"/></g>
							</g>
						</svg>

						<small>
							<a style="color:<?php echo esc_html($attributes['badge_ratings_color'] ?? 'inherit'); ?>" href="https://www.discogs.com/sell/seller_feedback/<?php echo esc_html($data['username']); ?>" title="<?php esc_html_e('View Seller Feedback','blocks-for-discogs-sellers'); ?>" target="_blank">
								<p class="discogs-badge-rating-percentage">
									<?php echo esc_html($data['seller_rating']); ?>% from <?php echo esc_html(number_format($data['seller_num_ratings'],0,'.',',')); ?> <?php esc_html_e('buyers','blocks-for-discogs-sellers'); ?>
								</p>
							</a>
						</small>
						
					</div>

					<div class="discogs-badge-num-for-sale">
						<p>
							<a style="color:<?php echo esc_html($attributes['badge_items_for_sale_color'] ?? 'inherit'); ?>" title="<?php esc_html_e('View Seller Inventory','blocks-for-discogs-sellers'); ?>" href="<?php echo esc_url('https://www.discogs.com/seller/'.$data['username'].'/profile'); ?>" target="_blank">
								<strong><?php echo esc_html(number_format($data['num_for_sale'], 0,'.',',')); ?></strong> <?php esc_html_e('Items for Sale','blocks-for-discogs-sellers'); ?>
							</a>
						</p>
					</div>

				</div>

			</div>

			<div class="discogs-badge-background" style="background-color:<?php echo esc_html($attributes['badge_background_color'] ?? '#FFFFFF') ?>">
				<?php
				if (!empty($attributes['display_banner_background'])) : ?>
					<?php if (!empty($data['banner_url'])) : ?>
						<img src="<?php echo esc_url($data['banner_url']); ?>" style="opacity:<?php echo esc_html($attributes['badge_banner_opacity'] ? ($attributes['badge_banner_opacity'] * .01) : .05); ?>">
					<?php endif; ?>
				<?php endif; ?>
			</div>

		</div>
		<?php endif; ?>

	</section>

</div>