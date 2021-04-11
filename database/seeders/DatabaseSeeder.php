<?php

namespace Database\Seeders;

use App\Domain\Models\Company;
use App\Domain\Models\Person;
use App\Domain\Models\Transfer;
use App\Domain\Models\User;
use App\Domain\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $personPayer1 = Person::factory()->create();
        Company::factory()->create(['person_id' => $personPayer1->id]);
        $walletPayee = Wallet::factory()->create(['person_id' => $personPayer1->id]);
        User::factory()->create(['person_id' => $personPayer1->id, 'email' => 'teste@lojista.com']);

        $personPayer2 = Person::factory()->create();
        $walletPayer =  Wallet::factory()->create(['person_id' => $personPayer2->id]);
        User::factory()->create(['person_id' => $personPayer2->id, 'email' => 'teste@comum1.com']);

        $personPayer3 = Person::factory()->create();
        Wallet::factory()->create(['person_id' => $personPayer3->id]);
        User::factory()->create(['person_id' => $personPayer3->id, 'email' => 'teste@comum2.com']);

        Transfer::factory()->create(
            [
                'status'             => Transfer::STATUS_PENDING,
                'wallet_payee_id'    => $walletPayee->id,
                'wallet_payer_id'    => $walletPayer->id,
                'value'              => $walletPayer->amount / 2,
                'notification_date'  => null,
            ]
        );
    }
}
