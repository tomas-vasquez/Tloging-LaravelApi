<?php

namespace App\Http\Controllers;

use App\Pay_report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PayReportsController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'product' => "required",
            'description' => "required",
        ]);

        //guardamos las imagenes que nos llegan
        $report_id = Str::random(8);
        $pic_name = "report-" . $report_id;
        $img_number = 0;

        if ($request->file('blob0') !== null) {
            $request->file('blob0')->storeAs("public/pay_reports", $pic_name . "-0.jpg");
            ++$img_number;
        }
        if ($request->file('blob1') !== null) {
            $request->file('blob1')->storeAs("public/pay_reports", $pic_name . "-1.jpg");
            ++$img_number;
        }
        if ($request->file('blob2') !== null) {
            $request->file('blob2')->storeAs("public/pay_reports", $pic_name . "-2.jpg");
            ++$img_number;
        }

        //guardamos el reporte en la base de datos
        $user =  $request->user();
        $inserted_data = [
            "report_id" => $report_id,
            "user_id" => $user["id"],
            "parent_id" => $user["parent_id"],
            "img_number" => $img_number,
            "description" => $request->description,
            "product" => $request->product,
        ];

        Pay_report::push_report($inserted_data);

        return response()->json($inserted_data, 200);
    }




    public function remove(Request $request, $report_id)
    {
        
        $user =  $request->user();
        $pay_reports = Pay_report::get_reports($user["id"])["data"];
        $pay_report_key = array_search($report_id, array_column($pay_reports, "report_id"));

        if ($pay_report_key !== false) {

            $pay_report = (array)$pay_reports[$pay_report_key];

            $result = Pay_report::delete_report($pay_report);
            
            return response($result, 200);

        } else {
            return response("not", 400);
        }
    }




    public function aprove_report(Request $request, $report_id)
    {
        $user =  $request->user();
        $pay_reports = Pay_report::get_reports($user["id"])["data"];
        $pay_report_key = array_search($report_id, array_column($pay_reports, "report_id"));

        if ($pay_report_key !== false) {

            $pay_report = (array)$pay_reports[$pay_report_key];
            
            $result = User::deliver_product($pay_report);
            
            return response($result, 200);

        } else {

            return response("not", 400);
        }
    }
}
