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

// SORT
if (isset($_REQUEST['sort'])) :
	$sort = sanitize_text_field(wp_unslash($_REQUEST['sort']));
elseif (isset($attributes['sorting_sort'])) :
	$sort = $attributes['sorting_sort'];
else :
	$sort = 'listed';
endif;

// SORT ORDER
if (isset($_REQUEST['sort_order']) && in_array( sanitize_text_field(wp_unslash($_REQUEST['sort_order'])), ['asc','desc']) ) :
	$sort_order = sanitize_text_field(wp_unslash($_REQUEST['sort_order']));
elseif (isset($attributes['sorting_sort_order'])) :
	$sort_order = $attributes['sorting_sort_order'];
else :
	$sort_order = 'asc';
endif;

// PER PAGE
if (isset($_REQUEST['per_page']) && (int)sanitize_text_field(wp_unslash($_REQUEST['per_page'])) <= 100) :
	$per_page = sanitize_text_field(wp_unslash($_REQUEST['per_page']));
elseif (isset($attributes['sorting_per_page'])) :
	$per_page = (int)$attributes['sorting_per_page'];
else :
	$per_page = 50;
endif;

// PAGE
if (isset($_GET['discogs_page'])) :
	$current_page = (int)sanitize_text_field(wp_unslash($_GET['discogs_page']));
else :
	$current_page = 1;
endif;

// CLEAN URL FOR PAGINATION
$currentURL = home_url( add_query_arg( null, null ) );
$paginationURL = remove_query_arg( 'discogs_page', $currentURL );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// GET DISCOGS DATA
$args = [
	'endpoint' => 'inventory',
	'id' => null,
	'page' => $current_page,
	'per_page' => $per_page,
	'sort' => $sort,
	'sort_order' => $sort_order,
	'username' => $username,
	'nocache' => $attributes['sorting_allow_user_sort'] ?? false,
];

$discogs = new BlocksForDiscogsSellers_API();
$data = $discogs->fetch($args);

if (!empty($data['error']) ) {
	echo wp_kses($discogs->render_error_html($data), 'post');
	return;
}

if (!empty($data['wants'])) :
	$releases = $data['wants'];
elseif (!empty($data['releases'])) :
	$releases = $data['releases'];
elseif (!empty($data['listings'])) :
	$releases = $data['listings'];
endif;

if (empty($releases)) :
	echo wp_kses($discogs->render_error_html([
		'error'=>true,
		'message'=>__('NO ITEMS IN THIS INVENTORY','blocks-for-discogs-sellers')
	]), 'post');
	return;
