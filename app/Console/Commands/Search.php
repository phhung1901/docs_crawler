<?php

namespace App\Console\Commands;

use App\Crawler\UrlHelper;
use App\Models\Institution;
use App\Services\SearxService\SearxClient;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class Search extends Command
{
    protected $min_document = 20;
    protected $per_page=100;

    protected SearxClient $client;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:search
    {--country=vn}
    {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $country = $this->option('country');
        $id = $this->option('id');
        $this->client = new SearxClient();
        $next = true;
        do{
            $institution = Institution::where('crawl_status', null)
                ->when($id, fn(Builder $query) => $query->where('id', $id))
                ->where('country', $country)
                ->first();
            if (!$institution){
                $next = false;
                continue;
            }
            $site = UrlHelper::domain(UrlHelper::homepage($institution->website));
            $query = "site:$site filetype:PDF";

            $this->info("[Searching] Domain: {$site} -> Search string: $query");

            for ($i=1; $i<=$this->per_page; $i++){
                $natural_result = $this->client->searchWithRetry('google', $query, $i, 100, 5);
                $organic_results = $natural_result->organic_results->toArray();
                if (!$organic_results && $i != 1){
                    $institution->crawl_status = 1;
                    $institution->save();
                    break;
                }
                if (!$organic_results && $i == 1){
                    $institution->crawl_status = -99;
                    $institution->save();
                    break;
                }


            }
        }while($next);

        return self::SUCCESS;
    }
}
