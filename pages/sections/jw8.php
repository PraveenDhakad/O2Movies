<!DOCTYPE html>
<html lang="en" dir="ltr" data-cast-api-enabled="true">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>JW Player</title>
        <script src='<?php echo $jwplal;?>'></script>
        <script type="text/javascript">
            var jw = <?php echo json_encode($data)."\n"; ?>
        </script>
        <style type="text/css" media="all">
            body.jwplayer{margin:0 auto;padding:0;overflow:hidden;background:#000}
            #player {width:100%;height:100%;overflow:hidden}
        </style>
    </head>
    <body class="jwplayer">
        <div id="player"></div>
        <script type="text/javascript">
            const player = jwplayer('player').setup({
                image: jw.image,
                mute: false,
                volume: 25,
                autostart: jw.auto,
                repeat: false,
                abouttext: jw.text,
                aboutlink: jw.link,
                skin: {
                    active: jw.color
                },
                logo: {
                    file: jw.logo,
                    hide: true,
                    link: jw.link,
                    margin: '15',
                    position: jw.lposi
                },
                sources: [{
                    file: jw.file,
                    type: 'video/mp4'
                }],
            })
        </script>
    </body>
</html>
