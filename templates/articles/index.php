<?php
$mcd_center_id = isset( $this->mcd_settings['center_id'] ) ? $this->mcd_settings['center_id'] : 0;
?>

<div ng-app="MyCenterPortalApp" ng-controller="ShareRailsArticlesCtrl" data-url="<?= admin_url('admin-ajax.php') ?>">
	<div class="mycenterdeals-wrapper mcp_sharerails_shopping_guide">
		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>

		<div id="mycenterdeals-wrapper" ng-cloak>
			<div id="shopping_guide_articles">
				<div class="mcp-article-item" ng-repeat="article in articles">
					<a class="mcp-article-image mcd_shadow_img" href="<?= mcd_single_page_url('mycenterarticle') ?>{{ article.slug }}">
						<img ng-src="{{article.image}}" />
					</a>
					<div class="mcp-article-content">
						<a class="article-title" href="<?= mcd_single_page_url('mycenterarticle') ?>{{ article.slug }}">{{ article.title }}</a>
						<div class="article-desc">{{ article.desc }}</div>
						<a class="article-readmore mcp_btn" href="<?= mcd_single_page_url('mycenterarticle') ?>{{ article.slug }}">Read more</a>
					</div>
				</a>
			</div>
		</div>

		<div id="mcd-load-more-div" ng-class="{loading: busy}"></div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	ga_event_tracking('Pages', 'Shopping Guide');
});
</script>

