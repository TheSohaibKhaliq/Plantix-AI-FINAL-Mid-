<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiseaseReportSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = DB::table('users')->where('role', 'user')->get(['id']);
        $experts = DB::table('users')->whereIn('role', ['expert', 'agency_expert'])->get(['id', 'email']);

        if ($farmers->isEmpty()) {
            $this->command->warn('No farmer users found.');
            return;
        }

        $now = Carbon::now();

        // Realistic disease dataset for Pakistani crops
        $diseases = [
            [
                'crop'       => 'Wheat',
                'disease'    => 'Yellow Rust (Stripe Rust)',
                'confidence' => 0.92,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Caused by Puccinia striiformis f. sp. tritici. Yellow-orange pustules appear in stripes along leaf veins. Common in cool, humid conditions (10–15°C).',
                'organic'        => 'Remove and destroy infected plant debris. Apply sulfur-based fungicide (wettable sulfur 80WP at 2kg/acre). Increase plant spacing for aeration.',
                'chemical'       => 'Propiconazole (Tilt 250EC) at 0.1% spray. Apply Tebuconazole (Folicur 250EW) at 750ml/ha. Repeat after 14 days if infection persists.',
                'preventive'     => 'Use rust-resistant varieties (Punjab-2011, Akbar-2019). Timely sowing (1–15 November). Balanced fertilization — avoid excess nitrogen. Monitor fields from January.',
                'products'       => ['Propiconazole 250EC', 'Wettable Sulfur 80WP', 'Tebuconazole 250EW'],
                'user_desc'      => 'My wheat leaves show yellow powder-like coating in lines. Started 3 weeks ago from one corner of the field.',
            ],
            [
                'crop'       => 'Cotton',
                'disease'    => 'Cotton Leaf Curl Virus (CLCuV)',
                'confidence' => 0.87,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Transmitted by whitefly (Bemisia tabaci). Leaves curl upward, veins thicken, leaf lamina reduces. Severe infection stunts plant growth entirely.',
                'organic'    => 'Yellow sticky traps for whitefly monitoring. Neem oil (5ml/L) foliar spray to repel whitefly. Inter-crop with maize to reduce whitefly density.',
                'chemical'   => 'Imidacloprid (Confidor 200SL) at 250ml/acre. Thiamethoxam (Actara 25WG) at 80g/acre as soil drench or spray.',
                'preventive' => 'Approved CLCuV-tolerant varieties (NIBGE Bt-1, FH-142). Early sowing (April 15 – May 15). Weekly scouting for whitefly adults from June.',
                'products'   => ['Imidacloprid 200SL', 'Thiamethoxam 25WG', 'Neem Oil 5%'],
                'user_desc'  => 'Leaves curling upward with dark vein thickening. Some plants completely stunted. Looks like it is spreading fast.',
            ],
            [
                'crop'       => 'Rice',
                'disease'    => 'Rice Blast (Neck Rot)',
                'confidence' => 0.89,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Caused by Magnaporthe oryzae. Diamond-shaped gray-brown lesions on leaves; neck rot at panicle stage causes white panicles with no grain (empty heads).',
                'organic'    => 'Apply silicon (silica gel) to strengthen cell walls. Trichoderma-based bio-fungicide soil application. Avoid overhead irrigation. Use resistant varieties.',
                'chemical'   => 'Tricyclazole (Beam 75WP) at 200g/acre. Isoprothiolane (Fuji-One) at 400ml/acre. Apply at flag leaf to heading stage for neck blast control.',
                'preventive' => 'Certified blast-resistant varieties (Super Basmati, PK-386). Balanced N application — split urea into 3 doses. Drain fields periodically. Avoid late-evening irrigations.',
                'products'   => ['Tricyclazole 75WP', 'Isoprothiolane 40EC', 'Propiconazole 250EC'],
                'user_desc'  => 'Panicle necks turning dark brown and all grains look empty. Started appearing in the last week of August.',
            ],
            [
                'crop'       => 'Tomato',
                'disease'    => 'Early Blight (Alternaria solani)',
                'confidence' => 0.83,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Dark brown-black target-like spots with concentric rings on older leaves. Causes premature defoliation, reducing fruit size and yield.',
                'organic'    => 'Remove affected lower leaves immediately. Baking soda (5g/L) spray as preventive. Apply copper-based bactericide (Kocide 3000) at first sign of disease.',
                'chemical'   => 'Mancozeb + Metalaxyl (Ridomil Gold MZ) at 2g/L spray. Azoxystrobin (Amistar) at 0.5g/L. Rotate fungicide groups to prevent resistance.',
                'preventive' => 'Stake plants for good air circulation. Drip irrigation, avoid leaf wetness. Apply mulch to prevent soil splash. Use disease-free certified seed.',
                'products'   => ['Mancozeb 75WP', 'Copper Oxychloride 50WP', 'Azoxystrobin 25SC'],
                'user_desc'  => 'Brown spots with rings on the bottom leaves of my tomato plants. Leaves are yellowing and falling off quickly.',
            ],
            [
                'crop'       => 'Sugarcane',
                'disease'    => 'Red Rot (Colletotrichum falcatum)',
                'confidence' => 0.78,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Internal reddening of stalk with white patches. Affected canes emit sour smell. Disease spreads through infected setts and waterlogged conditions.',
                'organic'    => 'Hot water treatment of setts at 50°C for 30 minutes. Destroy infected cane stalks by burning. Do not ratoon infected crops.',
                'chemical'   => 'Sett treatment with Carbendazim (Derosal 500SC) 0.1% solution. Apply Mancozeb as soil application in furrows before planting.',
                'preventive' => 'Plant resistant varieties (CPF-237, HSF-240). Improve drainage in waterlogged fields. Plant disease-free healthy setts. 3-year crop rotation.',
                'products'   => ['Carbendazim 500SC', 'Mancozeb 75WP', 'Thiram 80WP'],
                'user_desc'  => 'Cut one stalk and noticed red inside with white spots and bad smell. Several plants in the row look stunted.',
            ],
            [
                'crop'       => 'Maize',
                'disease'    => 'Corn Stalk Rot (Fusarium)',
                'confidence' => 0.85,
                'model'      => 'plantix-disease-cnn-v3',
                'description'    => 'Lower internodes discolor and rot; plant lodges easily. Pink or white fungal growth inside stalk. Caused by Fusarium moniliforme in hot, humid conditions.',
                'organic'    => 'Promote plant vigor through balanced nutrition. Apply biocontrol agent Trichoderma viride at 2.5 kg/acre in soil. Improve drainage.',
                'chemical'   => 'Furrow application of Carbendazim or Benomyl at planting. Foliar spray Tebuconazole at early stalk elongation if pressure is high.',
                'preventive' => 'Plant resistant hybrids. Harvest at correct moisture (< 23%). Avoid water stress during grain fill. Balance NPK — excess N increases susceptibility.',
                'products'   => ['Tebuconazole 250EW', 'Trichoderma viride WP', 'Carbendazim 50WP'],
                'user_desc'  => 'Plants falling over easily when I push them. The lower stem is soft and brown inside with a pinkish tinge.',
            ],
        ];

        $reportStatuses = ['processed', 'processed', 'processed', 'manual_review', 'pending'];

        $reports     = [];
        $suggestions = [];
        $farmerList  = $farmers->all();
        shuffle($farmerList);

        // Give each farmer 1–3 disease reports
        foreach ($farmerList as $i => $farmer) {
            $numReports = rand(1, 2);
            for ($r = 0; $r < $numReports; $r++) {
                $disease = $diseases[($i + $r) % count($diseases)];
                $reportedAt = Carbon::now()->subDays(rand(5, 300));

                $allPredictions = [
                    ['label' => $disease['disease'],                  'confidence' => $disease['confidence']],
                    ['label' => 'Nutrient Deficiency (' . $disease['crop'] . ')', 'confidence' => round(1 - $disease['confidence'] - 0.05, 2)],
                    ['label' => 'Healthy',                            'confidence' => 0.03],
                ];

                $reports[] = [
                    'user_id'          => $farmer->id,
                    'crop_name'        => $disease['crop'],
                    'image_path'       => 'disease_reports/' . $farmer->id . '_' . ($r + 1) . '.jpg',
                    'detected_disease' => $disease['disease'],
                    'confidence_score' => $disease['confidence'],
                    'all_predictions'  => json_encode($allPredictions),
                    'model_used'       => $disease['model'],
                    'status'           => $reportStatuses[array_rand($reportStatuses)],
                    'user_description' => $disease['user_desc'],
                    'created_at'       => $reportedAt,
                    'updated_at'       => $now,
                ];
            }
        }

        foreach (array_chunk($reports, 50) as $chunk) {
            DB::table('crop_disease_reports')->insert($chunk);
        }

        // Reload inserted reports to get IDs
        $reportRows  = DB::table('crop_disease_reports')->orderBy('id')->get(['id', 'detected_disease']);
        $expertUsers = $experts->isNotEmpty() ? $experts : collect([null]);

        foreach ($reportRows as $report) {
            // Find the matching disease data
            $disease = collect($diseases)->firstWhere('disease', $report->detected_disease);
            if (!$disease) {
                $disease = $diseases[0];
            }

            $expertVerified = rand(0, 1);
            $verifiedBy     = null;
            if ($expertVerified && $expertUsers->isNotEmpty()) {
                $verifiedBy = $expertUsers->random()->id ?? null;
            }

            $suggestions[] = [
                'report_id'             => $report->id,
                'disease_name'          => $disease['disease'],
                'description'           => $disease['description'],
                'organic_treatment'     => $disease['organic'],
                'chemical_treatment'    => $disease['chemical'],
                'preventive_measures'   => $disease['preventive'],
                'recommended_products'  => json_encode($disease['products']),
                'expert_verified'       => $expertVerified,
                'verified_by'           => $verifiedBy,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        foreach (array_chunk($suggestions, 50) as $chunk) {
            DB::table('disease_suggestions')->insert($chunk);
        }

        $this->command->info(sprintf(
            'DiseaseReportSeeder: %d disease reports, %d suggestions seeded.',
            count($reports),
            count($suggestions)
        ));
    }
}
