<?php
// $Id: block.tpl.php,v 3.0.6.11 2008/11/10 19:00:00 hass Exp $
?>
<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="clearfix block block-<?php print $block_zebra; ?> block-<?php print $block->module ?>">
  <?php if ($block->subject): ?><h3><?php print $block->subject ?></h3><?php endif;?>
  <div class="content"><?php print $block->content ?></div>
</div>
