<?php

namespace App\Http\Controllers;

use App\StaffAttendance;
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
    public function attendance_report(Request $request)
    {
        $staff_id = $request->post('staff_id');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');

        $reports = StaffAttendance::all()
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date);

        dd($reports);
    }
    
}
