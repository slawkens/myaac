<?php
if(isset($config['boxes']))
	$config['boxes'] = explode(",", $config['boxes']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo template_place_holder('head_start'); ?>
	<link rel="shortcut icon" href="<?php echo $template_path; ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="<?php echo $template_path; ?>/images/favicon.ico" type="image/x-icon" />
	<link href="<?php echo $template_path; ?>/basic.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="tools/basic.js"></script>
	<script type="text/javascript" src="<?php echo $template_path; ?>/ticker.js"></script>
	<script type="text/javascript">
		<?php echo 'var loginStatus="' . ($logged ? 'true' : 'false') . '";
		var activeSubmenuItem="' . PAGE . '";
		var IMAGES="' . $template_path . '/images";
		var LINK_ACCOUNT="' . BASE_URL . '";';
		?>

		function rowOverEffect(object) {
			if (object.className == 'moduleRow') object.className = 'moduleRowOver';
		}

		function rowOutEffect(object) {
			if (object.className == 'moduleRowOver') object.className = 'moduleRow';
		}

		<!-- // Framekiller
		setTimeout ("changePage()", 6000);
		function changePage()
		{
		  if (parent.frames.length > 2) {
			if (browserTyp == "ie")
				parent.location=document.location;
			else
				self.top.location=document.location;
		  }
		}
		// -->

		function InitializePage() {
		  LoadLoginBox();
		  LoadMenu();
		}

		// initialisation of the loginbox status by the value of the variable 'loginStatus' which is provided to the HTML-document by PHP in the file 'header.inc'
		function LoadLoginBox()
		{
		  if(loginStatus == "false") {
			document.getElementById('LoginstatusText_1').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-you-are-not-logged-in.gif')";
			document.getElementById('ButtonText').style.backgroundImage = "url('" + IMAGES + "/buttons/_sbutton_login.gif')";
			document.getElementById('LoginstatusText_2').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-create-account.gif')";
			document.getElementById('LoginstatusText_2_1').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-create-account.gif')";
			document.getElementById('LoginstatusText_2_2').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-create-account-over.gif')";
		  } else {
			document.getElementById('LoginstatusText_1').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-welcome.gif')";
			document.getElementById('ButtonText').style.backgroundImage = "url('" + IMAGES + "/buttons/_sbutton_myaccount.gif')";
			document.getElementById('LoginstatusText_2').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-logout.gif')";
			document.getElementById('LoginstatusText_2_1').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-logout.gif')";
			document.getElementById('LoginstatusText_2_2').style.backgroundImage = "url('" + IMAGES + "/loginbox/loginbox-font-logout-over.gif')";
		  }
		}

		// mouse-over and click events of the loginbox
		function MouseOverLoginBoxText(source)
		{
		  source.lastChild.style.visibility = "visible";
		  source.firstChild.style.visibility = "hidden";
		}
		function MouseOutLoginBoxText(source)
		{
		  source.firstChild.style.visibility = "visible";
		  source.lastChild.style.visibility = "hidden";
		}
		function LoginButtonAction()
		{
		  if(loginStatus == "false") {
			window.location = "<?php echo $template['link_account_manage']; ?>";
		  } else {
			window.location = "<?php echo $template['link_account_manage']; ?>";
		  }
		}
		function LoginstatusTextAction(source) {
		  if(loginStatus == "false") {
			window.location = "<?php echo $template['link_account_create']; ?>";
		  } else {
			window.location = "<?php echo $template['link_account_logout']; ?>";
		  }
		}

		var menu = new Array();
		menu[0] = new Object();
		var unloadhelper = false;

		// load the menu and set the active submenu item by using the variable 'activeSubmenuItem'
		function LoadMenu()
		{
		  document.getElementById("submenu_"+activeSubmenuItem).style.color = "white";
		  document.getElementById("ActiveSubmenuItemIcon_"+activeSubmenuItem).style.visibility = "visible";
		  if(self.name.lastIndexOf("&") == -1) {
			self.name = "news=1&account=0&community=0&library=0&forum=0<?php if($config['gifts_system']) echo '&shops=0'; ?>&";
		  }
		  FillMenuArray();
		  InitializeMenu();
		}

		function SaveMenu()
		{
		  if(unloadhelper == false) {
			SaveMenuArray();
			unloadhelper = true;
		  }
		}

		// store the values of the variable 'self.name' in the array menu
		function FillMenuArray()
		{
		  while(self.name.length > 0 ){
			var mark1 = self.name.indexOf("=");
			var mark2 = self.name.indexOf("&");
			var menuItemName = self.name.substr(0, mark1);
			menu[0][menuItemName] = self.name.substring(mark1 + 1, mark2);
			self.name = self.name.substr(mark2 + 1, self.name.length);
		  }
		}

		// hide or show the corresponding submenus
		function InitializeMenu()
		{
		  for(menuItemName in menu[0]) {
			if(menu[0][menuItemName] == "0") {
			  document.getElementById(menuItemName+"_Submenu").style.visibility = "hidden";
			  document.getElementById(menuItemName+"_Submenu").style.display = "none";
			  document.getElementById(menuItemName+"_Lights").style.visibility = "visible";
			  document.getElementById(menuItemName+"_Extend").style.backgroundImage = "url(" + IMAGES + "/general/plus.gif)";
			}
			else {
			  document.getElementById(menuItemName+"_Submenu").style.visibility = "visible";
			  document.getElementById(menuItemName+"_Submenu").style.display = "block";
			  document.getElementById(menuItemName+"_Lights").style.visibility = "hidden";
			  document.getElementById(menuItemName+"_Extend").style.backgroundImage = "url(" + IMAGES + "/general/minus.gif)";
			}
		  }
		}

		// reconstruct the variable "self.name" out of the array menu
		function SaveMenuArray()
		{
		  var stringSlices = "";
		  var temp = "";
		  for(menuItemName in menu[0]) {
			stringSlices = menuItemName + "=" + menu[0][menuItemName] + "&";
			temp = temp + stringSlices;
		  }
		  self.name = temp;
		}

		// onClick open or close submenus
		function MenuItemAction(sourceId)
		{
		  if(menu[0][sourceId] == 1) {
			CloseMenuItem(sourceId);
		  }
		  else {
			OpenMenuItem(sourceId);
		  }
		}
		function OpenMenuItem(sourceId)
		{
		  menu[0][sourceId] = 1;
		  document.getElementById(sourceId+"_Submenu").style.visibility = "visible";
		  document.getElementById(sourceId+"_Submenu").style.display = "block";
		  document.getElementById(sourceId+"_Lights").style.visibility = "hidden";
		  document.getElementById(sourceId+"_Extend").style.backgroundImage = "url(" + IMAGES + "/general/minus.gif)";
		}
		function CloseMenuItem(sourceId)
		{
		  menu[0][sourceId] = 0;
		  document.getElementById(sourceId+"_Submenu").style.visibility = "hidden";
		  document.getElementById(sourceId+"_Submenu").style.display = "none";
		  document.getElementById(sourceId+"_Lights").style.visibility = "visible";
		  document.getElementById(sourceId+"_Extend").style.backgroundImage = "url(" + IMAGES + "/general/plus.gif)";
		}

		// mouse-over effects of menubuttons and submenuitems
		function MouseOverMenuItem(source)
		{
		  source.firstChild.style.visibility = "visible";
		}
		function MouseOutMenuItem(source)
		{
		  source.firstChild.style.visibility = "hidden";
		}
		function MouseOverSubmenuItem(source)
		{
		  source.style.backgroundColor = "#14433F";
		}
		function MouseOutSubmenuItem(source)
		{
		  source.style.backgroundColor = "#0D2E2B";
		}
	</script>
	<?php echo template_place_holder('head_end'); ?>
</head>
<body onBeforeUnLoad="SaveMenu();" onUnload="SaveMenu();">
	<?php echo template_place_holder('body_start'); ?>
  <div id="top"></div>
  <div id="ArtworkHelper" style="background-image:url(<?php echo $template_path; ?>/images/header/<?php echo $config['background_image']; ?>);" >
    <div id="Bodycontainer">
      <div id="ContentRow">
        <div id="MenuColumn">
          <div id="LeftArtwork">
            <img id="Statue_1" src="<?php echo $template_path; ?>/images/header/animated-statue.gif" alt="logoartwork" />
            <img id="TibiaLogoArtworkTop" src="<?php echo $template_path; ?>/images/header/<?php echo $config['logo_image']; ?>" onClick="window.location = '<?php echo internalLayoutLink('news')?>';" alt="logoartwork" />
            <img id="TibiaLogoArtworkBottom" src="<?php echo $template_path; ?>/images/header/tibia-logo-artwork-bottom.gif" alt="logoartwork" />
            <img id="Statue_2" src="<?php echo $template_path; ?>/images/header/animated-statue.gif" alt="logoartwork" />
            <img id="LogoLink" src="<?php echo $template_path; ?>/images/header/tibia-logo-artwork-string.gif" onClick="window.location = 'mailto:<?php echo $config['lua']['ownerEmail']; ?>';" alt="logoartwork" />
          </div>

  <div id="Loginbox" >
    <div id="LoginTop" style="background-image:url(<?php echo $template_path; ?>/images/general/box-top.gif)" ></div>
    <div id="BorderLeft" class="LoginBorder" style="background-image:url(<?php echo $template_path; ?>/images/general/chain.gif)" ></div>

    <div class="Loginstatus" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-textfield-background.gif)" >
      <div id="LoginstatusText_1" class="LoginstatusText" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-font-you-are-not-logged-in.gif)" ></div>
    </div>

    <div id="LoginButtonContainer" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-textfield-background.gif)" >
      <div id="LoginButton" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif)" >
        <div onClick="LoginButtonAction();" onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);"><div class="Button" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif)" ></div>
			<?php
          echo '<div id="ButtonText" '.($logged ? '' : 'style="background-image:url('.$template_path.'/images/buttons/_sbutton_login.gif)"').'>
			 </div>';
			 ?>
        </div>
      </div>

    </div>

    <div style="clear:both" ></div>

    <div class="Loginstatus" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-textfield-background.gif)" >
      <div id="LoginstatusText_2" onClick="LoginstatusTextAction(this);" onMouseOver="MouseOverLoginBoxText(this);" onMouseOut="MouseOutLoginBoxText(this);" ><div id="LoginstatusText_2_1" class="LoginstatusText" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-font-create-account.gif)" ></div><div id="LoginstatusText_2_2" class="LoginstatusText" style="background-image:url(<?php echo $template_path; ?>/images/loginbox/loginbox-font-create-account-over.gif)" ></div></div>
    </div>

    <div id="BorderRight" class="LoginBorder" style="background-image:url(<?php echo $template_path; ?>/images/general/chain.gif)" ></div>
    <div id="LoginBottom" class="Loginstatus" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif)" ></div>
  </div>

