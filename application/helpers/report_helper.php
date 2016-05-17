<?php
//Some reports need time information others do not. So this allows us to reuse this function. The $time parameter should be passed from the corresponding
//date_input_excel_whatever_specific_blabla that calls the private function: _get_common_report_data, that in turn, calls this helper function.
function get_simple_date_ranges($time=false)
{
		$CI =& get_instance();

		if(!$time)
		{
			$today =  date('Y-m-d');
			$yesterday = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$start_of_this_month = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y")));
			$end_of_this_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
			$start_of_last_month = date('Y-m-d', mktime(0,0,0,date("m")-1,1,date("Y")));
			$end_of_last_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime((date('m') - 1).'/01/'.date('Y').' 00:00:00'))));
			$start_of_this_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")));
			$end_of_this_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")));
			$start_of_last_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")-1));
			$end_of_last_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")-1));
			$start_of_time =  date('Y-m-d', 0);

			$previous_week = strtotime("-1 week +1 day");
			$current_week = strtotime("-0 week +1 day");

			$previous_start_week = strtotime("last monday midnight",$previous_week);
			$previous_end_week = strtotime("next sunday",$previous_start_week);

			$previous_start_week = date("Y-m-d",$previous_start_week);
			$previous_end_week = date("Y-m-d",$previous_end_week);

			$current_start_week = strtotime("last monday midnight",$current_week);
			$current_end_week = strtotime("next sunday",$current_start_week);

			$current_start_week = date("Y-m-d",$current_start_week);
			$current_end_week = date("Y-m-d",$current_end_week);

			return array(
				$today. '/' . $today 								=> lang('reports_today'),
				$yesterday. '/' . $yesterday						=> lang('reports_yesterday'),
				$six_days_ago. '/' . $today 						=> lang('reports_last_7'),
				$current_start_week. '/' . $current_end_week		=> lang('reports_this_week'),
				$previous_start_week. '/' . $previous_end_week		=> lang('reports_last_week'),
				$start_of_this_month . '/' . $end_of_this_month		=> lang('reports_this_month'),
				$start_of_last_month . '/' . $end_of_last_month		=> lang('reports_last_month'),
				$start_of_this_year . '/' . $end_of_this_year	 	=> lang('reports_this_year'),
				$start_of_last_year . '/' . $end_of_last_year		=> lang('reports_last_year'),
				$start_of_time . '/' . 	$today						=> lang('reports_all_time'),
			);
		}
		else
		{
			$today =  date('Y-m-d').' 00:00:00';
			$end_of_today=date('Y-m-d').' 23:59:59';
			$yesterday = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y"))).' 00:00:00';
			$end_of_yesterday=date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y"))).' 23:59:59';
			$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-6,date("Y"))).' 00:00:00';
			$start_of_this_month = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y"))).' 00:00:00';
			$end_of_this_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00')))).' 23:59:59';
			$start_of_last_month = date('Y-m-d', mktime(0,0,0,date("m")-1,1,date("Y"))).' 00:00:00';
			$end_of_last_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime((date('m') - 1).'/01/'.date('Y').' 00:00:00')))).' 23:59:59';
			$start_of_this_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y"))).' 00:00:00';
			$end_of_this_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y"))).' 23:59:59';
			$start_of_last_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")-1)).' 00:00:00';
			$end_of_last_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")-1)).' 23:59:59';
			$start_of_time =  date('Y-m-d', 0);

			$previous_week = strtotime("-1 week +1 day");
			$current_week = strtotime("-0 week +1 day");

			$previous_start_week = strtotime("last monday midnight",$previous_week);
			$previous_end_week = strtotime("next sunday",$previous_start_week);

			$previous_start_week = date("Y-m-d",$previous_start_week).' 00:00:00';
			$previous_end_week = date("Y-m-d",$previous_end_week).' 23:59:59';

			$current_start_week = strtotime("last monday midnight",$current_week);
			$current_end_week = strtotime("next sunday",$current_start_week);

			$current_start_week = date("Y-m-d",$current_start_week).' 00:00:00';
			$current_end_week = date("Y-m-d",$current_end_week).' 23:59:59';

			return array(
				$today. '/' . $end_of_today 						=> lang('reports_today'),
				$yesterday. '/' . $end_of_yesterday					=> lang('reports_yesterday'),
				$six_days_ago. '/' . $end_of_today  				=> lang('reports_last_7'),
				$current_start_week. '/' . $current_end_week		=> lang('reports_this_week'),
				$previous_start_week. '/' . $previous_end_week		=> lang('reports_last_week'),
				$start_of_this_month . '/' . $end_of_this_month		=> lang('reports_this_month'),
				$start_of_last_month . '/' . $end_of_last_month		=> lang('reports_last_month'),
				$start_of_this_year . '/' . $end_of_this_year	 	=> lang('reports_this_year'),
				$start_of_last_year . '/' . $end_of_last_year		=> lang('reports_last_year'),
				$start_of_time . '/' . 	$end_of_today						=> lang('reports_all_time'),
			);
		}
}

