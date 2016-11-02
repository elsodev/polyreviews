<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // seed neighbourhood for Subang Jaya
        $subang_jaya = \App\Area::where('name', 'Subang Jaya')->select('id')
            ->first();

        $hoods = [
            ['SS12', ''],
            ['SS13', ''],
            ['SS14', ''],
            ['SS15', ''],
            ['SS16', ''],
            ['SS17', ''],
            ['SS18', ''],
            ['SS19', ''],
            ['PJ7/9/11', 'Bandar Sunway'],
            ['USJ', ''],
            ['Putra Heights', ''],
            ['Batu Tiga', ''],
        ];

        foreach($hoods as $hood)
        {
            \App\Neighbourhood::create([
                'name' => $hood[0],
                'other_name' => $hood[1],
                'area_id' => $subang_jaya->id
            ]);
        }

        factory(App\User::class)->create([
            'name' => 'polytester',
            'email' => 'homestead@gmail.com',
            'password' => bcrypt('secret')
        ]);
    }
}
