<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="et" xml:lang="et">
<head>
  <title><?php echo $title; ?></title>
  <base href="<?php echo BASE_URI; ?>" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="kambja.css" />
  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="kambja.js"></script>
</head>
<body <?php echo $body_class; ?>>

<div id="container">

  <h1><a href="<?php echo LANG."/index"; ?>"><strong>Kambja</strong> <small>Hotell</small></a></h1>

  <?php echo $lang_menu; ?>
   
  <div id='nav'><div>
  <?php echo $main_menu; ?>
  </div></div>
    
  <?php echo $edit_menu; ?>

  <div id="body">
    <div id="content">
      <?php echo $content; ?>
    </div>
    
    <div id="sidebar">
     <div id="animation"></div>
     
     <h2><?php echo _("Partners:"); ?></h2>
     <ul>
      <li><a href="http://www.veinimaailm.ee/">Veinimaailm</a></li>
      <li><a href="http://www.kindlusgrupp.ee/">Kindlus Grupp</a></li>
      <li>Soval OÜ</li>
     </ul>
    </div>
    
    <p id="footer">
      <?php echo $admin_link; ?>
      <?php echo _("Address:"); ?> Tartu maakond, Kambja, Võru mnt 2.
      <?php echo _("Phone:"); ?> +372 711 4497, +372 509 3253.
      <?php echo _("E-mail:"); ?> info@kindlusgrupp.ee
    </p>
  </div>
</div>

</body>
</html>
