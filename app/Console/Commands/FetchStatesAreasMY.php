<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte;
use App\State;
use App\Area;

class FetchStatesAreasMY extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:states-areas-my';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches Statea and Areas data from Mudah.my';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $crawler = Goutte::request('GET', 'http://www.mudah.my/');

            $this->info('Fetch all States and its areas');

            $states = $crawler->filter('.newRegionList li')->each(function ($node) {
                $anchor = $node->filter('a');
                if($anchor->attr('title') != 'Entire Malaysia') {
                    $this->info($anchor->attr('title'));

                    return [
                        'name' => $anchor->attr('title'),
                        'area_link' => $anchor->attr('href'),
                    ];
                } else {
                    return null;
                }
            });

            // get areas for each states
            foreach($states as $state)
            {
                if(!is_null($state)) {
                    $crawler = Goutte::request('GET', $state['area_link']);

                    $this->info('GET: '.$state['area_link']);

                    $new_state = State::create([
                        'name' => $state['name']
                    ]);

                    $crawler->filter('#searcharea_detailed > option')->each(function ($node) use ($new_state) {
                        if($node->text() != 'Select Area') {

                            Area::create([
                                'name' => trim($node->text()),
                                'state_id' => $new_state->id
                            ]);

                            $this->info($new_state->name .'+'. $node->text());
                        }
                    });

                }
            }


       }
}
