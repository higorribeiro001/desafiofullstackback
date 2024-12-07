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
        $news = []; // list of news that will be saved in the cache with redis
        $queueNews = []; // queue of news that will be sent

        if (Cache::get('news')) { // if news is not null in the cache
            $news = Cache::get('news');
        }

        if (Cache::get('queueNews')) { // if queue of news is not null in the cache
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
            $newsUol = $dataArray['channel']['item']; // uol api news

            $existingTitles = array_column($news, 'title'); // extract titles from news array
            foreach ($newsUol as $n)
            {
                if (!in_array($n['title'], $existingTitles)) { // if the current title does not exist in the extracted titles
                    $queueNews[] = $n; // the current title will be added to the queue
                }
            }
            Cache::put('queueNews', $queueNews, 259200); // overlap queue
            Cache::put('news', array_merge($news, $newsUol), 259200); // overlay news
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
        // I used FIFO as a strategy
        // First In, First Out
        $queueNews = [];

        if (Cache::get('queueNews')) {
            $queueNews = Cache::get('queueNews');
        } 

        if (count($queueNews) > 0) { // if the number of news in the queue is greater than 0
            $users = $this->getUsers(); // email list
            Mail::to($users)->send(new NewsUolMail($queueNews[0]['title'], $queueNews[0]['description'], $queueNews[0]['link'], $queueNews[0]['pubDate'])); // sends emails with the first news in line
            array_shift($queueNews); // remove the first news from the queue
            Cache::put('queueNews', $queueNews, 259200); // overlap queue
        } else {
            $this->getNews(); // executes function that returns news
        }
        
        self::dispatch()->delay(now()->addSeconds(60)); // makes use of recursion so that it loops in the background
    }
}
