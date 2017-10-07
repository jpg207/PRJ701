<?php
    header("Content-Type: text/html; charset=ISO-8859-1");
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A layout example that shows off a responsive product landing page.">
    <title>Comp Creator</title>

    <link rel="stylesheet" href="../Styles/purecss-main-min.css" integrity="sha384-" crossorigin="anonymous">
    <link rel="stylesheet" href="../Styles/purecss-grids.css">
    <link rel="stylesheet" href="../Styles/font-awesome.css">

    <link rel="stylesheet" href="../styles/styles.css">

    <script src="../Scripts/jquery-min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.header_background').click(function(e) {
            if ($(this).parent().next('.dropdown').is(':visible')){
                var object = this;
                $(this).parent().next('.dropdown').slideUp('slow', function(){
                    $(object).parent().parent().parent().toggleClass('clicked');
                    $(object).parent('.result-head').toggleClass('clicked');
                });
                var img = $(this).find('img.expand_icon')[0];
                img.src = '../Images/plus.png';
            }else{
                $(this).parent().next('.dropdown').slideDown('slow');
                $(this).parent().next('.dropdown').slideDown('slow');
                $(this).parent().parent().parent().toggleClass('clicked');
                $(this).parent('.result-head').toggleClass('clicked');
                var img = $(this).find('img.expand_icon')[0];
                img.src  = '../Images/minus.png';
            }
        });

        $('.price-container').click(function(e) {
            $('.price').toggleClass('clicked');
        });

        $('input[type=radio]').change(function(){
            $('form').submit();
       });
    });
    </script>
</head>
