<html>
    <?php
    include 'session.php';
    include 'databaseConn.php';
    ?>

    <head>
        <link href = 'css/display_cal.css' type = 'text/css' rel = 'stylesheet' />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>
    <body>

    <script>
        function getCurVal(element)
        {
         var datePressed = element;
            if(datePressed != "")
            {
                 var idCur = <?php echo $_SESSION['idCur']?>;
                 var levelCur = "<?php echo $_SESSION['levelCur']?>";
                 var userCur = "<?php echo $_SESSION['userCur']?>";

                    $.ajax(
                        {
                            url: 'reserveDay.php', type: 'POST', data:
                            {
                                cid: idCur, cur_date: datePressed, cur_level: levelCur, cur_user:userCur
                            }
                        }).success(function( msg )
                        {
                        if(msg.indexOf('Error') > -1)
                        {
                            alert(msg);
                        }
                        else
                        {
                            location.reload();
                        }
                    });
            }
            else
            {
                alert("The Date You Have Chosen Is Invalid, Please Select Another");
            }
         }
    </script>

    <div id='cssmenu'>
        <ul>
            <li class='active'><a href='signOut.php'><span>Logout</span></a></li>
            <li><a href='#'><span>Update Details</span></a></li>
        </ul>
    </div>

    <?php
    include 'displayCalendar.php';
        $calendar = new Calendar();
        echo $calendar->show();
    ?>
    </body>
</html>  
