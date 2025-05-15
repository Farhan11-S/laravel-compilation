<?php

namespace Database\Seeders;

use App\Models\Cluster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clusters = [
            'BMW',
            'Bahamas',
            'Brazilia',
            'Buenos',
            'Caribbean',
            'CentroHavana',
            'ClioVintage',
            'Costarica',
            'DeRio',
            'LAZ',
            'LaVintage',
            'Mexicano',
            'Patagonia',
            'SalingSapa',
            'Santiago',
            'VirginIsland',
        ];

        foreach ($clusters as $clusterName) {
            $cluster = Cluster::where('name', $clusterName)->first();
            if ($cluster === null) {
                $cluster = Cluster::create([
                    'name' => $clusterName,
                ]);
            }

            for ($i = 1; $i <= 2; $i++) {
                $cluster->users()->create([
                    'name' => $cluster->name . ' User ' . $i,
                    'email' => $cluster->name . 'user' . $i . '@mail.com',
                    'username' => $cluster->name . 'user' . $i,
                    'role' => 'simpul',
                    'password' => Hash::make($cluster->name . 'Hore' . $i),
                ]);
            }
        }
    }
}
