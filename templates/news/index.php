<?php
$mcd_center_id = isset( $this->mcd_settings['center_id'] ) ? $this->mcd_settings['center_id'] : 0;
$isotope_grid = $this->mcd_settings['blog_posts_shortcode_atts']['filters'] == 'yes';
?>

<div ng-app="MyCenterPortalApp" ng-controller="BlogPostsCtrl" data-url="<?= MCD_API_NEWS.'?limit=100&page=1' ?>" data-center-id="<?= $this->mcd_settings['center_id'] ?>">
	<div class="mycenterdeals-wrapper mycenterblog">
		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>

		<div id="mycenterdeals-wrapper" ng-hide="data.error" ng-class="{loading: busy}">
			<?php if( $isotope_grid ) : ?>
				<div id="mycenterblog_filters" ng-show="!busy" ng-cloak>
					<ul>
						<li class="filter active" data-filter="*">All</li>
						<li class="filter" ng-repeat="category in data.categories" data-filter=".{{category | categoryName}}">{{category}}</li>
					</ul>
				</div>
			<?php endif; ?>

			<div id="mycenterblog" class="<?= ($isotope_grid?'isotope':'') ?>">
				<div class="mcd-blog-post-item" ng-class="{{post.categories | categoryClassesFromArr}}" ng-repeat="post in blogposts" ng-cloak>
					<a class="" href="<?= mcd_single_page_url('mycenterblogpost') ?>{{ post.slug }}">
						<span class="mcd-post-featured-image mcd_shadow_img">
							<span class="mcd-post-date">
								<span class="mcd-day">{{ post.post_date_day }}</span>
								<span class="mcd-month">{{ post.post_date_month }}</span>
							</span>
							<img ng-src="{{ post.media.url }}" />
						</span>
						<span class="mcd-blog-post-details">
							<span class="mcd-blog-post-title">{{ post.title }}</span>
							<span class="mcd-sep"></span>
							<span class="mcd-learn-more">Learn More</span>
						</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	var BlogPostsCtrl = $('[ng-controller="BlogPostsCtrl"]');
	BlogPostsCtrl.scope().loadResults(function() {
		<?php if( $isotope_grid ) : ?>
			var $grid = null;
			
			setTimeout(function() {
				$grid = $('#mycenterblog.isotope').isotope({
					itemSelector: '.mcd-blog-post-item',
					layoutMode: 'fitRows',
					percentPosition: true,
					filter: '*'
				});

				$('#mycenterblog_filters').on('click', '.filter', function() {
					$('#mycenterblog_filters .filter.active').removeClass('active');
					$(this).addClass('active');

					var filterValue = $(this).attr('data-filter');
					$grid.isotope({ filter: filterValue });
				});
			}, 1);
		<?php endif; ?>
	});

});
</script>