<div id='Menu'>
<div id='MenuTop' style='background-image:url(<?php echo $template_path; ?>/images/general/box-top.gif);'></div>


<div id='news' class='menuitem'>
<span onClick="MenuItemAction('news')">
  <div class='MenuButton' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background.gif);'>
    <div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'><div class='Button' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background-over.gif);'></div>
      <span id='news_Lights' class='Lights'>
        <div class='light_lu' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ld' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>

        <div class='light_ru' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
      </span>
      <div id='news_Icon' class='Icon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-news.gif);'></div>
      <div id='news_Label' class='Label' style='background-image:url(<?php echo $template_path; ?>/images/menu/label-news.gif);'></div>
      <div id='news_Extend' class='Extend' style='background-image:url(<?php echo $template_path; ?>/images/general/plus.gif);'></div>
    </div>
  </div>
</span>
<div id='news_Submenu' class='Submenu'>
<a href='<?php echo $template['link_news']; ?>'>
  <div id='submenu_news' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_news' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Latest News</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_news_archive'];?>'>
  <div id='submenu_newsarchive' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_newsarchive' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>News Archive</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<!--a href='<?php echo $template['link_changelog'];?>'>
  <div id='submenu_changelog' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_changelog' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Changelog</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a-->

</div>
</div>


<div id='account' class='menuitem'>
<span onClick="MenuItemAction('account')">
  <div class='MenuButton' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background.gif);'>
    <div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'><div class='Button' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background-over.gif);'></div>
      <span id='account_Lights' class='Lights'>
        <div class='light_lu' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ld' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>

        <div class='light_ru' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
      </span>
      <div id='account_Icon' class='Icon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-account.gif);'></div>
      <div id='account_Label' class='Label' style='background-image:url(<?php echo $template_path; ?>/images/menu/label-account.gif);'></div>
      <div id='account_Extend' class='Extend' style='background-image:url(<?php echo $template_path; ?>/images/general/plus.gif);'></div>
    </div>
  </div>
