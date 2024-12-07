<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\MailJob;

class MailNewsController extends Controller
{
    /**
     *  @OA\GET(
     *      path="/api/job",
     *      summary="Start job",
     *      description="Route to start the news sending job",
     *      tags={"Job"},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *  )
    */
    public function index() {
        MailJob::dispatch()->delay(now()->addSeconds(10));
        return response()->json(["msg" => "ok"], 200);
    }
}
