<?php
    session_start();

    if (empty($_POST['email']) || empty($_POST['password']))
    {
        echo "Invalid Email/or Password!";
    }
    else
    {
        $username = $_POST['email'];
        $password = $_POST['password'];

        $username = sanStrCheck($username);
        $password = sanStrCheck($password);

        $connection = mysql_connect("mudfoot.doc.stu.mmu.ac.uk", "degnenj", "Slorgank9");
        $db = mysql_select_db("degnenj", $connection);

        if (preg_match("/^\d{4}@nhs\.org$/", $username) == true)
        {
			$password = passwordMD5($password);
            $query = mysql_query("SELECT * FROM worker WHERE password='".$password."' AND workerID_Email='".$username."'");
            $rows = mysql_num_rows($query);
            $fetch = mysql_fetch_assoc($query);
        }
        else if (preg_match("/^[A-Z]\.[A-Z][\w-]+@nhs\.org$/", $username) == true)
        {
			$password = passwordMD5($password);
            $query = mysql_query("SELECT * FROM worker WHERE password='".$password."' AND workerEmail='".$username."'");
            $rows = mysql_fetch_array($query);
        }
        else
        {
            //echo "Invalid Email/or Password!";
        }
            if ($rows)
            {
                $_SESSION['userCur'] = $rows['surname'];
                $_SESSION['idCur'] = $rows['idNumber'];
                $_SESSION['levelCur'] =$rows['worker_level'];

                header("location: bookedDays.php");
            }
            else
            {
				var_dump($username.$password);
                echo "The Record Cannot Be Found.";
            }
            mysql_close($connection);
}
function passwordMD5($var)
{
    $var = md5($var);
    return $var;
}

function sanStrCheck($var)
{
    if(get_magic_quotes_gpc())
    {
        $var = stripcslashes($var);
    }
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
}