<?php
ob_start("ob_gzhandler");

?>

<!doctype html>
<html>
<title>Everything</title>
<head>
    <meta charset="utf-8">
    <meta name="author" content="InstanceLabs">
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="jquery-letterfx.min.css">
    <script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="jquery-letterfx.min.js"></script>
    <script src="https://www.youtube.com/player_api"></script>
    <script>
        $(document).ready(function(){
            $('.modulehead').letterfx({"fx":"swirl", "fx_duration":"150ms"});

            $("body").sortable();
            $("body").sortable({ handle: ".modulehead" });

            loadYoutubeModule();
            loadRedditModule();

            

        });
        
        
        
        // Reddit
        
        function loadRedditModule(){
            
            $.getJSON("http://www.reddit.com/.json", function( data ) {
                var obj = data;
                for (var i = 0, len = obj.data.children.length; i < len; ++i) {
                    var post = obj.data.children[i];
                    $('.reddit.modulebody').append('<div class="reddit modulesegment" data="' + i + '">' + post.data.title + '</div>');
                }
                $('.reddit.loader').remove();
            });

        }
        
        
        // Youtube
        
        var currentid = 0;
        var player;
        var interval;
        var ids;
        var started_playing = false;

        function loadYoutubeModule(){
            var jqxhr = $.ajax({
                url: "YoutubeModule.php",
                dataType: "json"
            })
            .done(function(res) {
                ids = res;
                //playNext();
                $('.yt.loader').remove();
                for (i = 0; i < ids.length; i++) {
                    $('.yt.modulebody').append('<div class="yt modulesegment" data="' + i + '">' + ids[i][1] + '</div>');
                }
                $("#yt").before('<center><div class="yt playicon"><div class="yt playicon-inner"></div></div></center>');
                $(".yt.playicon").on("click", function(){
                    if(!$(this).hasClass("toggled")){
                        $(this).html('<div class="yt pauseicon-inner"></div>');
                        $(this).toggleClass("toggled");
                        if(started_playing){
                            player.playVideo();
                        } else {
                            playNext();
                            started_playing = true;
                        }
                    } else {
                        $(this).html('<div class="yt playicon-inner"></div>');
                        $(this).toggleClass("toggled");
                        player.pauseVideo();
                    }
                });
                $(".yt.modulesegment").on("click", function(){
                    playNext($(this).attr("data"));
                });
            });
        }
        
        function playNext(i){
            i = (typeof i === "undefined") ? currentid : i;
            clearInterval(interval);
            player.loadVideoById(ids[i][0]);
            document.title = ids[i][1];
            interval = setInterval(calcProgress, 500); //check status
            currentid = i + 1;
        }

        function calcProgress(){
            var t = player.getCurrentTime();
            var d = player.getDuration();
            $('.bar').css("width", Math.round(t / d * 100) + "%");
        }

        function onYouTubePlayerAPIReady() {
            player = new YT.Player('yt', {
                height: 1,
                width: 1,
                videoId: 'JKL6ZhObBQs',
                playerVars: {
                    autoplay: 0, // 1
                    controls: 0,
                    rel: 0,
                    showinfo: 0,
                    autohide: 1,
                    iv_load_policy: 3
                },
                events: {
                    'onStateChange': onPlayerStateChange,
                    'onError': onError
                }
            });
        }

        function onPlayerStateChange(event) {      
            if(event.data === 0) {          
                playNext();
            }
        }

        function onError(event){
            playNext();
        }
    </script>
</head>
<body>
    
    <div class="module youtube">
        <div class="modulehead">
            Youtube Music
        </div>
        <div class="yt modulebody">
            <center><div class="yt loader"><div class="loader-inner"></div></div></center>
            <div id='yt' style='width: 0px; height: 0px'></div>
        </div>
    </div>
    <div class="module soundcloud">
        <div class="modulehead">
            Soundcloud Music
        </div>
        <div class="modulebody">
            <center><div class="sc loader"><div class="loader-inner"></div></div></center>
        </div>
    </div>
    <div class="module reddit">
        <div class="reddit modulehead">
            Reddit
        </div>
        <div class="reddit modulebody">
            <center><div class="reddit loader"><div class="loader-inner"></div></div></center>
        </div>
    </div>

   
    
    
</body>
</html>

