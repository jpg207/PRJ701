<?php
    header("Content-Type: text/html; charset=ISO-8859-1");
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example that shows off a responsive product landing page.">
    <title>Comp Creator</title>

    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

    <link rel="stylesheet" href="../styles/styles.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.header_background').click(function(e) {
            if ($('.result-details').is(':visible')){
                var object = this;
                $(this).parent().next('.result-details').slideUp('slow', function(){
                    $(object).parent().parent().parent().toggleClass('clicked');
                    $(object).parent('.result-head').toggleClass('clicked');
                });
                var img = $(this).find('img.expand_icon')[0];
                img.src = '../Images/plus.png';
            }else{
                $(this).parent().next('.result-details').slideDown('slow');
                $(this).parent().parent().parent().toggleClass('clicked');
                $(this).parent('.result-head').toggleClass('clicked');
                var img = $(this).find('img.expand_icon')[0];
                img.src  = '../Images/minus.png';
            }
        });

        $('.header_backgroundall').click(function(e) {
            if ($('.result-details').is(':visible')){
                $('.result-details').slideUp('slow');
                $('.result-container').toggleClass('clicked');
                $('.result-head').toggleClass('clicked');
                var img = $(this).find('img.expand_icon')[0];
                img.src = '../Images/plus.png';
            }else{
                $('.result-details').slideDown('slow');
                $('.result-container').toggleClass('clicked');
                $('.result-head').toggleClass('clicked');
                var img = $(this).find('img.expand_icon')[0];
                img.src  = '../Images/minus.png';
            }
        });
    });
    </script>
</head>
