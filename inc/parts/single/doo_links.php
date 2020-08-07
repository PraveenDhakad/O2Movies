<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="robots" content="noindex, follow">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
        <title><?php echo wp_kses_post($titl);?></title>
        <link rel='stylesheet' id='fonts-css'  href='https://fonts.googleapis.com/css?family=Roboto:400,500' type='text/css' media='all' />
        <link rel='stylesheet' id='link-single'  href='<?php echo DOO_URI,'/assets/css/front.links.css'; ?>' type='text/css' media='all' />
        <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script type='text/javascript'>
            var Link = <?=$json; ?>;
        </script>
        <?php if($ganl){ ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$ganl;?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?=$ganl;?>');
        </script>
        <?php } ?>
        <style type='text/css'>
            :root {
                --main-color:<?=$clor;?>;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="container">
                <div class="box" style="border-top: solid 3px <?=$clor;?>">
                    <?php if($adst) echo "<div class='ads top'>{$adst}</div>"; ?>
                    <div class="inside">
                        <div class="counter">
                            <span id="counter"><?=$time;?></span>
                            <small><?php _d('Please wait until the time runs out'); ?></small>
                        </div>
                        <a id="link" href="<?php echo $murl; ?>" class="btn" style="background-color:<?=$clor;?>"><?=$btxt;?></a>
                        <small class="text"><?=$txun;?></small>
                        <small class="text"><a href="<?=$prml;?>"><?=$titl;?></a></small>
                    </div>
                    <?php if($adsb) echo "<div class='ads bottom'>{$adsb}</div>"; ?>
                </div>
                <?php if($type === __d('Torrent')) { ?>
                    <small class="footer"><?php _d('Get this torrent'); ?></small>
                <?php } else { ?>
                    <small class="footer"><?php echo sprintf( __d('Are you going to %s'), '<strong>'.$domn.'</strong>'); ?></small>
                <?php } ?>
            </div>
        </div>
    </body>
    <script type='text/javascript' src='<?php echo doo_compose_javascript('front.links'); ?>'></script>
</html>
