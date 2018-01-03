<style type="text/css" media="all">
  .Toplevelbox {
	top: -4px;
    position: relative;
    margin-bottom: 10px;
    width: 180px;
    height: 200px;
  }
  .top_level {
	position: absolute;
	top: 29px;
    left: 6px;
    height: 160px;
    width: 168px;
    z-index: 20;
    text-align: center;
    padding-top: 6px;
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 9.2pt;
    color: #FFF;
    font-weight: bold;
    text-align: right;
    text-decoration: inherit;
    text-shadow: 0.1em 0.1em #333
  }

  #Topbar a {
  text-decoration: none;
  }
  .online {
	  color: #008000;
  }
  
  .offline {
	  color: #FF0000;
  }
  a.topfont {
	font-family: Verdana, Arial, Helvetica; 
    font-size: 11px; 
    text-decoration: none
  }
  a:hover.topfont {
	font-family: Verdana, Arial, Helvetica; 
    font-size: 11px; 
    color: #CCC; 
    text-decoration:none
  }
</style>

<div id="Topbar" class="Themebox" style="background-image:url(<?PHP echo $template_path; ?>/images/themeboxes/highscores/highscores.png);">
  <div class="top_level" style="background:url(<?PHP echo $template_path; ?>/images/themeboxes/bg_top.png)" align="	">
    <?php
    
    foreach(getTopPlayers(5) as $player) {
	    echo '<div align="left"><a href="'.getPlayerLink($player['name'], false).'" class="topfont ' . ($player['online'] == 1 ? 'online' : 'offline') . '">
        <font color="#CCC">&nbsp;&nbsp;&nbsp;&nbsp;'.$player['rank'].' - </font>'.$player['name'].'
        <br>
        <small><font color="white">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Level: ('.$player['level'].')</font></small>
        <br>
      </a>
      </div>';
    }
    ?>
<div class="Bottom" style="background-image:url(<?PHP echo $template_path; ?>/images/general/box-bottom.gif); top: 159px;; left:-5px;">
</div>
</div>
</div>
<br/><br/><br/>