</span>
<div id='account_Submenu' class='Submenu'>
<a href='<?php echo $template['link_account_manage']; ?>'>
  <div id='submenu_accountmanagement' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_accountmanagement' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Account Management</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_account_create']; ?>'>
  <div id='submenu_createaccount' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_createaccount' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Create Account</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_account_lost']; ?>'>
  <div id='submenu_lostaccount' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_lostaccount' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Lost Account?</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_rules']; ?>'>
  <div id='submenu_rules' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_rules' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Server Rules</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php
echo "<a href='" . $template['link_downloads'] . "'>
  <div id='submenu_downloads' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_downloads' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Downloads</div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";
?>
</div>
</div>


<div id='community' class='menuitem'>
<span onClick="MenuItemAction('community')">
  <div class='MenuButton' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background.gif);'>
    <div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'><div class='Button' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background-over.gif);'></div>
      <span id='community_Lights' class='Lights'>
        <div class='light_lu' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ld' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ru' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
      </span>
      <div id='community_Icon' class='Icon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-community.gif);'></div>
      <div id='community_Label' class='Label' style='background-image:url(<?php echo $template_path; ?>/images/menu/label-community.gif);'></div>
      <div id='community_Extend' class='Extend' style='background-image:url(<?php echo $template_path; ?>/images/general/plus.gif);'></div>

    </div>
  </div>
