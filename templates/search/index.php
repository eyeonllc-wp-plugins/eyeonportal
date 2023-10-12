<div class="mycenterdeals-wrapper mycentersearch">
	<?php include MCD_PLUGIN_PATH.'templates/search/form.php'; ?>

	<div class="mcd_search_heading">Search results for “<span><?= stripslashes(urldecode(get_query_var('mycentersearch'))) ?></span>”</div>

	<div id="mcd_search_tabs">
		<ul class="mcd_search_tab_links">
			<?php foreach ($this->search['types'] as $key => $value) :
				$results_count = 0;
				if( substr($key, 0, 7) == 'portal_' )  {
					$type = substr($key, 7);
					$results_count = count($this->search['mcd_results'][$type]);
				} elseif ( substr($key, 0, 3) == 'wp_' ) {
					$type = substr($key, 3);
					$results_count = count($this->search['post_results'][$type]);
				}
				?>
				<li><a href="#mcd_results_<?= $key ?>"><?= $this->search['titles'][$key] ?> <span class="count">(<?= $results_count ?>)</span></a></li>	
			<?php endforeach; ?>
		</ul>

		<div class="mcd_search_results">
			<?php foreach ($this->search['types'] as $key => $value) : ?>
			<!-- <?= $key ?> starts -->
			<div id="mcd_results_<?= $key ?>" class="mcd_search_result">
				<?php if( substr($key, 0, 7) == 'portal_' ) : $type = substr($key, 7); ?>
					<?php if( is_array($this->search['mcd_results'][$type]) ) : ?>
						<?php if( count($this->search['mcd_results'][$type]) > 0 ) : ?>
							<div class="mcd_search_items mcd_<?= $type ?>">
								<?php foreach( $this->search['mcd_results'][$type] as $single ) : ?>
									<?php include MCD_PLUGIN_PATH.'templates/search/item/'.$type.'-single.php'; ?>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<div class="mcd-alert">No results found.</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php if( substr($key, 0, 3) == 'wp_' ) : $type = substr($key, 3); ?>
					<?php if ( count($this->search['post_results'][$type]) > 0 ) : ?>
						<div class="mcd_search_items mcd_<?= $type ?>">
							<?php foreach( $this->search['post_results'][$type] as $post ) : ?>
								<?php include MCD_PLUGIN_PATH.'templates/search/item/wp-post-single.php'; ?>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<div class="mcd-alert">No results found.</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<!-- <?= $key ?> ends -->
			<?php endforeach; ?>
		</div>
	</div>
</div>