function get_simple_date_ranges_expire()
{
	$today =  date('Y-m-d');
	$yesterday = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
	$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-6,date("Y")));
	$start_of_this_month = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y")));
	$end_of_this_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
	$start_of_last_month = date('Y-m-d', mktime(0,0,0,date("m")-1,1,date("Y")));
	$end_of_last_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime((date('m') - 1).'/01/'.date('Y').' 00:00:00'))));
	$start_of_this_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")));
	$end_of_this_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")));
	$start_of_last_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")-1));
	$end_of_last_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")-1));
	$start_of_time =  date('Y-m-d', 0);

	$previous_week = strtotime("-1 week +1 day");
	$current_week = strtotime("-0 week +1 day");

	$previous_start_week = strtotime("last monday midnight",$previous_week);
	$previous_end_week = strtotime("next sunday",$previous_start_week);

	$previous_start_week = date("Y-m-d",$previous_start_week);
	$previous_end_week = date("Y-m-d",$previous_end_week);

	$current_start_week = strtotime("last monday midnight",$current_week);
	$current_end_week = strtotime("next sunday",$current_start_week);

	$current_start_week = date("Y-m-d",$current_start_week);
	$current_end_week = date("Y-m-d",$current_end_week);

	return array(
		$today. '/' . $today 								=> lang('reports_today'),
		$current_start_week. '/' . $current_end_week		=> lang('reports_this_week'),
		$start_of_this_month . '/' . $end_of_this_month		=> lang('reports_this_month'),
	);
}

function get_months()
{
	$months = array();
	for($k=1;$k<=12;$k++)
	{
		$cur_month = mktime(0, 0, 0, $k, 1, 2000);
		$months[date("m", $cur_month)] = get_month_translation(date("m", $cur_month));
	}

	return $months;
}

function get_month_translation($month_numeric)
{
	return lang('reports_month_'.$month_numeric);
}

function get_days()
{
	$days = array();

	for($k=1;$k<=31;$k++)
	{
		$cur_day = mktime(0, 0, 0, 1, $k, 2000);
		$days[date('d',$cur_day)] = date('j',$cur_day);
	}

	return $days;
}

function get_years()
{
	$years = array();
	for($k=0;$k<10;$k++)
	{
		$years[date("Y")-$k] = date("Y")-$k;
	}

	return $years;
}

function get_hours($time_format)
    {
       $hours = array();
	   if($time_format == '24_hour')
	   {
       for($k=0;$k<24;$k++)
		{
          $hours[$k] = $k;
		}
	   }
	   else 
	   {
		for($k=0;$k<24;$k++)
		{
		
          $hours[$k]  = date('h a', mktime($k));
		
		}
		
		
	   }
       return $hours;
    }


    function get_minutes()
    {
       $hours = array();
       for($k=0;$k<60;$k++)
       {
          $minutes[$k] = $k;
       }
       return $minutes;
    }


function get_random_colors($how_many)
{
	$colors = array();

	for($k=0;$k<$how_many;$k++)
	{
		$colors[] = '#'.random_color();
	}

	return $colors;
}

function random_color()
{
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function get_template_colors()
{
	//https://flatuicolors.com
	return array('#1abc9c','#16a085','#f1c40f','#f39c12','#2ecc71','#27ae60','#e67e22','#d35400','#3799dc','#2980b9','#e74c3c','#c0392b','#9b59b6','#8e44ad','#ecf0f1','#bdc3c7','#34495e','#2c3e50','#95a5a6','#7f8c8d');
}

function get_time_intervals()
{
	return array(
		1800 => '30 '.lang('reports_min'),
		3600 => '60 '.lang('reports_min'),
		5400 => '90 '.lang('reports_min'),
		7200 => '120 '.lang('reports_min'),
		9000 => '150 '.lang('reports_min'),
		10800 => '180 '.lang('reports_min'),
	);
}

function can_display_graphical_report()
{
	$CI =& get_instance();
	return !$CI->agent->is_android_less_than_4_4();
}