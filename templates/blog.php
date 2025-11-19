<?php
$mycenterblogpost = $this->mcd_settings['mycenterblogpost'];

$blog_post_url = mcd_single_page_url('mycenterblogpost');
$prev_url = '';
$next_url = '';

if( isset($mycenterblogpost['prev']) ) {
  $prev_url = $blog_post_url.$mycenterblogpost['prev']['slug'];
}
if( isset($mycenterblogpost['next']) ) {
  $next_url = $blog_post_url.$mycenterblogpost['next']['slug'];
}
?>

<?php if( is_array($mycenterblogpost) ) : ?>

<div id="eyeonnews-single" class="mycenterdeals-wrapper">
  <?php if( isset( $mycenterblogpost['error'] ) ) : ?>
    <div class="mcd-alert"><?= $mycenterblogpost['error'] ?></div>
  <?php else: ?>
    <div class="eyeon-newspost clearfix">
      <div class="mcd-prev-next-nav">
        <?php if( !empty($this->mcd_settings['blog_listing_page']) ) : ?>
          <a href="<?= get_permalink($this->mcd_settings['blog_listing_page']) ?>" class="item back">Back to Blog</a>
        <?php endif; ?>
        <a <?= (!empty($prev_url)?'href="'.$prev_url.'"':'') ?> class="item prev hide <?= (empty($prev_url)?'disabled':'') ?>"><i class="fas fa-chevron-left"></i><span>Prev</span></a>
        <a <?= (!empty($next_url)?'href="'.$next_url.'"':'') ?> class="item next hide <?= (empty($next_url)?'disabled':'') ?>"><span>Next</span><i class="fas fa-chevron-right"></i></a>
      </div>

      <div class="mcd-post-cols">
        <div class="mcd-post-details-col">
          <div class="mcd-post-title"><?= $mycenterblogpost['title'] ?></div>

          <div class="mcd-post-metadata">
            <div class="mcd-posted-on"><?= eyeon_format_date($mycenterblogpost['post_date']) ?></div>
            
            <?php if( $this->mcd_settings['blog_single_show_author'] ) : ?>
              <div class="mcd-posted-by">By <?= $mycenterblogpost['author_name'] ?></div>
            <?php endif; ?>

            <?php if( $this->mcd_settings['blog_single_show_categories'] ) : ?>
              <?php if( count($mycenterblogpost['categories']) > 0 ) : ?>
                <div class="mcd-categories">
                  <span><?= implode(', ', array_map(function($category) { return $category['name']; }, $mycenterblogpost['categories'])) ?></span>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <!--
          <div class="mcd-post-image mcd_shadow_img">
            <img src="<?= $mycenterblogpost['media']['url'] ?>" />
          </div>
          -->

          <div class="mcd-post-content editor_output"><?= get_editor_output($mycenterblogpost['post_content']) ?></div>
        </div>

        <div class="mcd-post-sidebar-col">
          <?php if( $this->mcd_settings['blog_single_social_share'] ) : ?>
          <div class="mcd-post-share clearfix">
            <h4 class="mcd-heading">Share</h4>
            <ul class="mcd-social-icons">
              <li class="twitter"><a href="http://twitter.com/share?text=<?= urlencode($mycenterblogpost['title']) ?>&url=<?= get_current_url() ?>" target="_blank">Twitter</a></li>
              <li class="facebook"><a href="http://www.facebook.com/sharer.php?u=<?= get_current_url() ?>&quote=<?= urlencode($mycenterblogpost['title']) ?>" target="_blank">Facebook</a></li>
              <!-- <li class="pinterest"><a href="http://pinterest.com/pin/create/button/?url=<?= get_current_url() ?>&media=<?= $mycenterblogpost['media']['url'] ?>&description=<?= $mycenterblogpost['title'] ?>" target="_blank">Pinterest</a></li> -->
              <li class="email"><a href="mailto:?subject=<?= $mycenterblogpost['title'] ?>&body=Hi,%0D%0A%0D%0ACheck this out! - <?= urlencode(get_current_url()) ?>%0D%0A%0D%0A<?= $mycenterblogpost['title'] ?>%0D%0A%0D%0A<?= strip_tags($mycenterblogpost['post_content']) ?>%0D%0A%0D%0A">Email</a></li>
            </ul>
          </div>
          <?php endif; ?>
          
          <div class="mcd-latest-posts">
            <h4 class="mcd-heading">Latest Posts</h4>
            <div class="mcd-posts mcd_loading_div small"></div>
          </div>
        </div>
      </div>
    </div>

  <?php endif; ?>	
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
  var postsDiv = $('.mcd-latest-posts .mcd-posts');

  $.ajax({
    url: EYEON.ajaxurl+'?api=<?= MCD_API_NEWS ?>',
    data: {
      action: 'eyeon_api_request',
      apiUrl: "<?= MCD_API_NEWS ?>",
      params: {
        limit: 6,
        page: 1,
        sortColumn: 'post_date',
        sortOrder: 'desc'
      },
    },
    method: "POST",
    dataType: 'json',
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if( response.items ) {
        $.each(response.items, function(index, blog) {
          postsDiv.removeClass('mcd_loading_div small');

          if( blog.id != '<?= $mycenterblogpost['id'] ?>' ) {
            postsDiv.append(`
              <a class="mcd-post" href="<?= mcd_single_page_url('mycenterblogpost') ?>`+blog.slug+`">
                <span class="mcd-image mcd_shadow_img">
                  <img src="`+blog.media.url+`" alt="`+blog.title+`">
                </span>
                <span class="mcd-title-date">
                  <span class="mcd-title">`+blog.title+`</span>
                  <span class="mcd-date">`+eyeonFormatDate(blog.post_date)+`</span>
                </span>
              </a>
            `);
          }
        });
      }
    }
  });
});
</script>

<?php endif; ?>

