<?php

include 'variablesGlobal.php';
include 'session.php';

class Calendar
{
    public function __construct()
    {
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }

    private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
    private $curYear=0;
    private $curMonth=0;
    private $curDay=0;
    private $prevDay=0;
    private $nullDays=0;
    private $curDate=null;
    private $daysInMonth=0;
    private $naviHref= null;

    public function show()
    {
	include 'databaseConn.php';
		$query = "SELECT * FROM reserved_days";

		if ($result = mysqli_query($dbcon, $query))
        {
			$user_id = array();
			$date = array();
			$surname = array();
            $level = array();
            $all = array();
            
			while ($row = mysqli_fetch_assoc($result))
            {
				$user_id[] = $row['id_worker'];
				$date[] = $row['book_day'];
				$surname[] = $row['worker_name'];
				$level[] = $row['worker_level'];
                $all[] = $row;
			}
		}
		
		$year  = null; 
        $month = null;
         
        if(null==$year&&isset($_GET['year']))
        {
            $year = $_GET['year'];
         
        }
        else if(null==$year)
        {
            $year = date("Y",time());
        }          
         
        if(null==$month&&isset($_GET['month']))
        {
            $month = $_GET['month'];
        }
        else if(null==$month)
        {
            $month = date("m",time());
        }                  
         
        $this->curYear=$year;
        $this->curMonth=$month;
        $this->daysInMonth=$this->numberDays_Month($month,$year);

        $content='<div id="calendar">'.
                        '<div class="box">'.
                            $this->navCreate().
                        '</div>'.
                        '<div class="box-content">';
                                $content .= '<ul class="label">';
                                $weeksInMonth = $this->numberWeeks_Month($month,$year);
                                for( $i=0; $i<$weeksInMonth; $i++ )
                                {
                                    $content .= $this->c_labels();
                                }
                                $content.='</ul>';
                                $content.='';
                                $content.='<ul class="dates">';

                                $weeksInMonth = $this->numberWeeks_Month($month,$year);

                                for( $i=0; $i<$weeksInMonth; $i++ )
                                {
                                    for($j=1;$j<=7;$j++)
                                    {
                                        $content.=$this->display_day($i*7+$j,$all);
                                    }
                                }

                                $content.='</ul>';
                                $content.='<div class="clear"></div>';
                        $content.=' </div> ';
        $content.='</div>';
        $content.='';
        return $content;
    }

    private function display_day($cellNumber,&$all)
    {
        if ($this->curDay == 0) {

            $firstDayOfTheWeek = date('N', strtotime($this->curYear . '-' . $this->curMonth . '-01'));

            if (intval($cellNumber) == intval($firstDayOfTheWeek))
            {
                $this->curDay = 1;
            }
        }

        if (($this->curDay != 0) && ($this->curDay <= $this->daysInMonth))
        {
            $this->curDate = date('Y-m-d', strtotime($this->curYear . '-' . $this->curMonth . '-' . ($this->curDay)));

            $cellContent = $this->curDay;
            $this->prevDay = $this->curDate;
            $this->curDay++;

        }
        else
        {
            $this->curDate = null;
            $this->nullDays++;
            $cellContent = null;
        }
            $onClickAction = ' onclick="getCurVal(this.id)" ';
            $standard = 0;
            $senior = 0;
            $keykeeper = array();
            $person = "";
            $resDay = "";

            $arr = $this->checkCur_date($all);
            if (!count($arr) == 0)
            {
                if (count($arr) > 0)
                {
                    foreach ($arr as $key => $val)
                    {
                        $keykeeper[] = $key;
                    }
                }

                for ($i = 0; $i < count($arr); $i++)
                {
                    $checklevel = $arr[$keykeeper[$i]]['worker_level']; $checkUser = $arr[$keykeeper[$i]]['id_worker']; $checkBooked = $arr[$keykeeper[$i]]['book_day'];

                    if ($checkUser == $_SESSION['idCur'])
                    {
                        $person = $_SESSION['userCur'];
                        if ($checkBooked == $this->curDate)
                        {
                            $resDay = " booked ";
                        }
                    }

                    if ($checklevel == "Standard")
                    {
                        $standard++;
                    }
                    elseif ($checklevel == "Senior")
                    {
                        $senior++;
                    }
                }

            }
            return '<li id="'.$this->curDate.'"'.$onClickAction.'
			class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
					($cellContent==null?'mask':'').
					(date("Y-m-d")==$this->curDate?'today':'').
					($this->curDate<Date("Y-m-d")?'before':'').
					($this->curDate>date("Y-m-d", strtotime("+". weekP_global ." week"))?'after':'')." ".$resDay.'"><div class="date">'.$cellContent.'</div><div id="nurse">Standard:'.$standard.'<br>'.'<br>Senior:'.$senior.'<br>'.'</div><div class="person">'.$person.'</div></li>';
    }

    private function navCreate()
    {
        $nextMonth = $this->curMonth==12?1:intval($this->curMonth)+1;
        $nextYear = $this->curMonth==12?intval($this->curYear)+1:$this->curYear;
        $preMonth = $this->curMonth==1?12:intval($this->curMonth)-1;
        $preYear = $this->curMonth==1?intval($this->curYear)-1:$this->curYear;
         
        return
            '<div class="header">'.
                '<a class="prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">Prev</a>'.
                    '<span class="title">'.date('Y M',strtotime($this->curYear.'-'.$this->curMonth.'-1')).'</span>'.
                '<a class="next" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'">Next</a>'.
            '</div>';
    }

    function checkCur_date($all)
    {
        $arr = array_filter($all, function($arr)
        {
            return ($arr['book_day'] == $this->curDate);
        });

        return $arr;
    }

    private function c_labels()
    {
        $content='';

        foreach($this->dayLabels as $index=>$label)
        {
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
        }
         
        return $content;
    }

    private function numberWeeks_Month($month=null,$year=null)
    {
        if( null==($year) )
        {
            $year =  date("Y",time()); 
        }
         
        if(null==($month))
        {
            $month = date("m",time());
        }

        $daysInMonths = $this->numberDays_Month($month,$year);
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay)
        {
            $numOfweeks++;
        }
         
        return $numOfweeks;
    }

    private function numberDays_Month($month=null,$year=null)
    {
        if(null==($year))
            $year =  date("Y",time());
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }
}