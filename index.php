<?php include("include/init.php");?>
<?php include("include/header.php"); ?>
<div class="top_line">
	<a href="/" style="float:left;"> <img class="logo" border=0 src="<?php echo $basedir; ?>imgs/ico/home.png" alt="home"/></a>
	<a href="ru" style="float:right;"><img class="logo" border=0 src="<?php echo $basedir; ?>imgs/ico/russia.png" alt="ru"/></a>
	<a href="fr" style="float:right;"><img class="logo" border=0 src="<?php echo $basedir; ?>imgs/ico/france.png" alt="fr"/></a>
</div>

<!-- Dict menu -->
<?php if ($page!="intro") { ?>
<div class="menu_dict">
	<div class="ui menu">
	  <div class="ui simple dropdown item">
	    <?php echo $locale["dict"]; ?>
	    <i class="dropdown icon"></i>
	    <div class="menu">
	      <a class="item" href="/about"><?php echo $locale["DAF"]; ?></a>
	      <a class="item" href="/aboutj1"><?php echo $locale["DINAF"]; ?></a>
	      <a class="item" href="/aboutj"><?php echo $locale["DINAFN"]; ?></a>
	    </div>
	  </div>
	</div>
</div>
<?php } ?>
<div class="menu_bg">
	<div id="navigator">
	<ul id="nav-menu">
<?php
    	if(($page=="dict") ||($page=="experiment")||($page=="about")||($page=="authors") || ($page =="help")) {
?>
		<li <?php if($page=="about"){echo "id=\"nav-menu-act\"";}?>><a href="about"><?php echo $locale['about']; ?></a></li>
		<li <?php if($page=="authors"){echo "id=\"nav-menu-act\"";}?>><a href="authors"><?php echo $locale['authors']; ?></a></li>
		<li <?php if($page=="dict"){echo "id=\"nav-menu-act\"";}?>><a href="dict"><?php echo $locale['dict']; ?></a></li>
		<li <?php if($page=="help"){echo "id=\"nav-menu-act\"";}?>><a href="help"><?php echo $locale['help']; ?></a></li>
<!--		<li <?php if($page=="experiment"){echo "id=\"nav-menu-act\"";}?>><a href="experiment"><?php echo $locale['experiments']; ?></a></li> -->
<?php } elseif(($page=="joint")||($page=="bjoint")||($page=="aboutj")||($page=="authorsj") || ($page=="helpj")) { ?>
		<li <?php if($page=="aboutj"){echo "id=\"nav-menu-act\"";}?>><a href="aboutj"><?php echo $locale['about']; ?></a></li>
		<li <?php if($page=="authorsj"){echo "id=\"nav-menu-act\"";}?>><a href="authorsj"><?php echo $locale['authors']; ?></a></li>
		<li <?php if($page=="joint"){echo "id=\"nav-menu-act\"";}?>><a href="joint"><?php echo $locale['dict']; ?></a></li>
		<li <?php if($page=="helpj"){echo "id=\"nav-menu-act\"";}?>><a href="helpj"><?php echo $locale['help']; ?></a></li>
<?php } elseif(($page=="joint1")||($page=="bjoint1")||($page=="aboutj1")||($page=="authorsj1") || ($page=="helpj1")) { ?>
		<li <?php if($page=="aboutj1"){echo "id=\"nav-menu-act\"";}?>><a href="aboutj1"><?php echo $locale['about']; ?></a></li>
		<li <?php if($page=="authorsj1"){echo "id=\"nav-menu-act\"";}?>><a href="authorsj1"><?php echo $locale['authors']; ?></a></li>
		<li <?php if($page=="joint1"){echo "id=\"nav-menu-act\"";}?>><a href="joint1"><?php echo $locale['dict']; ?></a></li>
		<li <?php if($page=="helpj1"){echo "id=\"nav-menu-act\"";}?>><a href="helpj1"><?php echo $locale['help']; ?></a></li>
<?php } else { ?>

<?php } ?>
	</ul>
	</div>
</div>
<div class="content">
<?php
	if(       ($page=="about")
		||($page=="aboutj")
		||($page=="aboutj1")
		||($page=="help")
		||($page=="helpj")
		||($page=="helpj1")
		||($page=="authors")
		||($page=="authorsj")
		||($page=="authorsj1")
		||($page=="dict")
		||($page=="intro")
		||($page=="joint")
		||($page=="joint1")
		||($page=="bjoint")
		||($page=="bjoint1")
		||($page=="experiment")){
			include("pages_".$lang."/".$page.".php");
		}
	  else
	  {
	  		include("pages_".$lang."/unknown.php");
	  }
		?>
</div>

<?php include("include/footer.php"); ?>