</span>
<div id='community_Submenu' class='Submenu'>
<a href='<?php echo $template['link_characters']; ?>'>
  <div id='submenu_characters' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_characters' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Characters</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>

  </div>
</a>
<a href='<?php echo $template['link_online']; ?>'>
  <div id='submenu_online' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_online' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Who Is Online?</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_highscores']; ?>'>
  <div id='submenu_highscores' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_highscores' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Highscores</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php if(isset($config['powergamers'])): ?>
<a href='<?php echo $template['link_powergamers']; ?>'>
  <div id='submenu_powergamers' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_powergamers' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Powergamers</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php endif; ?>
<a href='<?php echo $template['link_lastkills']; ?>'>
  <div id='submenu_lastkills' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_lastkills' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Last Kills</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_houses']; ?>'>
  <div id='submenu_houses' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_houses' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Houses</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_guilds']; ?>'>
  <div id='submenu_guilds' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>

    <div id='ActiveSubmenuItemIcon_guilds' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Guilds</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php if(isset($config['wars'])): ?>
<a href='<?php echo $template['link_wars']; ?>'>
  <div id='submenu_wars' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>

    <div id='ActiveSubmenuItemIcon_wars' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Guild wars</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php endif; ?>
<?php if(isset($config['polls'])): ?>
<a href='<?php echo $template['link_polls']; ?>'>
  <div id='submenu_polls' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>

	<div id='ActiveSubmenuItemIcon_polls' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Polls</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php endif; ?>
<?php if(tableExist('bans')): ?>
<a href='<?php echo $template['link_bans']; ?>'>
  <div id='submenu_bans' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>

    <div id='ActiveSubmenuItemIcon_bans' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Bans</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php endif; ?>
<a href='<?php echo $template['link_team']; ?>'>
  <div id='submenu_team' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_team' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Support List</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
</div>
</div>

<div id='forum' class='menuitem'>
<span onClick="MenuItemAction('forum')">
<div class='MenuButton' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background.gif);'>
	<div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'>
		<div class='Button' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background-over.gif);'></div>
			<span id='forum_Lights' class='Lights'>
				<div class='light_lu' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
				<div class='light_ld' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
				<div class='light_ru' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
			</span>
			<div id='forum_Icon' class='Icon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-forum.gif);'></div>
			<div id='forum_Label' class='Label' style='background-image:url(<?php echo $template_path; ?>/images/menu/label-forum.gif);'></div>
			<div id='forum_Extend' class='Extend' style='background-image:url(<?php echo $template_path; ?>/images/general/plus.gif);'></div>
			</div>
		</div>