endif;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<div <?php echo wp_kses_post(!$is_editor ? $wrapper_attributes : ''); ?>>

	<section id="<?php echo esc_attr($blockID); ?>" class="discogs-seller-inventory">

		<?php if ( ! empty($attributes['display_section_title']) ) : ?>
		<h2><?php echo esc_html((!empty($data['name']) ? $data['name'] : $username).'\'s Inventory'); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty($attributes['sorting_allow_user_sort']) ) : ?>
		<form action="" method="get" class="discogs-sorting">

			<div>
				<label><?php esc_html_e('Sort','blocks-for-discogs-sellers'); ?></label>
				<select name="sort">
					<option <?php if ($sort == 'listed') : echo 'selected'; endif; ?> value="listed"><?php esc_html_e('Listed','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'price') : echo 'selected'; endif; ?> value="price"><?php esc_html_e('Price','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'item') : echo 'selected'; endif; ?> value="item"><?php esc_html_e('Item','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'artist') : echo 'selected'; endif; ?> value="artist"><?php esc_html_e('Artist','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'label') : echo 'selected'; endif; ?> value="label"><?php esc_html_e('Label','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'catno') : echo 'selected'; endif; ?> value="catno"><?php esc_html_e('Catalog Number','blocks-for-discogs-sellers'); ?></option>
					<option <?php if ($sort == 'rating') : echo 'selected'; endif; ?> value="rating"><?php esc_html_e('Rating','blocks-for-discogs-sellers'); ?></option>
				</select>
			</div>

			<div>
				<label><?php esc_html_e('Sort Order','blocks-for-discogs-sellers'); ?></label>
				<select name="sort_order">
					<option value="asc" <?php if ($sort_order == 'asc') : echo 'selected'; endif; ?>><?php esc_html_e('Ascending (A to Z)','blocks-for-discogs-sellers'); ?></option>
					<option value="desc" <?php if ($sort_order == 'desc') : echo 'selected'; endif; ?>><?php esc_html_e('Descending (Z to A)','blocks-for-discogs-sellers'); ?></option>
				</select>
			</div>

			<div>
				<label><?php esc_html_e('Per Page','blocks-for-discogs-sellers'); ?></label>
				<select name="per_page">
					<option value="10" <?php if ($per_page == 10) : echo 'selected'; endif; ?>>10</option>
					<option value="20" <?php if ($per_page == 20) : echo 'selected'; endif; ?>>20</option>
					<option value="50" <?php if ($per_page == 50) : echo 'selected'; endif; ?>>50</option>
					<option value="100" <?php if ($per_page == 100) : echo 'selected'; endif; ?>>100</option>
				</select>
			</div>

			<button class="wp-element-button"><?php esc_html_e('Filter','blocks-for-discogs-sellers'); ?></button>

		</form>
		<?php endif; ?>


		<?php if (!empty($releases)) : ?>
		<ul class="discogs-layout-<?php echo esc_attr($attributes['layout_style'] ?? 'cards'); ?> discogs-columns-<?php echo esc_attr($attributes['layout_columns'] ?? 3); ?>">

			<?php foreach ($releases as $release) : ?>
			<li id="discogs-<?php echo esc_attr($release['id']); ?>">

				<?php if ($attributes['display_release_image'] == true) : ?>
				<div class="discogs-release-image">
					<a title="View Listing" href="<?php echo esc_url( isset($attributes['destination_url']) && $attributes['destination_url'] == 'custom' && !empty($attributes['destination_custompage']) ? $attributes['destination_custompage'].'?listing_id='.$release['id'] : $release['uri']); ?>" >
						<img loading="lazy" alt="<?php echo esc_attr($release['release']['title'].' Cover'); ?>" src="<?php echo esc_url(!empty($release['release']['images'][0]['uri']) ? $release['release']['images'][0]['uri'] : GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_URL.'/assets/img/PLACEHOLDER.jpg'); ?>">
					</a>
				</div>
				<?php endif; ?>


				<div class="discogs-release-information">

					<div class="discogs-release-titles">

						<?php
						if ($attributes['title_display'] == 'artist_title') :

							if ( ! empty($attributes['display_release_title']) ) :
								echo '<h5>'.esc_html($release['release']['title']).'</h5>';
							endif;

							if ( ! empty($attributes['display_release_artist']) ) :
								echo '<h6>'.esc_html($release['release']['artist']).'</h6>';
							endif;

						else :

							if ( ! empty($attributes['display_release_title']) ) :
								echo '<h5>'.esc_html($release['release']['description']).'</h5>';
							endif;

						endif;
						
						if ( ! empty($attributes['display_item_price']) ) :
							echo '<p>'.esc_html($release['original_price']['formatted'].' '.$release['price']['currency']).'</p>';
						endif;

						if ( ! empty($attributes['display_item_comments']) ) :
							echo '<small><em>'.esc_html($release['comments']).'</em></small>';
						endif;

						?>

					</div>

					<div class="discogs-release-meta">
						<?php
						if ( ! empty( $attributes['display_release_format'] ) ) {
							echo '<span class="discogs-release-meta-format">' . esc_html__( 'Format:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $release['release']['format'] ?? '' ) . '</span>';
						}

						if ( ! empty( $attributes['display_release_year'] ) ) {
							echo '<span class="discogs-release-meta-year">' . esc_html__( 'Year:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( (int)$release['release']['year'] > 0 ? (int)$release['release']['year'] : 'N/A' ) . '</span>';
						}

						if ( ! empty( $attributes['display_release_catalog_number'] ) ) {
							echo '<span class="discogs-release-meta-catalog">' . esc_html__( 'Catalog:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $release['release']['catalog_number'] ?? 'N/A' ) . '</span>';
						}

						if ( ! empty( $attributes['display_item_grading'] ) ) {
							echo '<span class="discogs-release-meta-grading">' . esc_html__( 'Media:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $release['condition'] ?? '' ) . '<br>' . esc_html__( 'Sleeve:', 'blocks-for-discogs-sellers' ) . ' ' . esc_html( $release['sleeve_condition'] ?? '' ) . '</span>';
						}

						if ( ! empty( $attributes['display_release_stats'] ) ) {
							$wantlist   = $release['release']['stats']['community']['in_wantlist'] ?? '';
							$collection = $release['release']['stats']['community']['in_collection'] ?? '';

							echo '<span class="discogs-release-meta-stats">' . esc_html__( 'In', 'blocks-for-discogs-sellers' ) . ' ' . esc_html($wantlist) . ' ' . esc_html__( 'Wantlists', 'blocks-for-discogs-sellers' ) . ' • ' . esc_html__( 'In', 'blocks-for-discogs-sellers' ) . ' ' . esc_html($collection) . ' ' . esc_html__( 'Collections', 'blocks-for-discogs-sellers' ) . '</span>';
						}

						if ( ! empty( $attributes['display_item_allow_offers'] ) && ! empty( $release['allow_offers'] ) ) {
							echo '<span class="discogs-release-meta-offers">' . esc_html__( 'Accepts Offers', 'blocks-for-discogs-sellers' ) . '</span>';
						}
						
						
						if ( $attributes['layout_style'] == 'list' && !empty( $attributes['display_purchase_button'] ) ) {
							$bg_color = esc_html( $attributes['purchase_button_background_color'] ?? '#111111' );
							$text_color = esc_html( gf_blocks_for_discogs__get_contrast_color( $bg_color ) );
							$uri = esc_url( $release['uri'] ?? '#' );
							echo '
							<div class="discogs-purchase-button">
								<a href="' . esc_url($uri) . '" target="_blank" style="display:inline-block;background:' . esc_attr($bg_color) . ';color:' . esc_attr($text_color) . ';">
								'.esc_html__( 'Purchase on Discogs ↗', 'blocks-for-discogs-sellers' ).'
								</a>
							</div>';						
						}
						
						?>
					</div>

				
				</div>

				<?php
				if ( $attributes['layout_style'] == 'cards' && !empty( $attributes['display_purchase_button'] ) ) {
					$bg_color = esc_html( $attributes['purchase_button_background_color'] ?? '#111111' );
					$text_color = esc_html( gf_blocks_for_discogs__get_contrast_color( $bg_color ) );
					$uri = esc_url( $release['uri'] ?? '#' );

					echo '
					<div class="discogs-purchase-button">
						<a href="' . esc_url($uri) . '" target="_blank" style="background:' . esc_attr($bg_color) . ';color:' . esc_attr($text_color) . ';">
						'.esc_html__( 'Purchase on Discogs ↗', 'blocks-for-discogs-sellers' ).'
						</a>
					</div>';
				}
				?>

			</li>
			<?php endforeach; ?>

		</ul>
		<?php endif; ?>

		<?php 
		if (!empty($data['pagination']) && !empty($attributes['display_pagination'] && (int)$data['pagination']['pages'] > 1)) : 

			$current       = isset($_GET['discogs_page']) ? max(1, (int)$_GET['discogs_page']) : 1;
			$total         = (int)$data['pagination']['pages'];
			$paginationURL = remove_query_arg('discogs_page');

			echo '<nav class="discogs-pagination">';

			// Prev button
			if ($current > 1) {
				$pageURL = add_query_arg('discogs_page', $current - 1, $paginationURL);
				echo '<a class="prev-btn" href="'.esc_url($pageURL).'">Prev</a> ';
			}

			// Dropdown
			echo '<form method="get" style="display:inline;">';

				// Keep other query parameters intact
				if (isset($_GET['sort'])) :
    				$discogsSort = sanitize_text_field( wp_unslash( $_GET['sort'] ) );
					echo '<input type="hidden" name="sort" value="'.esc_attr($discogsSort).'">';
				endif;

				if (isset($_GET['sort_order'])) :
    				$discogsSortOrder = sanitize_text_field( wp_unslash( $_GET['sort_order'] ) );
					echo '<input type="hidden" name="sort_order" value="'.esc_attr($discogsSortOrder).'">';
				endif;

				if (isset($_GET['per_page'])) :
    				$discogsPerPage = sanitize_text_field( wp_unslash( $_GET['per_page'] ) );
					echo '<input type="hidden" name="per_page" value="'.esc_attr($discogsPerPage).'">';
				endif;

				// Output pages dropdown
				echo '<select name="discogs_page" onchange="this.form.submit()">';

				for ($p = 1; $p <= $total; $p++) {
					$selected = ($p == $current ? 'selected' : '');
					echo '<option value="'.esc_attr($p).'" '.esc_attr($selected).'>Page '.esc_attr($p).'</option>';
				}

				echo '</select>';

			echo '</form>';

			// Next button
			if ($current < $total) {
				$pageURL = add_query_arg('discogs_page', $current + 1, $paginationURL);
				echo ' <a class="next-btn" href="'.esc_url($pageURL).'">Next</a>';
			}

			echo '</nav>';

		endif;
		?>


	</section>

</div>