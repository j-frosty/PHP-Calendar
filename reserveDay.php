<?php
include 'variablesGlobal.php';
include 'session.php';

$GLOBALS ['confirm']= '';
$idCur=$_POST['cid'];
$selDate=$_POST['cur_date'];
$levelCur=$_POST['cur_level'];
$userCur=$_POST['cur_user'];

        $curDate = curDate_global;
        list($year, $month, $day) = explode('-', $curDate);
        $time = mktime(0, 0, 0, $month, $day, $year);
        $time = strtotime('+ ' . weekP_global. ' week', $time);
        $maxDate = date('Y-m-d', $time);
        $selWeek = date('W', strtotime($selDate));
        $selDay = date('N', strtotime($selDate));
        $selYear = date('Y', strtotime($selDate));

        if ($selYear < year_global)
        {
            $result = "Error - Sorry, You Are Unable To Book Back In Time!";
        }

        elseif ($selYear > year_global)
        {
            if ($selDate > $maxDate)
            {
                $result = ('Error - You Have Exceeded The 3 Months Booking Limit!');
            }

            else
            {
                addBookedDay($idCur, $selDate, $levelCur, $userCur);
            }
        }
        elseif ($selYear === year_global)
        {
            if ($selWeek < weekNo_global || $selWeek == weekNo_global && $selDay < dayNo_global)
            {
                if($selDate >= date("Y-m-d") && $selDate < $maxDate)
                {
                    addBookedDay($idCur, $selDate, $levelCur, $userCur);
                }
                else
                {
                $result = "Error - Sorry, You Are Unable To Book Back In Time!";
                }
            }
            else
            {
                addBookedDay($idCur, $selDate, $levelCur, $userCur);
            }
        }
        else
        {
            addBookedDay($idCur, $selDate, $levelCur, $userCur);
        }

function addBookedDay($users_idNo,$processDate,$processLevel,$processname)
{
        include 'databaseConn.php';
		$check = "SELECT id_worker, book_day from reserved_days where id_worker='".$users_idNo."' AND book_day='".$processDate."'";
        global $result;
        $Qresult = mysqli_query($dbcon, $check);
		$row = mysqli_fetch_array($Qresult);
			if(!$row)
            {
                $query = "INSERT INTO reserved_days (idreserved_days, worker_name, worker_level, id_worker, book_day) VALUES (0,'".$processname."','".$processLevel."',".$users_idNo.",'".$processDate."')";$weekCheck = checkResLim($processDate,$users_idNo);
                    if($weekCheck == true)
                    {
                        if ($dbcon->query($query) === TRUE);

                        else
                        {
                            $result = "Error: " . $query . "<br>" . $dbconnect->error;
                        }
                    }
                    else if($weekCheck == false)
                    {
                        $result = "Error: You Have Reached Your 5 Day Booking Limit!";
                    }
			}
            else
            {
                $removalAllowed  = allowBookedRemoval($processDate);
                    if($removalAllowed == true)
                    {
                        $query = "DELETE FROM reserved_days WHERE `book_day` = '".$processDate."' AND id_worker ='".$users_idNo."'";
                        if($dbcon->query($query)===TRUE);
                    }
                    else
                    {
                        $result = "Error: Violation";
                    }
			}
}

function allowBookedRemoval($processDate)
{
    $user_level = Array();
    include 'databaseConn.php';
    $query = 'SELECT * FROM reserved_days WHERE DATE(`book_day`) = "'.$processDate.'"';

    if ($Qresult = mysqli_query($dbcon, $query))
    {
        while ($row = mysqli_fetch_assoc($Qresult))
        {
            $user_level[] = $row['worker_level'];
        }
    }

    $userStandard = 0;
    $userSenior = 0;

    for($i = 0; $i<count($user_level);$i++)
    {
        if($user_level[$i] == "Standard")
        {
            $userStandard++;
        }
        else if($user_level[$i] == "Senior")
        {
            $userSenior++;
        }
    }

    if($userStandard == 2 && $userSenior == 1)
    {
        return false;
    }

    else
    {
        return true;
    }
}
function checkResLim($process_date,$process_user_id)
{
    include 'databaseConn.php';
    $reservedDates = Array();
    $dayName = date('l', strtotime($process_date));

    if($dayName == "Monday")
    {
        $checkMonday = date('Y-m-d',strtotime("this monday", strtotime($process_date)))."<br>";$checkSunday = date('Y-m-d',strtotime("this sunday", strtotime($process_date)));
    }
    else
    {
        $checkMonday = date('Y-m-d',strtotime("last monday", strtotime($process_date)))."<br>";$checkSunday = date('Y-m-d',strtotime("this sunday", strtotime($process_date)));
    }

    $query = "SELECT * FROM reserved_days WHERE DATE(`book_day`) >= '".$checkMonday."' AND DATE(`book_day`) <= '".$checkSunday."' AND id_worker = ".$process_user_id;

    if ($Qresult = mysqli_query($dbcon, $query))
    {
        while ($row = mysqli_fetch_assoc($Qresult))
        {
            $reservedDates[] = $row;
        }
    }

    if(count($reservedDates) >= 5)
    {
        return false;
    }
    else if (count($reservedDates) <= 5)
    {
        return true;
    }
    else
    {
        return false;
    }
}
echo $result;
?>