</span>

        <div id='forum_Submenu' class='Submenu'>
          <?php echo $template['link_forum']; ?>
           <div id='submenu_forum' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
             <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
             <div id='ActiveSubmenuItemIcon_forum' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
             <div class='SubmenuitemLabel'>Server Forum</div>
             <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
           </div>
          </a>
        </div>
		 </div>
<div id='library' class='menuitem'>
<span onClick="MenuItemAction('library')">
  <div class='MenuButton' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background.gif);'>
    <div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'><div class='Button' style='background-image:url(<?php echo $template_path; ?>/images/menu/button-background-over.gif);'></div>
      <span id='library_Lights' class='Lights'>
        <div class='light_lu' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ld' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
        <div class='light_ru' style='background-image:url(<?php echo $template_path; ?>/images/menu/green-light.gif);'></div>
      </span>
      <div id='library_Icon' class='Icon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-library.gif);'></div>
      <div id='library_Label' class='Label' style='background-image:url(<?php echo $template_path; ?>/images/menu/label-library.gif);'></div>
      <div id='library_Extend' class='Extend' style='background-image:url(<?php echo $template_path; ?>/images/general/plus.gif);'></div>
    </div>
  </div>
</span>
<div id='library_Submenu' class='Submenu'>
<a href='<?php echo $template['link_creatures']; ?>'>
  <div id='submenu_creatures' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_creatures' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Creatures</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_spells']; ?>'>
  <div id='submenu_spells' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_spells' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Spells</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo $template['link_commands']; ?>'>
  <div id='submenu_commands' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_commands' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Commands</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<a href='<?php echo  $template['link_experienceStages']; ?>'>
  <div id='submenu_experiencestages' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_experiencestages' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Exp stages</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php

if(isset($config['freehouses'])): ?>
<a href='<?php echo $template['link_freehouses']; ?>'>
  <div id='submenu_freehouses' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_freehouses' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Free houses</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
<?php
endif;
?>

<?php
echo "<a href='" . $template['link_screenshots'] . "'>
  <div id='submenu_screenshots' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_screenshots' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Screenshots</div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";
if(isset($config['movies']))
echo "<a href='" . $template['link_movies'] . "'>
  <div id='submenu_movies' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_movies' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Movies</div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";

echo "<a href='" . $template['link_serverInfo'] . "'>
  <div id='submenu_serverinfo' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_serverinfo' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Server Info</div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";
?>
<a href='<?php echo $template['link_experienceTable']; ?>'>
  <div id='submenu_experiencetable' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_experiencetable' class='ActiveSubmenuItemIcon' style='background-image:url(<?php echo $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Experience Table</div>
    <div class='RightChain' style='background-image:url(<?php echo $template_path; ?>/images/general/chain.gif);'></div>
  </div>
</a>
</div>
</div>
<?php
if($config['gifts_system'])
{
echo "<div id='shops' class='menuitem'>
<span onClick=\"MenuItemAction('shops')\">
  <div class='MenuButton' style='background-image:url(".$template_path."/images/menu/button-background.gif);'>
    <div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'><div class='Button' style='background-image:url(".$template_path."/images/menu/button-background-over.gif);'></div>
      <span id='shops_Lights' class='Lights'>
        <div class='light_lu' style='background-image:url(".$template_path."/images/menu/green-light.gif);'></div>
        <div class='light_ld' style='background-image:url(".$template_path."/images/menu/green-light.gif);'></div>
        <div class='light_ru' style='background-image:url(".$template_path."/images/menu/green-light.gif);'></div>
      </span>
      <div id='shops_Icon' class='Icon' style='background-image:url(".$template_path."/images/menu/icon-shops.gif);'></div>
      <div id='shops_Label' class='Label' style='background-image:url(".$template_path."/images/menu/label-shops.gif);'></div>
      <div id='shops_Extend' class='Extend' style='background-image:url(".$template_path."/images/general/plus.gif);'></div>
    </div>
  </div>
</span>
</div>
<div id='shops_Submenu' class='Submenu'>
<a href='" . $template['link_points'] . "'>
  <div id='submenu_points' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_points' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'><font style=\"color: red;\">Buy Points</font></div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>
<a href='" . $template['link_gifts'] . "'>
  <div id='submenu_gifts' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_gifts' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'><div style=\"color: green;\">\$hop Offer</div></div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";
if($logged)
echo "<a href='" . $template['link_gifts_history'] . "'>
  <div id='submenu_gifts' class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)' onMouseOut='MouseOutSubmenuItem(this)'>
    <div class='LeftChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
    <div id='ActiveSubmenuItemIcon_gifts' class='ActiveSubmenuItemIcon' style='background-image:url(".$template_path."/images/menu/icon-activesubmenu.gif);'></div>
    <div class='SubmenuitemLabel'>Trans. History</div>
    <div class='RightChain' style='background-image:url(".$template_path."/images/general/chain.gif);'></div>
  </div>
</a>";
echo "</div>";
}
?>

