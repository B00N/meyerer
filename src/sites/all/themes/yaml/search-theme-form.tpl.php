<?php
// $Id: search-theme-form.tpl.php,v 3.0.6.11 2008/11/10 19:00:00 hass Exp $
?>
<div id="search" class="container-inline">
  <?php
  $search['search_theme_form'] = preg_replace('/<label(.*)>(.*)<\/label>\n/i', '', $search['search_theme_form']);
  print $search['search_theme_form'];
  print $search['submit'];
  print $search['hidden'];
  ?>
</div>
