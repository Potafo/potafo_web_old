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
        $is_from_app = $request->post('from_dash');

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

        $dates = $this->getDatesFromRange($from_date, $to_date);
        foreach ($dates as $key => $date) {
            $date_stamp = strtotime($date);
            $to_date_stamp = strtotime($to_date_tmp);

            if($date_stamp > $to_date_stamp) {
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

            $orders = DB::SELECT("select review_star from order_master where delivery_assigned_to = $staff_id and order_date like '$date%'");

            foreach ($orders as $key => $order) {
                if($order->review_star == 5) {
                    $star_amount += 5;
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
                        if($earning_amount->bonus_amount != 0) {
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
                if($time->checkout_time == Null || $time->checkout_time == 'null' || $time->checkout_time == null) {
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


            $final_amouont = $total_earnings + $bonus_amount - $shortage_amount + $star_amount;

            $prev_time = '';
            $prev_date = '';
            $bonus_amount_final = 0;
            $bonus_hour_final = 0; 
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

                    if($log['is_bonus_hour']) {
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

                    if($log['is_bonus_hour']) {
                        $bonus_hour_final += $log['work_duration'];
                        $bonus_amount_final += $log['bonus_amount'];
                    }
                }

                $prev_time = $log['time_slot'];
                $prev_date = $log['date'];
            }


            $data[] = [
                'date' => $date,
                'total_duration' => number_format((float) $total_duration, 1, '.', ''),
                'total_earnings' => $total_earnings,
                'extra_bonus' => $bonus_amount,
                'shortage' => $shortage_amount,
                'final_amount' => $final_amouont,
                'total_earnings' => $total_earnings,
                'star_amount' => $star_amount,
                'earnings_log' => $earnings_log,
                'bonus_log' => $extra_bonus_log,
                'shortage_log' => $shortage_log,
                'attendance' => $attendance_log,
                'bonus_hour' => $bonus_hour_final,
                'hour_bonus_amount' => $bonus_amount_final
            ];
        }

        if($is_from_app == 1) {
            $response = [
                'status' => 'success',
                'response_code' => 200,
                'total_earnings' => $total_earnings,
                'duraiion' => number_format((float) $total_duration, 1, '.', ''),
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
}
