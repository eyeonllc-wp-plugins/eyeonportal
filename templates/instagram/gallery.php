<?php
$instagram_account_url = 'https://instagram.com/'.$this->mcd_settings['instagram_account_info']['username'];
?>

<div class="mycenterdeals-wrapper mcd_instagram_wrapper">
	<div class="mcd_instagram_gallery <?= $this->mcd_settings['instagram_gallery_shortcode_atts']['slider'] ?> owl-carousel">
		<?php foreach ($this->mcd_settings['instagram_posts'] as $key => $post) :
			$post_link = $post['permalink'];
			$post_date = date('M j', strtotime($post['timestamp']));
			$post_image = $post['media_url'];
			if( $post['media_type'] == 'VIDEO' ) {
				$post_image = $post['thumbnail_url'];
			}
			?>
			<div class="mcd_item">
				<span></span>
				<div class="mcd_item_content">
					<?php if( $post['media_type'] == 'CAROUSEL_ALBUM' ) : ?>
						<div class="mcd_post_icon carousel"></div>
					<?php elseif( $post['media_type'] == 'VIDEO' ) : ?>
						<div class="mcd_post_icon video"></div>
					<?php endif; ?>
					<div class="mcd_ig_image" style="background-image: url('<?= $post_image ?>');"></div>
					<div class="mcd_ig_overlay">
						<div class="mcd_ig_caption"><?= @nl2br($post['caption']) ?></div>
						<div class="mcd_ig_account">
							<div class="mcd_ig_name"><a href="<?= $instagram_account_url ?>" target="_blank"><?= $this->mcd_settings['instagram_account_info']['name'] ?></a></div>
							<div class="mcd_ig_username_date"><a href="<?= $instagram_account_url ?>" target="_blank"><?= $this->mcd_settings['instagram_account_info']['username'] ?></a> &#8226; <a href="<?= $post_link ?>" target="_blank"><?= $post_date ?></a></div>
						</div>
						<div class="mcd_ig_stats">
							<div class="mcd_ig_likes"><a href="<?= $post_link ?>" target="_blank"><i class="far fa-heart"></i> <?= mcd_likes_number_format($post['like_count']) ?></a></div>
							<div class="mcd_ig_comments"><a href="<?= $post_link ?>" target="_blank"><i class="far fa-comment"></i> <?= mcd_likes_number_format($post['comments_count']) ?></a></div>
						</div>
					</div>
				</div>
		
				<div class="mcd-popup mcd_ig_popup">
					<div class="mcd-popup-wrapper">
						<div class="mcd-popup-overlay"></div>
						<div class="mcd-popup-content">
							<div class="mcd_popup_header">
								<!-- <div class="mcd-popup-close"></div> -->
								<div class="mcd_ig_account_profile_picture" style="background-image: url('<?= $this->mcd_settings['instagram_account_info']['profile_picture_url'] ?>');"></div>
								<div class="mcd_ig_account_username">
									<a href="<?= $instagram_account_url ?>" target="_blank"><?= $this->mcd_settings['instagram_account_info']['username'] ?></a>
								</div>
								<a class="mcd_ig_view_link" href="<?= $post_link ?>" target="_blank">View on Instagram</a>
							</div>
							<div class="mcd_popup_maincontent">
								<img class="mcd_ig_post_image" src="<?= $post_image ?>" />
								<?php if( !empty($post['caption']) ) : ?>
									<div class="mcd_ig_post_caption"><?= @nl2br($post['caption']) ?></div>
								<?php endif; ?>
							</div>
							<div class="mcd_popup_footer">
								<div class="mcd_ig_stats">
									<div class="mcd_ig_likes"><a href="<?= $post_link ?>" target="_blank"><i class="far fa-heart"></i> <?= mcd_likes_number_format($post['like_count']) ?></a></div>
									<div class="mcd_ig_comments"><a href="<?= $post_link ?>" target="_blank"><i class="far fa-comment"></i> <?= mcd_likes_number_format($post['comments_count']) ?></a></div>
								</div>
								<div class="mcd_ig_post_date"><a href="<?= $post_link ?>" target="_blank"><?= $post_date ?></a></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.mcd_item .mcd_item_content').on('click', function(event) {
		if( event.target.tagName.toLowerCase() != 'a' ) {
			jQuery('html').addClass('noscrollbar');
			jQuery(this).parents('.mcd_item').find('.mcd-popup').clone().appendTo('body').fadeIn();
		}
	});
	jQuery(document).on('click', '.mcd-popup .mcd-popup-overlay, .mcd-popup .mcd-popup-close', function(event) {
		jQuery('html').removeClass('noscrollbar');
		jQuery(this).parents('.mcd-popup').fadeOut(function() {
			jQuery(this).remove();
		});
	});

	<?php if( $this->mcd_settings['instagram_gallery_shortcode_atts']['slider'] == 'yes' ) : ?>
	var owl_options = {
		nav: true,
		navText: ['',''],
		dots: false,
		dotsEach: true,
		loop: false,
		items: 5,
		autoplay: true,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		slideBy: 1,
		touchDrag: true,
		mouseDrag: true,
		center: false,
		margin: 20,
		itemElement: 'mcd_item',
		responsive: {
			0: {
				items: 2,
			},
			600: {
				items: 3,
			},
			1025: {
				items: 5,
			},
		}
	};
	jQuery('.mcd_instagram_gallery').owlCarousel(owl_options);
	<?php endif; ?>
});
</script>