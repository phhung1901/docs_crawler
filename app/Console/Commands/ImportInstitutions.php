<?php

namespace App\Console\Commands;

use App\Crawler\UrlHelper;
use App\Models\Institution;
use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psy\Exception\TypeErrorException;

class ImportInstitutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'institutions:import
    {--country= : country}
    {--domain= : domain institution}
    {--host= : host}
    {--token= : token}
    {--with-subject : import with subject}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = 'App\Models\Institution';
        $filters = [
            'with' => ['source'],
            'exact' => [
                'country' => $this->option('country'),
            ],

        ];

        if ($this->option('domain')) {
            $filters = [
                'contains' => [
                    'website' => $this->option('domain') ? $this->option('domain') : null,
                ]
            ];
        }

        if ($this->option('with-subject')) {
            $filters['with'][] = 'courses';
        }

        $page = 1;
        do {
            $filters['page'] = $page;
            $institutions = $this->filtered($model, $filters);
            foreach ($institutions as $institution_data) {
                if (!$institution_data['website']) {
                    continue;
                }

                try {
                    $domain = UrlHelper::domain(UrlHelper::homepage($institution_data['website']));
                }catch (\Exception|\Throwable $exception){
                    continue;
                }

                //convert API data
                if ($institution_data['type'] == 'high_school') $institution_data['type'] = 'school';
                $institution_data['country'] = Str::lower($institution_data['country']);
                $institution_data['website'] = UrlHelper::homepage($institution_data['website'], false);

                $this->warn("Adding: [{$institution_data['name']}] [{$institution_data['website']}]");

                $institution = Institution::updateOrCreate([
                    'domain' => $domain,
                ], Arr::only($institution_data, [
                    'name', 'global_name' , 'code',
                    'website','type', 'country',
                    'address', 'payload'
                ]));


                $this->info("Added: [$institution->name] [$institution->domain]");

            }
            $page++;
        } while($institutions->hasMorePages());

        return self::SUCCESS;
    }

    protected function getClient() {
        $host = $this->option('host');
        $token = $this->option('token');

        return new Client([
            'base_uri' => $host,
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'verify' => false,
        ]);
    }

    public function filtered($model, $filters) : Paginator
    {
        $filters['model'] = $model;
        $response = $this->getClient()->get('api/model', [
            'query' => $filters,
        ]);
        $result = Utils::jsonDecode($response->getBody()->getContents(), true);
        $page = new Paginator($result['data'], $result['per_page'], $result['current_page'], [
            //path, query, fragment, pageName
            'path' => $result['path'],
            'query' => $filters,
        ]);
        $page->hasMorePagesWhen($result['next_page_url']);
        return $page;
    }
}
