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

// RESOLVE LISTING ID
$post_id = get_the_ID();
if ( $post_id ) {
	$meta_id = get_post_meta( $post_id, 'listing_id', true );
}

if ( !empty($attributes['listing_id']) ) {
	$listing_id = $attributes['listing_id'];
	$listing_id_source = 'ATTRIBUTES';
} elseif ( !empty($meta_id) ) {
	$listing_id = sanitize_text_field( $meta_id );
	$listing_id_source = 'POSTMETA';
} elseif ( !empty($_GET['listing_id']) ) {
	$listing_id = sanitize_text_field(wp_unslash($_GET['listing_id']));
	$listing_id_source = 'GET';
} else {
	$listing_id = '';
	$listing_id_source = 'TEST';
}

// SKIP IF NO ID EXISTS
if ( empty( $listing_id ) ) {

	if ( $is_editor ) {

		$data = [
			'id'=>'XXXXXXXXXX',
			'uri'=>'XXXXXXXXXX',
			'release' => [
				'title' => 'Test Title',
				'artist' => 'Test Artist',
				'format' => 'Test Format',
				'year' => '1999',
				'description' => 'Test Artist - Test Title (Test Format)',
				'images' => [
					0 => [
						'uri' => GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_URL.'/assets/img/PLACEHOLDER.jpg'
					]
				],
				'stats' => [
					'community' => [
						'in_wantlist' => 100,
						'in_collection' => 94
					]
				],
			],
			'original_price' => [
				'formatted' => '$19.99'
			],
			'price' => [
				'currency' => 'USD'
			],
			'original_shipping_price' => [
				'formatted' => '$5.99'
			],
			'shipping_price' => [
				'value' => 5.99
			],			
			'allow_offers' => true,
			'catalog_number' => 'TEST-123',
			'comments' => 'Clean copy with minimal play. Sleeve has shelf wear.',
			'condition' => 'Near Mint (NM)',
			'sleeve_condition' => 'Very Good Plus (VG+)'
		];

	}

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (empty($data)) {
	$discogs = new BlocksForDiscogsSellers_API();
	$data = $discogs->fetch(['endpoint' =>'listing', 'id'=>$listing_id]);
}
if (!empty($data['error'])) {
	echo wp_kses($discogs->render_error_html($data), 'post');
	return;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<div <?php echo wp_kses_post(!$is_editor ? $wrapper_attributes : ''); ?>>

	<section id="<?php echo esc_attr($blockID); ?>" class="discogs-seller-listing">

		<?php if ($is_editor) { echo '<span style="position:absolute;z-index:100;top:10px;left:10px;background:#ff0000;color:#fff;padding:5px 15px;border-radius:10px;font-size:10px;display:inline-block">'.esc_html($listing_id_source).'</span>'; } ?>

		<div id="listing-<?php echo esc_attr($data['id']); ?>" class="layout-<?php echo esc_attr($attributes['layout_style'] ?? 'stacked'); ?>">

			<?php if ($attributes['display_release_image'] == true) : ?>
			<div class="discogs-release-image">
				<a target="_blank" href="<?php echo esc_url($data['uri']); ?>" title="View Listing">
					<img alt="<?php echo esc_attr($data['release']['title'].' Cover'); ?>" loading="lazy" src="<?php echo esc_url(!empty($data['release']['images'][0]['uri']) ? $data['release']['images'][0]['uri'] : GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_URL.'/assets/img/PLACEHOLDER.jpg'); ?>" >
				</a>
			</div>
			<?php endif; ?>

			<div class="discogs-release-information">

				<div class="discogs-release-titles">

					<?php
					if ($attributes['title_display'] == 'artist_title') :

						if ( ! empty($attributes['display_release_title']) ) :
							echo '<h5 class="discogs-listing-title">'.esc_html($data['release']['title']).'</h5>';
						endif;

						if ( ! empty($attributes['display_release_artist']) ) :
							echo '<h6 class="discogs-listing-artist">'.esc_html($data['release']['artist']).'</h6>';
						endif;

					else :

						if ( ! empty($attributes['display_release_title']) ) :
							echo '<h5 class="discogs-listing-description">'.esc_html($data['release']['description']).'</h5>';
						endif;

					endif;
					
					if ( ! empty($attributes['display_item_price']) ) :
						if ($data['status'] != 'Sold') :
							echo '<p class="discogs-listing-price">'.esc_html($data['original_price']['formatted'].' '.$data['price']['currency']).'</p>';
						else :
							echo '<p class="discogs-listing-price sold">SOLD</p>';
						endif;
					endif;

					if ( ! empty($attributes['display_item_comments']) && $data['status'] != 'Sold' ) :
						echo '<p class="discogs-listing-comments"><em>'.esc_html($data['comments']).'</em></p>';
					endif;

					?>

				</div>

				<div class="discogs-release-meta">
					<?php
					if ( ! empty( $attributes['display_release_format'] ) ) {
						echo '<span class="discogs-release-meta-format">' . esc_html__( 'Format:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $data['release']['format'] ?? '' ) . '</span>';
					}

					if ( ! empty( $attributes['display_release_year'] ) ) {
						echo '<span class="discogs-release-meta-year">' . esc_html__( 'Year:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( (int)$data['release']['year'] > 0 ? (int)$data['release']['year'] : 'N/A' ) . '</span>';
					}

					if ( ! empty( $attributes['display_release_catalog_number'] ) ) {
						echo '<span class="discogs-release-meta-catalog">' . esc_html__( 'Catalog:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $data['release']['catalog_number'] ?? 'N/A' ) . '</span>';
					}

					if ( ! empty( $attributes['display_item_grading'] ) ) {
						echo '<span class="discogs-release-meta-grading">' . esc_html__( 'Media:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $data['condition'] ?? '' ) . '<br>' . esc_html__( 'Sleeve:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $data['sleeve_condition'] ?? '' ) . '</span>';
					}

					if ( ! empty( $attributes['display_release_stats'] ) ) {
						$wantlist   = $data['release']['stats']['community']['in_wantlist'] ?? '';
						$collection = $data['release']['stats']['community']['in_collection'] ?? '';

						echo '<span class="discogs-release-meta-stats">' . esc_html__( 'In', 'blocks-for-discogs-sellers' ) . ' ' . esc_html($wantlist) . ' ' . esc_html__( 'Wantlists', 'blocks-for-discogs-sellers' ) . ' • ' . esc_html__( 'In', 'blocks-for-discogs-sellers' ) . ' ' . esc_html($collection) . ' ' . esc_html__( 'Collections', 'blocks-for-discogs-sellers' ) . '</span>';
					}

					if ( ! empty( $attributes['display_item_allow_offers'] ) && ! empty( $data['allow_offers'] ) && $data['status'] != 'Sold' ) {
						echo '<span class="discogs-release-meta-offers">' . esc_html__( 'Accepts Offers', 'blocks-for-discogs-sellers' ) . '</span>';
					}
					?>
				</div>

				<div class="discogs-buttons">
				<?php
				if ( ! empty( $attributes['display_purchase_button'] ) && $data['status'] != 'Sold' ) {
					$bg_color = esc_html( $attributes['button_background_color'] ?? '#111111' );
					$text_color = esc_html( gf_blocks_for_discogs__get_contrast_color( $bg_color ) );
					$uri = esc_url( $data['uri'] ?? '#' );

					echo '
						<a href="' . esc_url($uri) . '" target="_blank" style="background:' . esc_attr($bg_color) . ';color:' . esc_attr($text_color) . ';">
						'.esc_html__( 'Purchase on Discogs ↗', 'blocks-for-discogs-sellers' ).'
						</a>';
				}

				if ( ! empty( $attributes['display_release_button'] ) ) {
					$bg_color = esc_html( $attributes['button_background_color'] ?? '#111111' );
					$text_color = esc_html( gf_blocks_for_discogs__get_contrast_color( $bg_color ) );
					$uri = esc_url( 'https://discogs.com/release/'.$data['release']['id'] );

					echo '
						<a href="' . esc_url($uri) . '" target="_blank" style="background:' . esc_attr($bg_color) . ';color:' . esc_attr($text_color) . ';">
						'.esc_html__( 'View Release on Discogs ↗', 'blocks-for-discogs-sellers' ).'
						</a>';
				}
				?>
				</div>

			</div>

		</div>

	</section>

</div>