<div id='MenuBottom' style='background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);'></div>
</div>
		<script type="text/javascript">
            InitializePage();
        </script>
        </div>
        <div id="ContentColumn">
          <div class="Content">
            <div id="ContentHelper">
			<?php echo news_place(); ?>


  <div id="<?php echo PAGE; ?>" class="Box">
    <div class="Corner-tl" style="background-image:url(<?php echo $template_path; ?>/images/content/corner-tl.gif);"></div>
    <div class="Corner-tr" style="background-image:url(<?php echo $template_path; ?>/images/content/corner-tr.gif);"></div>
    <div class="Border_1" style="background-image:url(<?php echo $template_path; ?>/images/content/border-1.gif);"></div>
    <div class="BorderTitleText" style="background-image:url(<?php echo $template_path; ?>/images/content/title-background-green.gif);"></div>
	<?php
	/*
	<img class="Title" src="<?php echo $template_path; ?>/headline.php?p=<?php if(isset($_404)) echo '404'; else echo PAGE; ?>" alt="Contentbox headline" />
*/
	if($config['site_closed'])
		$tmp_page = $config['site_closed_title'];
	else
		$tmp_page = (isset($_404) ? '404' : PAGE);

	$headline = $template_path.'/images/header/headline-' . $tmp_page . '.gif';
	if(!file_exists($headline))
		$headline = $template_path . '/headline.php?t=' . ucfirst($tmp_page);
