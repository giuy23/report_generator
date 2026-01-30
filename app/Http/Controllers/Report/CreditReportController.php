<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request\CreditReportRequest;
use App\Jobs\GenerateReportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreditReportController extends Controller
{
    public function generate_report(CreditReportRequest $request)
    {
        try {

            $file_name = "credit_report_{$request->start_date}_{$request->end_date}.xlsx";

            if (!Storage::disk('local')->exists("reports/{$file_name}")) {
                dispatch(new GenerateReportJob($request->start_date, $request->end_date, $file_name));
            }

            return response()->json(['type' => 'success', 'file' => $file_name]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'error']);
        }
    }

    public function verify_document(Request $request): JsonResponse
    {
        $path = "reports/{$request->file}";
        $exists = Storage::disk('local')->exists($path);

        return response()->json([
            'exists' => $exists,
            'link' => "reports/{$request['file']}"
        ]);
    }

    public function download(Request $request)
    {
        $path = "reports/{$request->file}";

        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }
}
