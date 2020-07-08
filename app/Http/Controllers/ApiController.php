<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\StaffAttendance;
use App\TimeBonus;
use App\TimeSlot;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Respnd with attendance report of delivery boys on date wise.
     * @method POST
     * @var Request $request
     * @post_data staff_id, from_date, to_date. 
     * @return \Illuminate\Http\Response
     */
    public function splitted_attendance_report(Request $request)
    {
        $response = [];
        $reports_response = [];
        $staff_id = $request->post('staff_id');
        $from_date_tmp = $request->post('from_date');
        $to_date_tmp = $request->post('to_date');

        $time_slots = TimeSlot::all()->where('status', '1');

        $total_earnings = 0;

        foreach ($time_slots as $key => $slote) {
            $from_date = $from_date_tmp . ' ' . $slote->from_time;
            $to_date = $to_date_tmp . ' ' . $slote->to_time;

            $reports = StaffAttendance::where('staff_id', $staff_id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->get();


            $orders = \DB::SELECT("select count(*) as total_orders from order_master where delivery_assigned_to = $staff_id and order_date between '$from_date' AND '$to_date'");

            if(count($reports) <= 0) {
                $reports_response[] = [
                    'time_slot' => $slote->from_time . ' - ' . $slote->to_time,
                    'duration' => 0,
                    'order_count' => 0,
                    'earnings' => 0,
                ];
            }

            foreach ($reports as $key => $report) {
                $duration = 0;
                $date = $report->created_at->format('Y-m-d');
                $day = $report->created_at->format('l');
                $checkin_time = date('g', strtotime($report->checkin_time));
                $checkot_time = date('g', strtotime($report->checkout_time));

                $in_time = $checkin_time * 60;
                $out_time = $checkot_time * 60;
                $duration = ($out_time - $in_time) / 60;

                $earnings = 0;
                $is_holiday = 0;

                $holiday = Holiday::where('created_at', $report->created_at)->first();

                if ($holiday) {
                    $is_holiday = 1;
                }

                $time_slot_bonus = 0;
                $time_bonus = TimeBonus::where('time_slot_id', $slote->id)->whereBetween('created_at', [$from_date, $to_date])->first();

                if (!$time_bonus) {
                    $time_slot_bonus = 0;
                } else {
                    if ($day == "Sunday" || $day == "Saturday" || $is_holiday) {
                        $time_slot_bonus = $time_bonus->special_bonus_amount;
                    } else {
                        $time_slot_bonus = $time_bonus->bonus_amount;
                    }
                }

                $earnings = $time_slot_bonus * $duration;

                $reports_response[] = [
                    'time_slot' => $slote->from_time . ' - ' . $slote->to_time,
                    'duration' => $duration,
                    'order_count' => $orders[0]->total_orders,
                    'earnings' => $earnings,
                ];
            }
        }

        $response = [
            'status' => 'success',
            'response_code' => 200,
            // 'time_slots' => $time_slots,
            'reports' => $reports_response
        ];

        return response($response);
    }

    // public function attendance_report(Request $request)
    // {
    //     $response = [];
    //     $reports_response = [];
    //     $staff_id = $request->post('staff_id');
    //     $from_date_tmp = $request->post('from_date');
    //     $to_date_tmp = $request->post('to_date');

    //     $time_slots = TimeSlot::all()->where('status', '1');
        
    //     $reports = StaffAttendance::where('staff_id', $staff_id)
    //                                 ->whereBetween('created_at', [$from_date, $to_date])
    //                                 ->get();
    // }
}
