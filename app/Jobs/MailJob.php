<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\NewsUolMail;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getNews() 
    {
        $news = [];
        $queueNews = [];

        if (Cache::get('news')) {
            $news = Cache::get('news');
        }

        if (Cache::get('queueNews')) {
            $queueNews = Cache::get('queueNews');
        } 

        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://rss.uol.com.br/feed/tecnologia.xml');

        if ($response->successful()) {
            $xmlContent = $response->body();
            $xmlContent = mb_convert_encoding($xmlContent, 'UTF-8', 'ISO-8859-1');

            $xmlObject = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

            $dataArray = json_decode(json_encode($xmlObject), true);
            $newsUol = $dataArray['channel']['item'];

            $existingTitles = array_column($news, 'title');
            foreach ($newsUol as $n)
            {
                if (!in_array($n['title'], $existingTitles)) {
                    $queueNews[] = $n;
                }
            }
            Cache::put('queueNews', $queueNews, 259200);
            Cache::put('news', array_merge($news, $newsUol), 259200);
        }
    }

    public function getUsers() 
    {
        $users = User::select(['email'])->get()->toArray();
        $emailUsers = [];

        foreach ($users as $user) 
        {
            $emailUsers[] = $user['email'];
        }

        return $emailUsers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $queueNews = [];

        if (Cache::get('queueNews')) {
            $queueNews = Cache::get('queueNews');
        } 

        if (count($queueNews) > 0) {
            $users = $this->getUsers();
            Mail::to($users)->send(new NewsUolMail($queueNews[0]['title'], $queueNews[0]['description'], $queueNews[0]['link'], $queueNews[0]['pubDate']));
            array_shift($queueNews);
            Cache::put('queueNews', $queueNews, 259200);
        } else {
            $this->getNews();
        }
        
        self::dispatch()->delay(now()->addSeconds(10));
    }
}
