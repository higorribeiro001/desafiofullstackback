<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\MailJob;

class MailNewsController extends Controller
{
    public function index() {
        MailJob::dispatch()->delay(now()->addSeconds(10));
        return response()->json(["msg" => "ok"], 200);
    }
}
