<?php
// $Id: node.tpl.php,v 3.0.6.11 2008/11/10 19:00:00 hass Exp $
//krumo($node);

$count_pictures = meyerer_count_attachments($node, 'picture');
$count_privat_pictures = meyerer_count_attachments($node, 'privat_picture');
$count_flvvideo = meyerer_count_attachments($node, 'flvvideo');
$count_privat_flvvideo = meyerer_count_attachments($node, 'privat_video');

$res_pictures = $count_pictures + $count_privat_pictures;
$res_videos   = $count_flvvideo + $count_privat_flvvideo;

?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print " sticky"; } ?><?php if (!$status) { print " node-unpublished"; } ?>">
  <div class="clearfix">
    <?php if ($page == 0): ?>
      <h3><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h3>
    <?php endif; ?>
    
    <?php if ($submitted): ?>
      <div class="meta">
      <?php if ($submitted): ?>
        <span class="submitted">
          <?php 
            print $submitted.' '.t("o'clock");
            if($count_pictures > 0 || $count_flvvideo > 0)
            {
                print '<span class="attachment_info">'.t('Attachments').': ';
                if($res_pictures > 0)
                {
                    print format_plural($res_pictures, '1 picture', '@count pictures');
                    if($count_privat_pictures > 0)
                    {
                    	print ' ('.$count_privat_pictures.' Privat)';
                    }
                }
                if($res_pictures > 0 && $res_videos > 0)
                {
                    print ' - ';
                }
                if($res_videos > 0)
                {
                    print format_plural($res_videos, '1 video', '@count videos');
                    if($count_privat_flvvideo > 0)
                    {
                    	print ' ('.$count_privat_flvvideo.' Privat)';
                    }                    
                }
                print '</span>';
            }
          ?>
        </span>
      <?php endif; ?>
      </div>
    <?php endif; ?>     
    
    <?php print $picture ?>

    <div class="content">
        <?php print meyerer_content_view_check($node, $content); ?>
    </div>   
    
  </div>
  <?php if ($links) { print $links; } ?>
  
  <?php 
  if (!$teaser) 
  { 
      if($terms) 
      {
          print '<div class="terms">Tags: '.$terms.'</div>';
      }
  }
  ?>  
  
</div>
