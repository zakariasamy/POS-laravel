<?php

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = ['ahmed', 'mohamed'];
        $phone = ['01032323232', '01145454545'];

        foreach ($clients as $client){
            Client::create([
                'name' => $client,
                'phone' => $phone,
                'address' => '12 st maadi cairo'
            ]);
        }
    }
}