?>
	<img class="Title" src="<?php echo $headline; ?>" alt="Contentbox headline" />
    <div class="Border_2">
      <div class="Border_3">
	<?php
	if(PAGE == 'news' && $config['lua']['serverName'] == "SlaskiOTS" && !isset($_GET['archive']) && !isset($_GET['id']))
	{
	?>
        <div style="background-image:url(<?php echo $template_path; ?>/images/content/scroll.gif);">
		<table style="clear:both; font-family: Verdana, Arial, Times New Roman, sans-serif;font-size: 10pt; padding: 8px" border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr><td>
			Witaj na Śląskim serwerze.<br>Przed rozpoczęciem gry zapoznaj z podstroną <a href="<?php echo internalLayoutLink('serverInfo')?>">Server Info</a>.<br>
			O pomoc w grze zawsze możesz poprosić na kanale <b>Help</b>.<br><br>

			Ostatnio zarejestrował się:
			<?php
				$newestMember = $db->query('SELECT `name` FROM `players` ORDER BY `id` DESC LIMIT 1');
				$newestMember = $newestMember->fetch();
				echo getPlayerLink($newestMember['name']) . '. Witamy!';
			?>
		</td></tr>
		</table>
	</div>
	<?php
		if(tableExist('wodz_exphistory'))
		{
			$top_enabled = true;
				function write_top($name, $list, $colspan = 2)
				{
					global $config;
					echo '
						<TD WIDTH=' . ($colspan == 2 ? '30' : '40') . '% VALIGN="TOP">'.
							'<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
								<TR BGCOLOR='.$config['vdarkborder'].'>
									<TD COLSPAN=' . $colspan . ' class="white"><B>TOP 5 - ' . $name . '</B></TD>
								</TR>';

							$i = 1;
							foreach($list as $str)
								echo '<TR BGCOLOR='.getStyle($i++).'>' . $str . '</TR>';
							echo
							'</TABLE>
						</TD>';
				}
			?>
		<div class="BoxContent" style="background-image:url(<?php echo $template_path; ?>/images/content/scroll.gif);">
			<TABLE BORDER=0 WIDTH=100%>
				<tr>
					<?php
						$vocation_name_short = array(
							array(
								1 => 'S',
								2 => 'D',
								3 => 'P',
								4 => 'K'
							),
							array(
								1 => 'MS',
								2 => 'ED',
								3 => 'RP',
								4 => 'EK'
							)
						);

						//players
						$top_array = array();
						$top_players_query = $db->query(
							'SELECT `name`, `level`, `experience`, `vocation`, `promotion`, `online` FROM `players`' .
							' WHERE players.deleted = 0 AND players.group_id < '.$config['highscores_groups_hidden'] .
							' ORDER BY `experience` DESC' .
							' LIMIT 5');

						$i = 0;
						foreach($top_players_query as $player)
						{
							$top_array[$i++] =
								'<TD>' . $i . '.</TD>
								<TD VALIGN=top>
									<a href="' . getPlayerLink($player['name'], false) . '">'.($player['online']>0 ? "<font color=\"green\">".$player['name']."</font>" : "<font color=\"red\">".$player['name']."</font>").'</a>
									<small>('.$player['level'].' '.$vocation_name_short[$player['promotion']][$player['vocation']].')</small>
								</TD>';
						}
						write_top('<a href="' . internalLayoutLink('highscores') . '" class="white">Players</a>', $top_array);

						//powergamers
						$top_array = array();
						$today = getZeroDay();
						$top_powergamers_query = $db->query(
							'SELECT `wodz_exphistory`.`exp_change` as exp_change, `name`, `players`.`level`, `players`.`experience`, `vocation`, `promotion`, `online`' .
							' FROM `wodz_exphistory`, `players`' .
							' WHERE players.id = wodz_exphistory.player_id AND `date` = ' . $today . ' ORDER BY `exp_change` DESC LIMIT 5');

						$i = 0;
						foreach($top_powergamers_query as $player)
						{
							$top_array[$i++] =
								'<TD>' . $i . '.</TD>
								<TD VALIGN=top>
									<a href="' . getPlayerLink($player['name'], false) . '">'.($player['online']>0 ? "<font color=\"green\">".$player['name']."</font>" : "<font color=\"red\">".$player['name']."</font>").'</a>
									<small>('.$player['level'].' '.$vocation_name_short[$player['promotion']][$player['vocation']].')</small>
								</TD>
								<TD><small>' . formatExperience($player['exp_change']) . '</small></TD>';
						}
						write_top('<a href="' . internalLayoutLink('powergamers') . '" class="white">Powergamers</a> (Today)', $top_array, 3);

						//guilds
						$top_array = array();
						$top_guilds_query = $db->query(
							'SELECT `name`, `total_level`' .
							' FROM `guilds`' .
							' ORDER BY `total_level` DESC' .
							' LIMIT 5');

						$i = 0;
						foreach($top_guilds_query as $guild)
						{
							$top_array[$i++] =
								'<TD>' . $i . '.</TD>
								<TD>' . getGuildLink($guild['name']) . '</TD>
								<TD><small>' . $guild['total_level'] . '</small></TD>';
						}
						write_top('<a href="' . internalLayoutLink('guilds') . '" class="white">Guilds</a>', $top_array, 3);
					?>
				</tr>
			</table>
	<?php
		}
	}
	else
	{
	?>
	<div class="BoxContent" style="background-image:url(<?php echo $template_path; ?>/images/content/scroll.gif);">
	<?php
	}
	?>
			<?php echo template_place_holder('center_top') . $content; ?>
		</div>
      </div>
    </div>
    <div class="Border_1" style="background-image:url(<?php echo $template_path; ?>/images/content/border-1.gif);"></div>

    <div class="CornerWrapper-b"><div class="Corner-bl" style="background-image:url(<?php echo $template_path; ?>/images/content/corner-bl.gif);"></div></div>
    <div class="CornerWrapper-b"><div class="Corner-br" style="background-image:url(<?php echo $template_path; ?>/images/content/corner-br.gif);"></div></div>
  </div>
           </div>
          </div>
          <div id="Footer"><?php echo template_footer(); ?><br/>Layout by CipSoft GmbH.</div>
        </div>
        <div id="ThemeboxesColumn">
          <div id="RightArtwork">
		  <?php
			//$tmp_link =
			//if($config['friendly_urls'])
		  ?>
            <img id="Monster" src="images/monsters/<?php echo logo_monster() ?>.gif" onClick="window.location = '?subtopic=creatures&creature=<?php echo $config['logo_monster'] ?>';" alt="Monster of the Week" />
            <img id="PedestalAndOnline" src="<?php echo $template_path; ?>/images/header/pedestal-and-online.gif" alt="Monster Pedestal and Players Online Box"/>
          <div id="PlayersOnline" onClick="window.location = '<?php echo $template['link_online']; ?>'">
		  <?php
			if($status['online'])
				echo '<div id="players" style="display: inline;">' . $status['players'] . '</div><br>Players Online';
			else
				echo '<font color="red"><b>Server<br />OFFLINE</b></font>';
			?></div>
        </div>

        <div id="Themeboxes">
			<?php if(in_array("newcomer", $config['boxes'])): ?>
			<div id="NewcomerBox" class="Themebox" style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/newcomer/newcomerbox.gif);">
				<a class="ThemeboxButton" href="<?php echo $template['link_account_create']; ?>" onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif);">
					<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);"></div>
					<div class="ButtonText" style="background-image:url(<?php echo $template_path; ?>/images/buttons/_sbutton_jointibia.gif);"></div>
				</a>
				<div class="Bottom" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
			</div>
			<?php endif; ?>
			<?php if(in_array("premium", $config['boxes'])): ?>
			<div id="PremiumBox" class="Themebox" style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/premium/premiumbox.gif);">
				<a class="ThemeboxButton" href="<?php echo internalLayoutLink('premium'); ?>" onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif);">
					<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);"></div>
					<div class="ButtonText" style="background-image:url(<?php echo $template_path; ?>/images/buttons/_sbutton_getpremium.gif);"></div>
				</a>
				<div class="Bottom" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
			</div>
			<?php endif; ?>
			<?php if(PAGE == 'news' && in_array("screenshots", $config['boxes'])): ?>
			<div id="ScreenshotBox" class="Themebox" style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/screenshot/screenshotbox.gif);">
				<a href="?subtopic=screenshots&screenshot=<?php echo $config['screenshot']; ?>" >
					<img id="ScreenshotContent" class="ThemeboxContent" src="images/screenshots/<?php echo $config['screenshot']; ?>_thumb.gif" alt="Screenshot of the Day" />
				</a>
				<div class="Bottom" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
			</div>
			<?php endif; ?>
			<?php if(PAGE == 'news' && in_array("poll", $config['boxes'])):
				$poll = $db->query('SELECT id, question FROM '.$db->tableName(TABLE_PREFIX . 'polls') . ' WHERE end > ' . time() . ' ORDER BY end LIMIT 1');
				if($poll->rowCount() > 0)
				{
					$poll = $poll->fetch();
					?>
			<div id="CurrentPollBox" class="Themebox" style="background-image:url(<?php echo $template_path; ?>/images/themeboxes/current-poll/currentpollbox.gif);">
				<div id="CurrentPollText"><?php echo $poll['question']; ?></div>
					<a class="ThemeboxButton" href="<?php echo internalLayoutLink('polls') . '&id=' . $poll['id']; ?>" onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif);">
					<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);"></div>
					<div class="ButtonText" style="background-image:url(<?php echo $template_path; ?>/images/buttons/_sbutton_votenow.gif);"></div>
				</a>
				<div class="Bottom" style="background-image:url(<?php echo $template_path; ?>/images/general/box-bottom.gif);"></div>
			</div>
			<?php
				}
			endif; ?>
<br/><br/>
 <?php
	if($config['template_allow_change'])
		 echo '<font color="white">Template:</font><br/>' . template_form();
 ?>
        </div>
      </div>
     </div>
    </div>
  </div>
	<?php echo template_place_holder('body_end'); ?>
</body>
</html>
<?php
function logo_monster()
{
	global $config;
	return str_replace(" ", "", trim(mb_strtolower($config['logo_monster'])));
}
?>
