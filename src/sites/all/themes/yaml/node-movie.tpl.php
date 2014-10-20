<?php
// $Id: node.tpl.php,v 3.0.6.11 2008/11/10 19:00:00 hass Exp $
require_once './sites/all/php_classes/imdbphp/imdb.class.php';
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print " sticky"; } ?><?php if (!$status) { print " node-unpublished"; } ?>">
  <div class="clearfix">
    <?php if ($page == 0): ?>
      <h3><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h3>
    <?php endif; ?>
    <div class="content">
      
      <?php 
      $asin = $node->field_movie_item[0]['asin'];
      $amazon_info = amazon_item_lookup_from_db(array($asin));
      $mediumpic = $amazon_info[$asin]['imagesets']['mediumimage']['url'];
      $largepic = $amazon_info[$asin]['imagesets']['largeimage']['url'];      
      ?>
      <a href="<?php print $largepic; ?>" class="thickbox" style="float:left;padding-right: 20px;"><img src="<?php print $mediumpic; ?>" /></a>
      <?php
      // http://projects.izzysoft.de/trac/imdbphp/wiki/MovieDetails.de#DetailszumFilm
      $movie   = new imdb($node->field_movie_imdb[0]['view']);         // eine Instanz der `imdb` Klasse erstellen und dabei auch gleich die IMDB-ID Ÿbergeben
      $imdb_info = array(
          'title'   => $movie->title(),
          'alsoknow'=> $movie->alsoknow(),
          'composer'=> $movie->composer(),
          'director'=> $movie->director(),
          'genre'   => $movie->genre(),
          'genres'  => $movie->genres(),
          'year'    => $movie->year(),         // das Erscheinungsjahr abfragen
          'runtime' => $movie->runtime(),      // die Laufzeit ermitteln (FilmlŠnge)
          'rating'  => $movie->mpaa(),         // ein Array[country=>rating] verfŸgbarer "Ratings" abrufen (auch als "FSK" bekannt)
          'trailer' => $movie->trailers(),     // ein Array verfŸgbarer Trailer holen
          'cast'    => $movie->cast(),
          'colors'  => $movie->colors(),
          'writing' => $movie->writing(),       
      );
      print '<p><strong>Orginaltitel:</strong> ' . $imdb_info['title'] . ' | ' . $imdb_info['year'] . ' | ' . $imdb_info['runtime'] . ' min</p>';
      
      print '<p><strong>Genre:</strong> ';
      foreach($imdb_info['genres'] as $genres) 
      {
                $out .= $genres . ' | ';
      }
      print substr($out,0,-2);
      print '</p>';
      
      print '<p><strong>Regisseur:</strong> ';
      $out = '';
      if(count($imdb_info['director']) > 10)
      {
          for ($i = 0; $i <= 10; $i++) 
          {
              $out .= '<a href="http://www.imdb.de/name/nm' . $imdb_info['director'][$i]['imdb'] . '/">' . $imdb_info['director'][$i]['name'] . '</a>, ';
          }
          $out = substr($out,0,-2) . '...';
      }
      else
      {
          foreach($imdb_info['director'] as $director) 
          {
                $out .= '<a href="http://www.imdb.de/name/nm' . $director['imdb'] . '/">' . $director['name'] . '</a>, ';
          }
          $out = substr($out,0,-2);
      }
      print $out; 
      print '</p>';
      
      print '<p><strong>Drehbuchautoren:</strong> ';
      $out = '';
      if(count($imdb_info['writing']) > 10)
      {
          for ($i = 0; $i <= 10; $i++) 
          {
              $out .= '<a href="http://www.imdb.de/name/nm' . $imdb_info['writing'][$i]['imdb'] . '/">' . $imdb_info['writing'][$i]['name'] . '</a>, ';
          }
          $out = substr($out,0,-2) . '...';
      }
      else
      {
          foreach($imdb_info['writing'] as $writing) 
          {
                $out .= '<a href="http://www.imdb.de/name/nm' . $writing['imdb'] . '/">' . $writing['name'] . '</a>, ';
          }
          $out = substr($out,0,-2);
      }
      print $out; 
      print '</p>'; 
      ?>
      
      <p><strong>IMDb:</strong> <a href="http://www.imdb.com/title/tt<?php print $node->field_movie_imdb[0]['view']; ?>/">http://www.imdb.com/title/tt<?php print $node->field_movie_imdb[0]['view']; ?>/</a></p>           
      
      <?php
      print '<p><strong>Besetzung:</strong> ';
      $out = '';
      if(count($imdb_info['cast']) > 10)
      {
          for ($i = 0; $i <= 10; $i++) 
          {
              $out .= '<a href="http://www.imdb.de/name/nm' . $imdb_info['cast'][$i]['imdb'] . '/">' . $imdb_info['cast'][$i]['name'] . '</a> (' . $imdb_info['cast'][$i]['role'] . '), ';
          }
          $out = substr($out,0,-2) . '...';
      }
      else
      {
          foreach($imdb_info['cast'] as $cast) 
          {
                $out .= '<a href="http://www.imdb.de/name/nm' . $cast['imdb'] . '/">' . $cast['name'] . '</a> (' . $cast['role'] . '), ';
          }
          $out = substr($out,0,-2);
      }
      print $out; 
      print '</p>';
      ?>
      
    <?php print $content ?>  
      
    </div>
    
    <?php print $picture ?>
    <?php if ($terms || $submitted): ?>
      <div class="meta">
      <?php if ($submitted): ?>
        <span class="submitted"><?php print $submitted ?></span>
      <?php endif; ?>
      <?php if ($terms): ?>
        <div class="terms"><?php print $terms ?></div>
      <?php endif;?>
      </div>
    <?php endif; ?>    
    
  </div>
  <?php if ($links) { print $links; } ?>
</div>
