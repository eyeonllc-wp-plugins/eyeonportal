<script type="text/javascript">
var mycenterarticle = <?= json_encode($this->mcd_settings['mycenterarticle']['data']['sections']) ?>;
</script>

<?php
$mycenterarticle = $this->mcd_settings['mycenterarticle'];

$title = $mycenterarticle['title'];

$banner_image = mcd_image_url();
if( isset($mycenterarticle['previewImageWide']['imageUrl']) ) {
	$banner_image = $mycenterarticle['previewImageWide']['imageUrl'];
}

$sections = array();
if( isset($mycenterarticle['data']['sections']) ) {
	$sections = $mycenterarticle['data']['sections'];
}
?>

<div class="mycenterdeals-wrapper mycenterarticle">
	<div class="article-sections">
		<div class="banner-article-section" style="background-image: url('<?= $banner_image ?>');">
			<div class="article-guide-title">
				<h1><?= $title ?></h1>
			</div>
		</div>

		<?php foreach($sections as $section) : ?>
			<div class="article-section">
				<?php if( $section['type'] == 0 ) : ?>
					<div class="header-section">
						<h3><?= $section['title'] ?></h3>
						<div class="desc"><?= $section['bodyHtml'] ?></div>
					</div>
				<?php elseif( $section['type'] == 1 ) : ?>
					<?php if( isset($section['products']) && count($section['products']) > 0 ) : ?>
						<div class="article-products">
							<?php foreach( $section['products'] as $product ) : ?>
								<?php
								if( $product['id'] > 0 ) :
									$product_image = mcd_image_url();
									if( isset($product['images']) && isset($product['images']['items']) && count($product['images']['items']) > 0 ) {
										$product_image = $product['images']['items'][0]['imageUrl'];
									}
									?>
									<a class="article-product" href="<?= mcd_single_page_url('mycenterproduct').getFriendlyURL($product['id'].'-'.$product['title']) ?>">
										<div class="article-product-image">
											<img src="<?= $product_image ?>" alt="<?= $product['title'] ?>" />
										</div>
										<div class="article-text">
											<div class="article-product-title end_dots"><?= $product['title'] ?></div>
											<div class="article-product-retailer"><?= $product['retailer']['name'] ?></div>
										</div>
									</a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>

