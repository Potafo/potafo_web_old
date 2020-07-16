<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\StaffAttendance;
use App\TimeBonus;
use App\TimeSlot;
use Illuminate\Http\Request;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class ApiController extends Controller
{
    /**
     * Respnd with attendance report of delivery boys on date wise.
     * @method POST
     * @var Request $request
     * @post_data staff_id, from_date, to_date. 
     * @return \Illuminate\Http\Response
     */

    public function attendance_report(Request $request)
    {
        $time_slots = TimeSlot::all();

        $is_from_dash = 0;
        $is_from_dash = $request->post('from_dash');
        // dd($is_from_dash);

        $response = [];
        $reports_response = [];
        $staff_id = $request->post('staff_id');
        $from_date_tmp = $request->post('from_date');
        $to_date_tmp = $request->post('to_date');

        $from_date = $from_date_tmp . ' 00:00:00';
        $to_date = $to_date_tmp . ' 23:00:00';

        $duration = 0;
        $bonus_amount = 0;
        $shortage_amount = 0;
        $total_earnings = 0;
        $data = [];
        $date = '';
        $orders_count = 0;
        $attendance_log = [];
        $totoal_dur = 0;

        $dates = $this->getDatesFromRange($from_date, $to_date);
        foreach ($dates as $key => $date) {
            $date_stamp = strtotime($date);
            $to_date_stamp = strtotime($to_date_tmp);

            if ($date_stamp > $to_date_stamp) {
                continue;
            }

            $bonus_amount = 0;
            $shortage_amount = 0;
            $earnings_log = [];
            $extra_bonus_log = [];
            $shortage_log = [];
            $star_amount = 0;
            $total_earnings = 0;
            $worked_hours = StaffAttendance::where('staff_id', $staff_id)
                ->where('created_at', 'like',  $date . '%')
                ->get();

            // $orders = DB::SELECT("select review_star from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");
            $orders = DB::SELECT("select json_unquote(json_extract(delivery_assigned_details, '$.star_rate')) as review from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");

            foreach ($orders as $key => $order) {
                if ($order->review != '') {
                    // $stars = json_decode($order->dlv_review_details)->dlv_cust_rating;
                    if ($order->review >= 5) {
                        $star_amount += 5;
                        // $date_wise_stars += 5;
                    }
                }
            }

            if (count($worked_hours) <= 0) {
                continue;
            }

            $total_duration = 0;
            foreach ($time_slots as $key => $slot) {
                $from = strtotime($slot->from_time);
                $to = strtotime($slot->to_time);

                $duration = 0;
                $earnings = 0;
                foreach ($worked_hours as $key => $time) {
                    $order_amount = 0;
                    $bonus = 0;
                    $is_bonus_hour = 0;
                    $work_duration = 0;
                    $date = $time->created_at->format('Y-m-d');
                    $start_time = strtotime($time->checkin_time);
                    $to_time = strtotime($time->checkout_time);

                    if ($to_time < $from) {
                        continue;
                    }

                    if ($start_time > $to) {
                        continue;
                    }

                    if ($start_time < $from) {
                        $started_at = $from;
                    } else {
                        $started_at = $start_time;
                    }

                    if ($to > $to_time) {
                        $end_time = $to_time;
                    } else {
                        $end_time = $to;
                    }

                    $in_time = $started_at * 60;
                    $out_time = $end_time * 60;
                    $duration += ($out_time - $in_time) / 60 / 60 / 60;
                    $duration = number_format((float) $duration, 1, '.', '');

                    $total_duration += number_format($duration, 1, '.', '');

                    $from_date = $date . ' ' . date('H:i:s', $started_at);
                    $to_date = $date . ' ' . date('H:i:s', $end_time);


                    $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date'");
                    if (count($orders) > 0) {
                        $orders_count += $orders[0]->total_orders;
                    }

                    $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();


                    if ($earning_amount) {
                        if ($earning_amount->bonus_amount != 0) {
                            $is_bonus_hour = 1;
                            $work_duration = $duration;
                        }
                        $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                        $bonus = floor($duration) * $earning_amount->bonus_amount;

                        $total_earnings = $total_earnings + $order_amount + $bonus;
                    }

                    $earnings_log[] = [
                        'date' => $date,
                        'time_slot' => date('H:i', $from) . ' - '  . date('H:i', $to),
                        'orders_count' => $orders[0]->total_orders,
                        'order_amount' => $order_amount,
                        'duration' => number_format((float) $duration, 1, '.', ''),
                        'work_duration' => floor($work_duration),
                        'is_bonus_hour' => $is_bonus_hour,
                        'bonus_amount' => $bonus,
                    ];
                }
            }

            $adjusts = DB::SELECT("SELECT * FROM internal_staffs_sal_adj WHERE is_staff_id = $staff_id AND is_staff_date = '$date'");

            foreach ($adjusts as $key => $adjust) {
                if ($adjust->is_mode == 'Bonus') {
                    $bonus_amount += $adjust->is_staff_amount;

                    $extra_bonus_log[] = [
                        'amount' => $adjust->is_staff_amount,
                        'reason' => $adjust->is_reason,
                    ];
                } elseif ($adjust->is_mode == 'Shortage') {
                    $shortage_amount += $adjust->is_staff_amount;

                    $shortage_log[] = [
                        'amount' => $adjust->is_staff_amount,
                        'reason' => $adjust->is_reason,
                    ];
                }
            }

            $attendance_log = [];
            $final_duration = 0;
            foreach ($worked_hours as $key => $time) {
                if ($time->checkout_time == Null || $time->checkout_time == 'null' || $time->checkout_time == null) {
                    continue;
                }
                $_duration = 0;
                $in = strtotime($time->checkin_time);
                $out = strtotime($time->checkout_time);

                $_duration += ($out - $in) / 60 / 60;
                $_duration = number_format((float) $_duration, 1, '.', '');

                // $a = $_duration;
                // $nums = explode('.', $a);

                // $duration = $nums[0];

                // if(isset($nums[1]) && $nums[1] > 5) {
                //     $duration = $nums[0] . '.5';
                // } 

                $attendance_log[] = [
                    'total_time' => $time->checkin_time . ' - ' . $time->checkout_time,
                    'duration' => $_duration
                ];
                $final_duration += $_duration;
            }


            $prev_time = '';
            $prev_date = '';
            $bonus_amount_final = 0;
            $bonus_hour_final = 0;
            $earnings_logs = [];
            foreach ($earnings_log as $key => $log) {
                if ($prev_time == $log['time_slot'] && $prev_date == $log['date']) {
                    $temp_log = $earnings_log[$key];

                    array_pop($earnings_logs);

                    $earnings_logs[] = [
                        'date' => $log['date'],
                        'time_slot' => $log['time_slot'],
                        'orders_count' => $log['orders_count'] + $temp_log['orders_count'],
                        'order_amount' => $log['order_amount'] + $temp_log['order_amount'],
                    ];

                    if ($log['is_bonus_hour']) {
                        $bonus_hour_final += $log['work_duration'];
                        $bonus_amount_final += $log['bonus_amount'];
                    }
                } else {
                    $earnings_logs[] = [
                        'date' => $log['date'],
                        'time_slot' => $log['time_slot'],
                        'orders_count' => $log['orders_count'],
                        'order_amount' => $log['order_amount'],
                    ];

                    if ($log['is_bonus_hour']) {
                        $bonus_hour_final += $log['work_duration'];
                        $bonus_amount_final += $log['bonus_amount'];
                    }
                }

                $prev_time = $log['time_slot'];
                $prev_date = $log['date'];
            }

            if ($bonus_hour_final < 4) {
                // $bonus_hour_final = 0;
                $bonus_amount_final = 0;
            }

            $final_amouont = $total_earnings + $bonus_amount - $shortage_amount + $star_amount;

            $data[] = [
                'date' => $date,
                'total_duration' => $final_duration,
                'total_earnings' => $total_earnings,
                'extra_bonus' => $bonus_amount,
                'shortage' => $shortage_amount,
                'final_amount' => $final_amouont,
                'total_earnings' => $total_earnings,
                'star_amount' => $star_amount,
                'earnings_log' => $earnings_logs,
                'bonus_log' => $extra_bonus_log,
                'shortage_log' => $shortage_log,
                'attendance' => $attendance_log,
                'bonus_hour' => $bonus_hour_final,
                'hour_bonus_amount' => $bonus_amount_final
            ];

            $totoal_dur = $final_amouont;
        }

        if ($is_from_dash == 1) {
            $response = [
                'status' => 'success',
                'response_code' => 200,
                'total_earnings' => $total_earnings,
                'duraiion' => number_format((float) $totoal_dur, 1, '.', ''),
            ];
        } else {
            $response = [
                'status' => 'success',
                'response_code' => 200,
                'data' => $data
            ];
        }
        return response($response);
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {

        // Declare an empty array 
        $array = array();

        // Variable that store the date interval 
        // of period 1 day 
        $interval = new DateInterval('P1D');
        // dd($interval);

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array 
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements 
        return $array;
    }

    public function salary_report($from_date, $to_date, $staff_id = Null)
    {
        if (!strtotime($from_date) || !strtotime($to_date)) {
            return 'Please check the dates you\'ve entered';
        }
        $time_slots = TimeSlot::all();

        $response = [];
        $from_date_tmp = $from_date;
        $to_date_tmp = $to_date;

        $from_date = $from_date_tmp . ' 00:00:00';
        $to_date = $to_date_tmp . ' 23:00:00';

        $duration = 0;
        $bonus_amount = 0;
        $shortage_amount = 0;
        $total_earnings = 0;
        $date = '';
        $orders_count = 0;
        $attendance_log = [];

        if ($staff_id == Null) {
            $staffs = DB::SELECT("SELECT * FROM internal_staffs WHERE designation='Delivery staff' and active='Y'");
        } else {
            $staffs = DB::SELECT("SELECT * FROM internal_staffs WHERE designation='Delivery staff' and active='Y' AND id=$staff_id");
        }

        $staff_data = [];

        $dates = $this->getDatesFromRange($from_date, $to_date);
        // array_pop($dates);
        foreach ($staffs as $key => $staff) {
            $data = [];
            $staff_id = $staff->id;

            $loop_index = 0;

            $date_wise_final = 0;
            $date_wise_duration = 0;
            $date_wise_bonus = 0;
            $date_wise_stars = 0;
            $date_wise_normal_orders = 0;
            $date_wise_normal_orders_amount = 0;
            $date_wise_special_orders = 0;
            $date_wise_special_amount = 0;
            // $last_date = end($dates);
            // $first_date = $dates[0];

            // dd($dates);

            $temp_worked_hours = StaffAttendance::where('staff_id', $staff_id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->get();


            if (count($temp_worked_hours) > 0) {
                $first_date = $temp_worked_hours[0]->created_at->format('Y-m-d');

                $end_date = end($temp_worked_hours);
                $end_date = end($end_date);

                $end_date->created_at->format('Y-m-d');

                // $last_date = end($temp_worked_hours);
                // $last_date = $last_date[0]->created_at->format('Y-m-d');
                $last_date = $end_date->created_at->format('Y-m-d');
            }
            // echo $first_date . '<br>';
            // echo $last_date . '<br>';
            // echo $staff_id . '<br>';
            // dd('end');
            // $star_amount = 0;
            $total_orders = 0;
            // $normal_order_count = 0;
            // $normal_order_earnings = 0;
            // $special_order_count = 0;
            // $special_order_earnings = 0;
            foreach ($dates as $key => $date) {
                $final_amouont = 0;
                $normal_order_count = 0;
                $normal_order_earnings = 0;
                $special_order_count = 0;
                $special_order_earnings = 0;
                $star_amount = 0;

                $date_stamp = strtotime($date);
                $to_date_stamp = strtotime($to_date_tmp);

                if ($date_stamp > $to_date_stamp) {
                    continue;
                }

                $bonus_amount = 0;
                $shortage_amount = 0;
                $earnings_log = [];
                $extra_bonus_log = [];
                $shortage_log = [];
                $total_earnings = 0;
                $worked_hours = StaffAttendance::where('staff_id', $staff_id)
                    ->where('created_at', 'like',  $date . '%')
                    ->get();

                $orders = DB::SELECT("select json_unquote(json_extract(delivery_assigned_details, '$.star_rate')) as review from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");

                foreach ($orders as $key => $order) {
                    if ($order->review != '') {
                        // $stars = json_decode($order->dlv_review_details)->dlv_cust_rating;
                        if ($order->review >= 5) {
                            $star_amount += 5;
                            $date_wise_stars += 5;
                        }
                    }
                }

                if (count($worked_hours) <= 0) {
                    continue;
                }

                // $last_date = 

                // dd($first_date);



                $total_duration = 0;

                // $pre_from_date = '';
                foreach ($time_slots as $key => $slot) {
                    $from = strtotime($slot->from_time);
                    $to = strtotime($slot->to_time);

                    $duration = 0;
                    $earnings = 0;
                    $login_time = $worked_hours[0]->checkin_time;
                    $logout_time = '';
                    // dd($worked_hours);
                    foreach ($worked_hours as $key => $time) {
                        $order_amount = 0;
                        $bonus = 0;
                        $is_bonus_hour = 0;
                        $work_duration = 0;

                        $date = $time->created_at->format('Y-m-d');
                        $logout_time = $time->checkout_time;
                        $start_time = strtotime($time->checkin_time);
                        $to_time = strtotime($time->checkout_time);

                        if ($to_time < $from) {
                            continue;
                        }

                        if ($start_time > $to) {
                            continue;
                        }

                        if ($start_time < $from) {
                            $started_at = $from;
                        } else {
                            $started_at = $start_time;
                        }

                        if ($to > $to_time) {
                            $end_time = $to_time;
                        } else {
                            $end_time = $to;
                        }

                        $in_time = $started_at * 60;
                        $out_time = $end_time * 60;
                        $duration += ($out_time - $in_time) / 60 / 60 / 60;
                        $duration = number_format((float) $duration, 1, '.', '');

                        $total_duration += number_format($duration, 1, '.', '');

                        $from_date = $date . ' ' . date('H:i:s', $started_at);
                        $to_date = $date . ' ' . date('H:i:s', $end_time);


                        $day = $time->created_at->format('l');

                        // $time_splits .= date('H:i:s', $start_time) . ' - ' . date('H:i:s', $to_time) . ', <br />';


                        $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date' AND current_status='D'");

                        if (count($orders) > 0) {
                            $orders_count += $orders[0]->total_orders;
                        }

                        $total_orders += $orders[0]->total_orders;

                        // $is_holiday = Holiday::where('created_at', $time->created_at)->get();

                        $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();

                        if ($earning_amount) {
                            $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                            if ($earning_amount->bonus_amount != 0) {
                                // $special_order_earnings += $order_amount;
                                // $special_order_count += $orders[0]->total_orders;

                                if ($day == 'Saturday' || $day == 'Sunday') {
                                    $bonus = floor($duration) * $earning_amount->special_bonus_amount;
                                } else {
                                    $bonus = floor($duration) * $earning_amount->bonus_amount;
                                }
                                $bonus = floor($duration) * $earning_amount->bonus_amount;

                                // $normal_order_count += $orders[0]->total_orders;
                                // $date_wise_normal_orders += $normal_order_count;
                                // $normal_order_earnings += $order_amount;
                                // $date_wise_normal_orders_amount += $normal_order_earnings;

                                $is_bonus_hour = 1;
                                $work_duration = $duration;
                            } else {
                                // $special_order_earnings += $order_amount;
                                // $special_order_count += $orders[0]->total_orders;

                                // $date_wise_special_orders += $special_order_count;
                                // $date_wise_special_amount += $special_order_earnings;

                                // $normal_order_count += $orders[0]->total_orders;
                                // $normal_order_earnings += $order_amount;
                            }

                            // $total_earnings = $total_earnings + $order_amount;
                        }

                        $earnings_log[] = [
                            'date' => $date,
                            'time_slot' => date('H:i', $from) . ' - '  . date('H:i', $to),
                            'orders_count' => $orders[0]->total_orders,
                            'order_amount' => $order_amount,
                            'duration' => number_format((float) $duration, 1, '.', ''),
                            'work_duration' => floor($work_duration),
                            'is_bonus_hour' => $is_bonus_hour,
                            'bonus_amount' => $bonus,
                            // 'normal_order_count' => $normal_order_count,
                            // 'normal_order_earnings' => $normal_order_earnings,
                            // 'special_order_count' => $special_order_count,
                            // 'special_order_earnings' => $special_order_earnings,
                        ];
                    }

                    $from_date = $date . ' ' . $slot->from_time;
                    $to_date = $date . ' ' . $slot->to_time;

                    $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date' AND current_status='D'");

                    if (count($orders) > 0) {
                        $orders_count += $orders[0]->total_orders;
                    }

                    $day = strtotime($from_date);
                    $day = date('l', $day);

                    // dd($day);

                    // $_total_orders += $orders_count;

                    // $is_holiday = Holiday::where('created_at', $time->created_at)->get();

                    $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();

                    if ($earning_amount) {
                        $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                        if ($earning_amount->bonus_amount != 0) {
                            // $special_order_earnings += $order_amount;
                            // $special_order_count += $orders[0]->total_orders;

                            if ($day == 'Saturday' || $day == 'Sunday') {
                                $bonus = floor($duration) * $earning_amount->special_bonus_amount;
                            } else {
                                $bonus = floor($duration) * $earning_amount->bonus_amount;
                            }
                            $bonus = floor($duration) * $earning_amount->bonus_amount;

                            $normal_order_count += $orders[0]->total_orders;
                            $date_wise_normal_orders += $normal_order_count;
                            $normal_order_earnings += $order_amount;
                            $date_wise_normal_orders_amount += $normal_order_earnings;

                            $is_bonus_hour = 1;
                            $work_duration = $duration;
                        } else {
                            $special_order_earnings += $order_amount;
                            $special_order_count += $orders[0]->total_orders;

                            $date_wise_special_orders += $special_order_count;
                            $date_wise_special_amount += $special_order_earnings;

                            // $normal_order_count += $orders[0]->total_orders;
                            // $normal_order_earnings += $order_amount;
                        }

                        $total_earnings = $total_earnings + $order_amount;
                    }
                }

                $adjusts = DB::SELECT("SELECT * FROM internal_staffs_sal_adj WHERE is_staff_id = $staff_id AND is_staff_date = '$date'");

                foreach ($adjusts as $key => $adjust) {
                    if ($adjust->is_mode == 'Bonus') {
                        $bonus_amount += $adjust->is_staff_amount;

                        $extra_bonus_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    } elseif ($adjust->is_mode == 'Shortage') {
                        $shortage_amount += $adjust->is_staff_amount;

                        $shortage_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    }
                }

                $attendance_log = [];
                $final_duration = 0;
                $time_splits = '';

                foreach ($worked_hours as $key => $time) {
                    if ($time->checkout_time == Null || $time->checkout_time == 'null' || $time->checkout_time == null) {
                        continue;
                    }
                    $_duration = 0;
                    $in = strtotime($time->checkin_time);
                    $out = strtotime($time->checkout_time);

                    $_duration += ($out - $in) / 60 / 60;
                    $_duration = number_format((float) $_duration, 1, '.', '');


                    $time_splits .= date('H:i:s', $in) . ' - ' . date('H:i:s', $out) . ', <br />';

                    $attendance_log[] = [
                        'total_time' => $time->checkin_time . ' - ' . $time->checkout_time,
                        'duration' => $_duration
                    ];
                    $final_duration += $_duration;
                }

                $prev_time = '';
                $prev_date = '';
                $bonus_amount_final = 0;
                $bonus_hour_final = 0;
                $earnings_logs = [];

                foreach ($earnings_log as $key => $log) {
                    if ($prev_time == $log['time_slot'] && $prev_date == $log['date']) {
                        $temp_log = $earnings_log[$key];

                        array_pop($earnings_logs);

                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'] + $temp_log['orders_count'],
                            'order_amount' => $log['order_amount'] + $temp_log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    } else {
                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'],
                            'order_amount' => $log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    }

                    $prev_time = $log['time_slot'];
                    $prev_date = $log['date'];
                }

                if ($final_duration < 4) {
                    // $bonus_hour_final = 0;
                    $bonus_amount_final = 0;
                }

                $final_amouont = $total_earnings + $bonus_amount_final - $shortage_amount + $star_amount + $bonus_amount;
                $date_wise_final += $final_amouont;
                $date_wise_duration += $final_duration;
                $date_wise_bonus += $bonus_amount_final;


                $data[] = [
                    // 'name' => $staff->first_name . " ($staff_id)",
                    'date' => $date,
                    'login_time' => $login_time,
                    'logout_time' => $logout_time,
                    'time_splits' => $time_splits,
                    'total_duration' => $final_duration,
                    'total_earnings' => $total_earnings,
                    'extra_bonus' => $bonus_amount,
                    'shortage' => $shortage_amount,
                    'final_amount' => $final_amouont,
                    'total_earnings' => $total_earnings,
                    'star_amount' => $star_amount,
                    // 'earnings_log' => $earnings_logs,
                    // 'bonus_log' => $extra_bonus_log,
                    // 'shortage_log' => $shortage_log,
                    // 'attendance' => $attendance_log,
                    'bonus_hour' => $bonus_hour_final,
                    'hour_bonus_amount' => $bonus_amount_final,
                    'normal_order_count' => $normal_order_count,
                    'normal_order_earnings' => $normal_order_earnings,
                    'special_order_count' => $special_order_count,
                    'special_order_earnings' => $special_order_earnings,
                ];


                ++$loop_index;
            }


            if ($date_wise_duration <= 25) {
                $date_wise_final = $date_wise_final - $date_wise_bonus;
            }


            if ($data != []) {
                $staff_data[] = [
                    'name' => $staff->first_name . " ($staff->id)",
                    'data' => $data,
                    'total' => $date_wise_final,
                    'weekly_duration' => $date_wise_duration,
                    'date_wise_star' => $date_wise_stars,
                    'date_wise_normal_orders' => $date_wise_normal_orders,
                    'date_wise_normal_orders_amount' => $date_wise_normal_orders_amount,
                    'date_wise_special_orders' => $date_wise_special_orders,
                    'date_wise_special_amount' => $date_wise_special_amount,
                ];
            }
        }
        return view('snippets/salary_report_tile')->with(['staff_data' => $staff_data]);
    }

    public function salary_report_account($from_date, $to_date, $staff_id = Null)
    {
        if (!strtotime($from_date) || !strtotime($to_date)) {
            return 'Please check the dates you\'ve entered';
        }
        $time_slots = TimeSlot::all();

        $response = [];
        $from_date_tmp = $from_date;
        $to_date_tmp = $to_date;

        $from_date = $from_date_tmp . ' 00:00:00';
        $to_date = $to_date_tmp . ' 23:00:00';

        $duration = 0;
        $bonus_amount = 0;
        $shortage_amount = 0;
        $total_earnings = 0;
        $date = '';
        $orders_count = 0;
        $attendance_log = [];

        if ($staff_id == Null) {
            $staffs = DB::SELECT("SELECT * FROM internal_staffs WHERE designation='Delivery staff' and active='Y'");
        } else {
            $staffs = DB::SELECT("SELECT * FROM internal_staffs WHERE designation='Delivery staff' and active='Y' AND id=$staff_id");
        }

        $staff_data = [];

        $dates = $this->getDatesFromRange($from_date, $to_date);
        // array_pop($dates);
        foreach ($staffs as $key => $staff) {
            $data = [];
            $staff_id = $staff->id;

            $loop_index = 0;

            $date_wise_final = 0;
            $date_wise_duration = 0;
            $date_wise_bonus = 0;
            $date_wise_stars = 0;
            $date_wise_normal_orders = 0;
            $date_wise_normal_orders_amount = 0;
            $date_wise_special_orders = 0;
            $date_wise_special_amount = 0;
            // $last_date = end($dates);
            // $first_date = $dates[0];

            // dd($dates);

            $temp_worked_hours = StaffAttendance::where('staff_id', $staff_id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->get();


            if (count($temp_worked_hours) > 0) {
                $first_date = $temp_worked_hours[0]->created_at->format('Y-m-d');

                $end_date = end($temp_worked_hours);
                $end_date = end($end_date);

                $end_date->created_at->format('Y-m-d');

                // $last_date = end($temp_worked_hours);
                // $last_date = $last_date[0]->created_at->format('Y-m-d');
                $last_date = $end_date->created_at->format('Y-m-d');
            }
            // echo $first_date . '<br>';
            // echo $last_date . '<br>';
            // echo $staff_id . '<br>';
            // dd('end');
            // $star_amount = 0;
            $total_orders = 0;
            // $normal_order_count = 0;
            // $normal_order_earnings = 0;
            // $special_order_count = 0;
            // $special_order_earnings = 0;
            foreach ($dates as $key => $date) {
                $final_amouont = 0;
                $normal_order_count = 0;
                $normal_order_earnings = 0;
                $special_order_count = 0;
                $special_order_earnings = 0;
                $star_amount = 0;

                $date_stamp = strtotime($date);
                $to_date_stamp = strtotime($to_date_tmp);

                if ($date_stamp > $to_date_stamp) {
                    continue;
                }

                $bonus_amount = 0;
                $shortage_amount = 0;
                $earnings_log = [];
                $extra_bonus_log = [];
                $shortage_log = [];
                $total_earnings = 0;
                $worked_hours = StaffAttendance::where('staff_id', $staff_id)
                    ->where('created_at', 'like',  $date . '%')
                    ->get();

                $orders = DB::SELECT("select json_unquote(json_extract(delivery_assigned_details, '$.star_rate')) as review from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");

                foreach ($orders as $key => $order) {
                    if ($order->review != '') {
                        // $stars = json_decode($order->dlv_review_details)->dlv_cust_rating;
                        if ($order->review >= 5) {
                            $star_amount += 5;
                            $date_wise_stars += 5;
                        }
                    }
                }

                if (count($worked_hours) <= 0) {
                    continue;
                }

                // $last_date = 

                // dd($first_date);



                $total_duration = 0;

                // $pre_from_date = '';
                foreach ($time_slots as $key => $slot) {
                    $from = strtotime($slot->from_time);
                    $to = strtotime($slot->to_time);

                    $duration = 0;
                    $earnings = 0;
                    $login_time = $worked_hours[0]->checkin_time;
                    $logout_time = '';
                    // dd($worked_hours);
                    foreach ($worked_hours as $key => $time) {
                        $order_amount = 0;
                        $bonus = 0;
                        $is_bonus_hour = 0;
                        $work_duration = 0;

                        $date = $time->created_at->format('Y-m-d');
                        $logout_time = $time->checkout_time;
                        $start_time = strtotime($time->checkin_time);
                        $to_time = strtotime($time->checkout_time);

                        if ($to_time < $from) {
                            continue;
                        }

                        if ($start_time > $to) {
                            continue;
                        }

                        if ($start_time < $from) {
                            $started_at = $from;
                        } else {
                            $started_at = $start_time;
                        }

                        if ($to > $to_time) {
                            $end_time = $to_time;
                        } else {
                            $end_time = $to;
                        }

                        $in_time = $started_at * 60;
                        $out_time = $end_time * 60;
                        $duration += ($out_time - $in_time) / 60 / 60 / 60;
                        $duration = number_format((float) $duration, 1, '.', '');

                        $total_duration += number_format($duration, 1, '.', '');

                        $from_date = $date . ' ' . date('H:i:s', $started_at);
                        $to_date = $date . ' ' . date('H:i:s', $end_time);


                        $day = $time->created_at->format('l');

                        // $time_splits .= date('H:i:s', $start_time) . ' - ' . date('H:i:s', $to_time) . ', <br />';


                        $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date' AND current_status='D'");

                        if (count($orders) > 0) {
                            $orders_count += $orders[0]->total_orders;
                        }

                        $total_orders += $orders[0]->total_orders;

                        // $is_holiday = Holiday::where('created_at', $time->created_at)->get();

                        $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();

                        if ($earning_amount) {
                            $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                            if ($earning_amount->bonus_amount != 0) {
                                // $special_order_earnings += $order_amount;
                                // $special_order_count += $orders[0]->total_orders;

                                if ($day == 'Saturday' || $day == 'Sunday') {
                                    $bonus = floor($duration) * $earning_amount->special_bonus_amount;
                                } else {
                                    $bonus = floor($duration) * $earning_amount->bonus_amount;
                                }
                                $bonus = floor($duration) * $earning_amount->bonus_amount;

                                // $normal_order_count += $orders[0]->total_orders;
                                // $date_wise_normal_orders += $normal_order_count;
                                // $normal_order_earnings += $order_amount;
                                // $date_wise_normal_orders_amount += $normal_order_earnings;

                                $is_bonus_hour = 1;
                                $work_duration = $duration;
                            } else {
                                // $special_order_earnings += $order_amount;
                                // $special_order_count += $orders[0]->total_orders;

                                // $date_wise_special_orders += $special_order_count;
                                // $date_wise_special_amount += $special_order_earnings;

                                // $normal_order_count += $orders[0]->total_orders;
                                // $normal_order_earnings += $order_amount;
                            }

                            // $total_earnings = $total_earnings + $order_amount;
                        }

                        $earnings_log[] = [
                            'date' => $date,
                            'time_slot' => date('H:i', $from) . ' - '  . date('H:i', $to),
                            'orders_count' => $orders[0]->total_orders,
                            'order_amount' => $order_amount,
                            'duration' => number_format((float) $duration, 1, '.', ''),
                            'work_duration' => floor($work_duration),
                            'is_bonus_hour' => $is_bonus_hour,
                            'bonus_amount' => $bonus,
                            // 'normal_order_count' => $normal_order_count,
                            // 'normal_order_earnings' => $normal_order_earnings,
                            // 'special_order_count' => $special_order_count,
                            // 'special_order_earnings' => $special_order_earnings,
                        ];
                    }

                    $from_date = $date . ' ' . $slot->from_time;
                    $to_date = $date . ' ' . $slot->to_time;

                    $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date' AND current_status='D'");

                    if (count($orders) > 0) {
                        $orders_count += $orders[0]->total_orders;
                    }

                    $day = strtotime($from_date);
                    $day = date('l', $day);

                    // dd($day);

                    // $_total_orders += $orders_count;

                    // $is_holiday = Holiday::where('created_at', $time->created_at)->get();

                    $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();

                    if ($earning_amount) {
                        $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                        if ($earning_amount->bonus_amount != 0) {
                            // $special_order_earnings += $order_amount;
                            // $special_order_count += $orders[0]->total_orders;

                            if ($day == 'Saturday' || $day == 'Sunday') {
                                $bonus = floor($duration) * $earning_amount->special_bonus_amount;
                            } else {
                                $bonus = floor($duration) * $earning_amount->bonus_amount;
                            }
                            $bonus = floor($duration) * $earning_amount->bonus_amount;

                            $normal_order_count += $orders[0]->total_orders;
                            $date_wise_normal_orders += $normal_order_count;
                            $normal_order_earnings += $order_amount;
                            $date_wise_normal_orders_amount += $normal_order_earnings;

                            $is_bonus_hour = 1;
                            $work_duration = $duration;
                        } else {
                            $special_order_earnings += $order_amount;
                            $special_order_count += $orders[0]->total_orders;

                            $date_wise_special_orders += $special_order_count;
                            $date_wise_special_amount += $special_order_earnings;

                            // $normal_order_count += $orders[0]->total_orders;
                            // $normal_order_earnings += $order_amount;
                        }

                        $total_earnings = $total_earnings + $order_amount;
                    }
                }

                $adjusts = DB::SELECT("SELECT * FROM internal_staffs_sal_adj WHERE is_staff_id = $staff_id AND is_staff_date = '$date'");

                foreach ($adjusts as $key => $adjust) {
                    if ($adjust->is_mode == 'Bonus') {
                        $bonus_amount += $adjust->is_staff_amount;

                        $extra_bonus_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    } elseif ($adjust->is_mode == 'Shortage') {
                        $shortage_amount += $adjust->is_staff_amount;

                        $shortage_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    }
                }

                $attendance_log = [];
                $final_duration = 0;
                $time_splits = '';

                foreach ($worked_hours as $key => $time) {
                    if ($time->checkout_time == Null || $time->checkout_time == 'null' || $time->checkout_time == null) {
                        continue;
                    }
                    $_duration = 0;
                    $in = strtotime($time->checkin_time);
                    $out = strtotime($time->checkout_time);

                    $_duration += ($out - $in) / 60 / 60;
                    $_duration = number_format((float) $_duration, 1, '.', '');


                    $time_splits .= date('H:i:s', $in) . ' - ' . date('H:i:s', $out) . ', <br />';

                    $attendance_log[] = [
                        'total_time' => $time->checkin_time . ' - ' . $time->checkout_time,
                        'duration' => $_duration
                    ];
                    $final_duration += $_duration;
                }

                $prev_time = '';
                $prev_date = '';
                $bonus_amount_final = 0;
                $bonus_hour_final = 0;
                $earnings_logs = [];

                foreach ($earnings_log as $key => $log) {
                    if ($prev_time == $log['time_slot'] && $prev_date == $log['date']) {
                        $temp_log = $earnings_log[$key];

                        array_pop($earnings_logs);

                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'] + $temp_log['orders_count'],
                            'order_amount' => $log['order_amount'] + $temp_log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    } else {
                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'],
                            'order_amount' => $log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    }

                    $prev_time = $log['time_slot'];
                    $prev_date = $log['date'];
                }

                if ($final_duration < 4) {
                    // $bonus_hour_final = 0;
                    $bonus_amount_final = 0;
                }

                $final_amouont = $total_earnings + $bonus_amount_final - $shortage_amount + $star_amount + $bonus_amount;
                $date_wise_final += $final_amouont;
                $date_wise_duration += $final_duration;
                $date_wise_bonus += $bonus_amount_final;


                $data[] = [
                    // 'name' => $staff->first_name . " ($staff_id)",
                    'date' => $date,
                    'login_time' => $login_time,
                    'logout_time' => $logout_time,
                    'time_splits' => $time_splits,
                    'total_duration' => $final_duration,
                    'total_earnings' => $total_earnings,
                    'extra_bonus' => $bonus_amount,
                    'shortage' => $shortage_amount,
                    'final_amount' => $final_amouont,
                    'total_earnings' => $total_earnings,
                    'star_amount' => $star_amount,
                    // 'earnings_log' => $earnings_logs,
                    // 'bonus_log' => $extra_bonus_log,
                    // 'shortage_log' => $shortage_log,
                    // 'attendance' => $attendance_log,
                    'bonus_hour' => $bonus_hour_final,
                    'hour_bonus_amount' => $bonus_amount_final,
                    'normal_order_count' => $normal_order_count,
                    'normal_order_earnings' => $normal_order_earnings,
                    'special_order_count' => $special_order_count,
                    'special_order_earnings' => $special_order_earnings,
                ];


                ++$loop_index;
            }


            if ($date_wise_duration <= 25) {
                $date_wise_final = $date_wise_final - $date_wise_bonus;
            }


            if ($data != []) {
                $staff_data[] = [
                    'name' => $staff->first_name,
                    'user_id' => $staff->id,
                    'data' => $data,
                    'total' => $date_wise_final,
                    'weekly_duration' => $date_wise_duration,
                    'date_wise_star' => $date_wise_stars,
                    'date_wise_normal_orders' => $date_wise_normal_orders,
                    'date_wise_normal_orders_amount' => $date_wise_normal_orders_amount,
                    'date_wise_special_orders' => $date_wise_special_orders,
                    'date_wise_special_amount' => $date_wise_special_amount,
                ];
            }
        }
        return view('snippets/salary_report_ac_tile')->with(['staff_data' => $staff_data]);
    }
    public function get_4hour_list($from_date, $to_date)
    {
        if (!strtotime($from_date) || !strtotime($to_date)) {
            return 'Please check the dates you\'ve entered';
        }
        $time_slots = TimeSlot::all();

        $response = [];
        $from_date_tmp = $from_date;
        $to_date_tmp = $to_date;

        $from_date = $from_date_tmp . ' 00:00:00';
        $to_date = $to_date_tmp . ' 23:00:00';

        $duration = 0;
        $bonus_amount = 0;
        $shortage_amount = 0;
        $total_earnings = 0;
        $date = '';
        $orders_count = 0;
        $attendance_log = [];

        $staffs = DB::SELECT("SELECT * FROM internal_staffs WHERE designation='Delivery staff' and active='Y'");


        $staff_data = [];

        $dates = $this->getDatesFromRange($from_date, $to_date);
        // array_pop($dates);
        foreach ($staffs as $key => $staff) {
            $data = [];
            $staff_id = $staff->id;

            $loop_index = 0;

            $date_wise_final = 0;
            $date_wise_duration = 0;
            $date_wise_bonus = 0;
            $date_wise_stars = 0;
            $date_wise_normal_orders = 0;
            $date_wise_normal_orders_amount = 0;
            $date_wise_special_orders = 0;
            $date_wise_special_amount = 0;

            $seven_days_four_count = 0;

            $temp_worked_hours = StaffAttendance::where('staff_id', $staff_id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->get();


            if (count($temp_worked_hours) > 0) {
                $first_date = $temp_worked_hours[0]->created_at->format('Y-m-d');

                $end_date = end($temp_worked_hours);
                $end_date = end($end_date);

                $end_date->created_at->format('Y-m-d');

                // $last_date = end($temp_worked_hours);
                // $last_date = $last_date[0]->created_at->format('Y-m-d');
                $last_date = $end_date->created_at->format('Y-m-d');
            }

            foreach ($dates as $key => $date) {
                $final_amouont = 0;
                $normal_order_count = 0;
                $normal_order_earnings = 0;
                $special_order_count = 0;
                $special_order_earnings = 0;
                $star_amount = 0;

                $date_stamp = strtotime($date);
                $to_date_stamp = strtotime($to_date_tmp);

                if ($date_stamp > $to_date_stamp) {
                    continue;
                }

                $bonus_amount = 0;
                $shortage_amount = 0;
                $earnings_log = [];
                $extra_bonus_log = [];
                $shortage_log = [];
                $total_earnings = 0;
                $worked_hours = StaffAttendance::where('staff_id', $staff_id)
                    ->where('created_at', 'like',  $date . '%')
                    ->get();

                $orders = DB::SELECT("select json_unquote(json_extract(delivery_assigned_details, '$.star_rate')) as review from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");

                foreach ($orders as $key => $order) {
                    if ($order->review != '') {
                        // $stars = json_decode($order->dlv_review_details)->dlv_cust_rating;
                        if ($order->review >= 5) {
                            $star_amount += 5;
                            $date_wise_stars += 5;
                        }
                    }
                }

                if (count($worked_hours) <= 0) {
                    continue;
                }

                $total_duration = 0;

                foreach ($time_slots as $key => $slot) {
                    $from = strtotime($slot->from_time);
                    $to = strtotime($slot->to_time);

                    $duration = 0;
                    $earnings = 0;
                    $login_time = $worked_hours[0]->checkin_time;
                    $logout_time = '';
                    // dd($worked_hours);
                    foreach ($worked_hours as $key => $time) {
                        $order_amount = 0;
                        $bonus = 0;
                        $is_bonus_hour = 0;
                        $work_duration = 0;

                        $date = $time->created_at->format('Y-m-d');
                        $logout_time = $time->checkout_time;
                        $start_time = strtotime($time->checkin_time);
                        $to_time = strtotime($time->checkout_time);

                        if ($to_time < $from) {
                            continue;
                        }

                        if ($start_time > $to) {
                            continue;
                        }

                        if ($start_time < $from) {
                            $started_at = $from;
                        } else {
                            $started_at = $start_time;
                        }

                        if ($to > $to_time) {
                            $end_time = $to_time;
                        } else {
                            $end_time = $to;
                        }

                        $in_time = $started_at * 60;
                        $out_time = $end_time * 60;
                        $duration += ($out_time - $in_time) / 60 / 60 / 60;
                        $duration = number_format((float) $duration, 1, '.', '');

                        $total_duration += number_format($duration, 1, '.', '');

                        $from_date = $date . ' ' . date('H:i:s', $started_at);
                        $to_date = $date . ' ' . date('H:i:s', $end_time);


                        $day = $time->created_at->format('l');

                        // $time_splits .= date('H:i:s', $start_time) . ' - ' . date('H:i:s', $to_time) . ', <br />';


                        $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date' AND current_status='D'");

                        if (count($orders) > 0) {
                            echo $orders[0]->total_orders . '<br>';
                            $orders_count += $orders[0]->total_orders;
                        }

                        // $is_holiday = Holiday::where('created_at', $time->created_at)->get();

                        $earning_amount = TimeBonus::where('time_slot_id', $slot->id)->where('created_at', 'like', $date . '%')->first();

                        if ($earning_amount) {
                            $order_amount = $orders[0]->total_orders * $earning_amount->amount_per_order;
                            if ($earning_amount->bonus_amount != 0) {
                                // $special_order_earnings += $order_amount;
                                // $special_order_count += $orders[0]->total_orders;

                                if ($day == 'Saturday' || $day == 'Sunday') {
                                    $bonus = floor($duration) * $earning_amount->special_bonus_amount;
                                } else {
                                    $bonus = floor($duration) * $earning_amount->bonus_amount;
                                }
                                $bonus = floor($duration) * $earning_amount->bonus_amount;

                                $normal_order_count += $orders[0]->total_orders;
                                $date_wise_normal_orders += $normal_order_count;
                                $normal_order_earnings += $order_amount;
                                $date_wise_normal_orders_amount += $normal_order_earnings;

                                $is_bonus_hour = 1;
                                $work_duration = $duration;
                            } else {
                                $special_order_earnings += $order_amount;
                                $special_order_count += $orders[0]->total_orders;

                                $date_wise_special_orders += $special_order_count;
                                $date_wise_special_amount += $special_order_earnings;

                                // $normal_order_count += $orders[0]->total_orders;
                                // $normal_order_earnings += $order_amount;
                            }

                            $total_earnings = $total_earnings + $order_amount;
                        }

                        $earnings_log[] = [
                            'date' => $date,
                            'time_slot' => date('H:i', $from) . ' - '  . date('H:i', $to),
                            'orders_count' => $orders[0]->total_orders,
                            'order_amount' => $order_amount,
                            'duration' => number_format((float) $duration, 1, '.', ''),
                            'work_duration' => floor($work_duration),
                            'is_bonus_hour' => $is_bonus_hour,
                            'bonus_amount' => $bonus,
                            'normal_order_count' => $normal_order_count,
                            'normal_order_earnings' => $normal_order_earnings,
                            'special_order_count' => $special_order_count,
                            'special_order_earnings' => $special_order_earnings,
                        ];
                    }
                }

                $adjusts = DB::SELECT("SELECT * FROM internal_staffs_sal_adj WHERE is_staff_id = $staff_id AND is_staff_date = '$date'");

                foreach ($adjusts as $key => $adjust) {
                    if ($adjust->is_mode == 'Bonus') {
                        $bonus_amount += $adjust->is_staff_amount;

                        $extra_bonus_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    } elseif ($adjust->is_mode == 'Shortage') {
                        $shortage_amount += $adjust->is_staff_amount;

                        $shortage_log[] = [
                            'amount' => $adjust->is_staff_amount,
                            'reason' => $adjust->is_reason,
                        ];
                    }
                }

                $attendance_log = [];
                $final_duration = 0;
                $time_splits = '';

                foreach ($worked_hours as $key => $time) {
                    if ($time->checkout_time == Null || $time->checkout_time == 'null' || $time->checkout_time == null) {
                        continue;
                    }
                    $_duration = 0;
                    $in = strtotime($time->checkin_time);
                    $out = strtotime($time->checkout_time);

                    $_duration += ($out - $in) / 60 / 60;
                    $_duration = number_format((float) $_duration, 1, '.', '');

                    // $a = $_duration;
                    // $nums = explode('.', $a);

                    // $duration = $nums[0];

                    // if(isset($nums[1]) && $nums[1] > 5) {
                    //     $duration = $nums[0] . '.5';
                    // } 

                    $time_splits .= date('H:i:s', $in) . ' - ' . date('H:i:s', $out) . ', <br />';

                    $attendance_log[] = [
                        'total_time' => $time->checkin_time . ' - ' . $time->checkout_time,
                        'duration' => $_duration
                    ];
                    $final_duration += $_duration;
                }

                if ($final_duration >= 4) {
                    $seven_days_four_count++;
                }

                $prev_time = '';
                $prev_date = '';
                $bonus_amount_final = 0;
                $bonus_hour_final = 0;
                $earnings_logs = [];

                foreach ($earnings_log as $key => $log) {
                    if ($prev_time == $log['time_slot'] && $prev_date == $log['date']) {
                        $temp_log = $earnings_log[$key];

                        array_pop($earnings_logs);

                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'] + $temp_log['orders_count'],
                            'order_amount' => $log['order_amount'] + $temp_log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    } else {
                        $earnings_logs[] = [
                            'date' => $log['date'],
                            'time_slot' => $log['time_slot'],
                            'orders_count' => $log['orders_count'],
                            'order_amount' => $log['order_amount'],
                        ];

                        if ($log['is_bonus_hour']) {
                            $bonus_hour_final += $log['work_duration'];
                            $bonus_amount_final += $log['bonus_amount'];
                        }
                    }

                    $prev_time = $log['time_slot'];
                    $prev_date = $log['date'];
                }

                if ($final_duration < 4) {
                    // $bonus_hour_final = 0;
                    $bonus_amount_final = 0;
                }

                $final_amouont = $total_earnings + $bonus_amount_final - $shortage_amount + $star_amount + $bonus_amount;
                $date_wise_final += $final_amouont;
                $date_wise_duration += $final_duration;
                $date_wise_bonus += $bonus_amount_final;


                $data[] = [
                    // 'name' => $staff->first_name . " ($staff_id)",
                    'date' => $date,
                    'login_time' => $login_time,
                    'logout_time' => $logout_time,
                    'time_splits' => $time_splits,
                    'total_duration' => $final_duration,
                    'total_earnings' => $total_earnings,
                    'extra_bonus' => $bonus_amount,
                    'shortage' => $shortage_amount,
                    'final_amount' => $final_amouont,
                    'total_earnings' => $total_earnings,
                    'star_amount' => $star_amount,
                    // 'earnings_log' => $earnings_logs,
                    // 'bonus_log' => $extra_bonus_log,
                    // 'shortage_log' => $shortage_log,
                    // 'attendance' => $attendance_log,
                    'bonus_hour' => $bonus_hour_final,
                    'hour_bonus_amount' => $bonus_amount_final,
                    'normal_order_count' => $normal_order_count,
                    'normal_order_earnings' => $normal_order_earnings,
                    'special_order_count' => $special_order_count,
                    'special_order_earnings' => $special_order_earnings,
                ];


                ++$loop_index;
            }

            if ($seven_days_four_count >= 7) {
                $staff_data[] = [
                    'id' => $staff->id,
                    'name' => $staff->first_name,
                    'days' => $seven_days_four_count,
                ];
            }
        }
        // dd($staff_data);
        return view('snippets/four_hour_tile')->with(['staff_data' => $staff_data]);
    }
}
