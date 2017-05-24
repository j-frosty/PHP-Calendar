<?php
    $connection = mysql_connect("mudfoot.doc.stu.mmu.ac.uk", "degnenj", "Slorgank9");
    $db = mysql_select_db("degnenj", $connection);

    if(!session_id()) session_start();
        $user_check=$_SESSION['idCur'];
        $ses_sql=mysql_query("select idNumber from worker where idNumber=$user_check", $connection);
        $row = mysql_fetch_assoc($ses_sql);
        $login_session =$row['idNumber'];

        if(!isset($login_session))
        {
            mysql_close($connection);
            header('Location: index.php');
        }
?>