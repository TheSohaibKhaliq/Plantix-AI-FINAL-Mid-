<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $farmers    = DB::table('users')->where('role', 'user')->get(['id', 'name']);
        $expertRows = DB::table('experts')->get(['id', 'user_id']);
        $categories = DB::table('forum_categories')->get(['id', 'slug']);

        if ($farmers->isEmpty()) {
            $this->command->warn('No farmers found.');
            return;
        }
        if ($categories->isEmpty()) {
            $this->command->warn('No forum categories. Run ForumCategorySeeder first.');
            return;
        }

        $now = Carbon::now();

        // Thread templates keyed by forum category slug
        $threadData = [
            'crop-management' => [
                ['title' => 'Best wheat variety for Punjab 2024 — which one gave you highest yield?',
                 'body'  => "Asalam o Alaikum kisaan bhai. Is saal maine AARI-2011 try kiya tha lekin yield 45 mann per acre se aagay nahi gayi. Khud se experiment karke mujhe pata chala ke Super Galaxy bhee achi performance deta hai. Aap log konsa variety use kartay hain aur kya experience raha? Soil type bhi batao kyunke meri zameen clay loam hai Sahiwal ke pass."],
                ['title' => 'Cotton sowing date debate — April 20 vs May 1 for Central Punjab',
                 'body'  => "I planted cotton on April 20th last year and had some germination issues due to cold nights. My neighbor waited until May 5 and got better stand. Can anyone share their experience from Faisalabad district specifically? Pre-irrigation or dry sowing — what works better?"],
                ['title' => 'First-time sugarcane cultivation — how to calculate plant to ratoon ratio?',
                 'body'  => "I have 20 acres and thinking of planting sugarcane for the first time near Rahim Yar Khan. The ginning factory is 12km away. How many acres should I plant vs ratoon? What is the typical yield difference between fresh plant and first ratoon in my area? Any advice from experienced growers would be appreciated."],
            ],
            'pest-disease-control' => [
                ['title' => 'URGENT: Yellow rust spreading fast — how to stop it from destroying whole field?',
                 'body'  => "My wheat crop is in danger! Yellow stripe rust appeared 3 days ago and today it has spread to almost 40% of the field. I am in 90 DAE (days after emergence) stage. I applied Tilt 250EC yesterday but not sure if it's enough. Should I spray again after 7 days or wait 14 days? Current temp is 12°C nights and 22°C days. Please help urgently."],
                ['title' => 'Cotton whitefly massive outbreak — Imidacloprid stopped working?',
                 'body'  => "Three consecutive sprays of Imidacloprid 200SL at full dose and the whitefly population is still building. I think resistance has developed. What alternative chemistry should I switch to? My field is in Vehari, heavy cotton zone. Leaf curl symptoms starting on 20% of plants now."],
                ['title' => 'Tomato leaves have yellow-brown spots with rings — early blight or something else?',
                 'body'  => "Growing tomatoes in a kitchen garden plus 0.5 acres commercial. Lower leaves showing yellow-brown spots that have concentric ring patterns inside them. Leaves dying from bottom up. I have Mancozeb 75WP in stock — is this sufficient or should I get something else? Pictures attached if I can figure out how to upload."],
            ],
            'fertilizers-nutrition' => [
                ['title' => 'DAP vs NP 20-20 — is there any real difference in field performance?',
                 'body'  => "Local dealer is pushing NP 20-20 at same price as DAP. He says it's better. DAP gives 18-46, NP gives 20-20. For wheat basal dose, which is actually better? I've always used DAP but want to understand the chemistry properly before switching. Any agronomist here who can explain clearly?"],
                ['title' => 'Zinc deficiency confirmed by soil test — Zinc Sulfate or Zinc EDTA chelate?',
                 'body'  => "My soil test came back with Zn = 0.3 mg/kg (deficient). Dealer offers Zinc Sulfate 33% at Rs. 115/kg and Zinc EDTA chelate at Rs. 850/kg. Is the chelate worth 7x the price or is plain ZnSO4 fine for soil application? Crop is rice paddy, loamy soil, pH 7.8."],
                ['title' => 'Potassium deficient crop — SOP vs MOP for salt-affected land?',
                 'body'  => "My land has EC above 4 dS/m (slightly saline). My soil K is low (80 kg/ha). Dealer says to use SOP (Sulfate of Potash) instead of MOP (Muriate of Potash/KCl) to avoid adding more Cl ions. Is this correct? The price difference is huge. Cotton crop currently at squaring stage."],
            ],
            'irrigation-water' => [
                ['title' => 'Furrow vs drip irrigation for cotton — water saving and yield comparison?',
                 'body'  => "Government is offering 50% subsidy on drip irrigation systems. My current furrow system uses roughly 6 acre-feet of water per season for cotton. Has anyone made the switch to drip for cotton? What is actual % water saving? Does yield improve significantly? Capital cost vs savings calculation help needed."],
                ['title' => 'Tube-well water with high EC reading causing crop damage — what to do?',
                 'body'  => "Just got my tube-well water tested. Results: EC = 3.8 dS/m, pH = 8.1, Sodium Absorption Ratio (SAR) = 18.5. This is classified as C4-S3 high salinity, high sodicity water. My wheat is showing poor germination and salt crust forming on soil surface. Any practical solutions? Gypsum water amendment possible?"],
            ],
            'farm-equipment' => [
                ['title' => 'Laser land leveling — worth the cost for a 15-acre holding?',
                 'body'  => "Rental cost for laser land leveler is Rs. 3,500-4,000 per acre here in Okara district. My land has uneven surface with slopes. Current water distribution is poor — 25% of field gets too much water. Will one-time leveling be enough or need repeat every 5 years? Return on investment calculation?"],
                ['title' => 'Second-hand combine harvester — important things to check before buying?',
                 'body'  => "I am considering buying a used combine harvester (2015 model, 1,200-1,400 hours) for shared use with 3 farmer neighbors. Total acreage we will harvest together is about 200 acres. What mechanical points should I specifically check? What major breakdowns are common in 1,000+ hour machines?"],
            ],
            'weather-climate' => [
                ['title' => 'Late monsoon this year — how to adjust Kharif crop calendar?',
                 'body'  => "Monsoon rains were delayed by 3 weeks this year compared to normal. My maize crop is already planted and now rain-fed irrigation schedule is off. Should I irrigate now or wait? Also thinking for next year — which crops handle late monsoon better? I am in Gujranwala region."],
                ['title' => 'Heatwave impact on wheat grain filling — can anything be done now?',
                 'body'  => "Temperature hit 42°C last week during grain filling stage of wheat. Forecasters say another heatwave in 5 days. My crop is at medium-milk stage. Should I do an emergency pre-harvest irrigation now even at this late stage? Will foliar antitranspirant spray help at all?"],
            ],
            'market-pricing' => [
                ['title' => 'Government wheat support price vs open market — sell now or store?',
                 'body'  => "PHP (Pakistan's procurement price) for wheat is Rs. 3,900/40kg. Open market is currently Rs. 3,600 but dealers say it will hit Rs. 4,200 by August. My 500 mann of wheat is in kothri (traditional storage). Fumigation cost Rs. 8/mann. Weight loss in 3 months storage is roughly how much? Is it worth waiting?"],
                ['title' => 'Cotton price crash — suggestions for alternative crops after cotton?',
                 'body'  => "Cotton prices are down 30% from last year. Fertilizer and pesticide costs have not reduced. I am thinking of shifting 10 acres from cotton to something else. Options I'm considering: sunflower, maize, or vegetables. Market access in Bahawalpur area — any suggestions based on real farm experience?"],
            ],
            'success-stories' => [
                ['title' => 'How I doubled wheat yield in 3 years using soil testing and precision farming',
                 'body'  => "Sharing my journey from average 35 mann/acre wheat to 68 mann/acre in 3 seasons. Key changes: 1) Annual soil testing and custom fertilizer program 2) Switched to Super Galaxy variety 3) Laser land leveling done once 4) Hired drone for fungicide spray at flag leaf stage 5) Reduced late sowing by moving to October 25-31 window. Anyone wants details on any of these steps?"],
                ['title' => 'From loss-making cotton to profitable vegetable growing — my 5-year transformation',
                 'body'  => "After two back-to-back bad cotton seasons in 2019-2020, I converted 5 acres to tunnel farming for cucumber and tomatoes. First year was very hard — lots of learning. But by year 3 I was making 3x the profit per acre vs cotton. Details on setup cost (~Rs. 180,000/tunnel), market linkage with Lahore commission market, and lessons learned."],
            ],
            'general-discussion' => [
                ['title' => 'Which government agriculture scheme is actually working in your area?',
                 'body'  => "There are many schemes from government: PM Agriculture Transformation Plan, Kissan Portal, Kisan Card. My experience with Kisan Card was good for discounted inputs. Has anyone used the Prime Minister Agriculture Emergency Program for machinery? Share your experience — both positive and negative."],
                ['title' => 'Young farmers discuss: should we stay in farming or go abroad?',
                 'body'  => "I am 24 years old, inheriting 30 acres from my father. I have an engineering degree but agriculture is in my blood. Cost of farming has gone up 3x in 5 years, margins thin, weather unpredictable. My friends are going to Gulf. Those who are young farmers here — what keeps you motivated? Any advice for modern commercial farming as a business?"],
            ],
        ];

        // Expert reply templates per category
        $expertReplies = [
            'crop-management' => [
                "Based on current CCRI (Central Cotton Research Institute) data and my field observations across Punjab, I recommend the following approach: For clay loam soils in Sahiwal, AARI-2011 performs well with proper N management. However, if you've been using the same variety for 3+ years, resistance to common pathogens may develop. I suggest alternating with Punjab-2011 or NARC-2009 in at least 30% of your acreage. Ensure your basal dose of DAP 50kg + Urea 25kg is applied and soil is adequately moist at sowing for optimal germination.",
                "As an agronomy specialist, I can clarify: For cotton in Central Punjab, optimal sowing window is April 25 – May 15. The key determinant is soil temperature at 10cm depth — cotton needs minimum 18°C for consistent germination. Use a soil thermometer to check. Pre-sowing irrigation (rauni) followed by seed bed preparation is recommended over dry sowing to ensure uniform germination. April 20 is generally too early for most cotton varieties.",
            ],
            'pest-disease-control' => [
                "This is an urgent situation that requires immediate action. My professional recommendation: 1) Apply Propiconazole (Tilt 250EC) at 0.1% — 500ml per 200L water per acre within 24 hours. 2) After 10-12 days (not 14), apply Tebuconazole 250EW as second spray. 3) Check to ensure no resistant strains — if stripe patterns are primarily on lower canopy after Tilt application, resistance may be developing. 4) Inspect for temperature — below 15°C supports continued spread. 5) Increase ventilation if possible. Most critically: Act within 48 hours — at 40% infection level, you can still save 60-70% of the crop.",
                "This is Bemisia tabaci resistance to neonicotinoids — a very serious and increasingly common problem. My advice: 1) Immediately rotate to a different chemistry group: Spiromesifen (Oberon SC) or Pyriproxyfen for whitefly. 2) Buprofezin (Applaud 25WP) specifically targets nymph stages. 3) Critical: remove all CLCuD symptomatic plants and bag them before pulling — do not leave them in the field to act as virus reservoir. 4) Consider releasing Encarsia formosa (biological control) from National IPM Programme. Act fast — viral spread accelerates with adult whitefly population.",
                "Based on your description, this is classic Alternaria solani (Early Blight) — the concentric ring pattern and progression from lower to upper leaves is diagnostic. Your Mancozeb 75WP is a good choice as protective spray at 2.5g/L but you need a curative component too. I recommend tank-mixing Mancozeb with Difenoconazole (Score 250EC) at 0.5ml/L. Avoid spraying before rain. Spray early morning when stomata are open. Also: remove and bag infected lower leaves (do not compost them) to reduce inoculum pressure.",
            ],
            'fertilizers-nutrition' => [
                "Great question on DAP vs NP fertilizers. From an agronomic perspective: DAP (18-46-0) has higher phosphorus content, critical for root development and energy transfer. NP 20-20 provides more nitrogen but less phosphorus. For wheat at the basal dose stage, phosphorus is more limiting in most Pakistani soils (typically P-deficient due to high pH fixing P). Therefore, DAP is recommended over NP 20-20 for wheat basal dose. However, if your soil phosphorus is already adequate (>9 ppm Olsen-P), NP 20-20 may be reasonable. Always make decisions based on your soil test results rather than generalized advice.",
                "For rice in alkaline pH soils (7.8), Zinc Sulfate 33% at 5kg/acre soil application or 1% foliar spray is perfectly adequate — the EDTA chelate price premium is NOT justified for soil application at this pH. Chelates are useful mainly in highly acidic soils where Zn is already well-available but leaches quickly. Save your money and use ZnSO4. Application timing: Zinc Sulfate should ideally be applied 7-10 days before transplanting, incorporated into soil. For quick correction, 1kg ZnSO4 in 100L water as foliar spray at tillering stage also works well.",
                "Your dealer is absolutely correct regarding SOP vs MOP for saline soils. Chloride ions (Cl-) in MOP can worsen soil salinity. For cotton on EC 4+ soils, SOP is recommended despite higher cost. Additionally, I recommend: 1) Apply gypsum at 400-500kg/acre and irrigate to leach sodium. 2) Use calcium ammonium nitrate (CAN) instead of urea for nitrogen to improve calcium to sodium balance. 3) Install sub-surface drainage if flooding is feasible for your field. Your current squaring stage is not too late for this nutrition adjustment.",
            ],
            'irrigation-water' => [
                "Excellent question about drip irrigation economics. From my experience advising 200+ farms in Punjab that converted to drip: Water saving for cotton with drip is typically 40-50% compared to furrow. Yield increase in cotton is modest (5-15%) but quality improvement is significant — better boll retention, higher lint quality. Capital cost for drip in cotton (sub-surface for cotton rows) is approximately Rs. 35,000-45,000/acre currently without subsidy. Payback period at 40% water saving (with your current pumping costs) is typically 3-4 years. With 50% government subsidy, payback reduces to 1.5-2 years. I strongly recommend applying for the subsidy before converting.",
                "Your irrigation water has critical salinity issues (C4-S3 class). Immediate recommendations: 1) Gypsum water amendment: add 2kg gypsum per 1000 gallons of irrigation water to reduce SAR. 2) Apply soil gypsum at 400kg/acre and leach with one heavy flood irrigation before sowing next crop. 3) Consider using fresh water (canal water when available) for critical irrigation events (germination, grain fill). 4) Crop selection: wheat, cotton, and sorghum are more salt-tolerant than rice and vegetables. 5) Land leveling to prevent salt accumulation in low spots. This is a recoverable situation with proper management.",
            ],
            'farm-equipment' => [
                "Laser land leveling ROI: at 15 acres, the Rs. 3,500-4,000/acre cost = total Rs. 52,500-60,000 one-time. Benefits to expect: 1) Water saving: 25-30% reduction in irrigation frequency and quantity. 2) Yield improvement: typically 8-12% for wheat due to uniform germination. 3) Weed control improvement: uniform water distribution prevents waterlogged areas where weeds thrive. At Rs. 2,500 savings per acre per season in water cost + Rs. 3,000-4,000 additional yield value, you will recover the cost in approximately 1.5-2 seasons. Yes — absolutely worth it for a 15-acre holding. Repeat leveling typically only needed after 8-10 years.",
                "Critical pre-purchase checks for used combine (1,000-1,400 hours): 1) Cylinder and concave wear — check clearance gap, should be 6-12mm for wheat. 2) Sieves and chaffer condition — bent louvers reduce cleaning efficiency. 3) Header auger wear — look for worn metal causing uneven feeding. 4) Engine oil color — brown is OK, black means worn rings. 5) Hydraulic system — test all functions under load. 6) Grain tank unloading auger — often damaged by stones. 7) Most expensive repairs on 1200-hour machines: cylinder bearings (Rs. 80,000+) and elevator chain replacement (Rs. 45,000+). Always request maintenance logbook.",
            ],
            'weather-climate' => [
                "For maize with delayed monsoon (3 weeks late), current assessment: At germination/early vegetative stage — irrigate once within next 48 hours to not stress crop during cell division phase. Drought stress in first 30 days significantly reduces tiller development and yield potential. However, one supplemental irrigation should be sufficient if monsoon arrives within 2 weeks. For late varieties in future: recommend Hybrid Pioneer 3025 or DK-6525 which have 10-15 days shorter season compared to traditional varieties, giving more flexibility with monsoon timing.",
                "Heatwave during wheat grain fill is critical — here's your action plan: 1) Irrigate immediately if soil moisture below 50% FC (check by hand — soil at 20cm depth should be moist). This is your single most important action — evaporative cooling from soil surface reduces canopy temperature 2-3°C. 2) Antitranspirant (kaolin-based Surround or glycerol 4% spray) can reduce heat absorption on flag leaf — apply evening time. 3) Foliar potassium nitrate at 2% reduces heat stress symptoms. 4) Accept 10-15% yield loss is likely but act now to minimize to 5-8%. Harvest 3-5 days earlier than normal once grain hardens.",
            ],
            'market-pricing' => [
                "Regarding wheat storage decision: Traditional storage (kothri) with proper fumigation is excellent. Your storage economics: Weight loss in 3 months for dry wheat (< 12% moisture): typically 1-2% if ventilated, up to 4% if moisture > 14%. Fumigation at Rs. 8/mann every 6 weeks = Rs. 32-40/mann total for 3 months. If market goes from Rs. 3,600 to Rs. 4,200, this is Rs. 600 gain per 40kg = Rs. 15/mann improvement. After deducting fumigation and weight loss costs, net gain is approximately Rs. 800-1,000 per mann. For 500 mann of quality wheat, this may be worthwhile. Key risk: if government flood market with imported wheat, prices may not rise as predicted.",
            ],
            'success-stories' => [
                "Congratulations on this remarkable transformation — from 35 to 68 mann/acre is exceptional and your methodology is sound. Your drone fungicide application point deserves emphasis: aerial application at flag leaf stage catches the Puccinia striiformis infection window perfectly when ground equipment would cause mechanical damage from wheels. I have documented 12-15% higher yield in farms using drone fungicide vs ground spray at this stage. Would you be willing to mentor other farmers in a community demonstration? We could coordinate through our expert network.",
            ],
            'general-discussion' => [
                "I want to address the young farmer's dilemma directly: Modern commercial agriculture in Pakistan can be profitable but requires treating it as a business, not a tradition. Key mindset shifts needed: 1) Record keeping — every input cost, every yield, every sale. 2) Market research before sowing — not after harvest. 3) Value addition — even basic cleaning and grading wheat doubles marketability. 4) Technology — soil testing, drone spraying, laser leveling are not luxuries but necessities for competitiveness. Your engineering degree is actually an advantage — precision agriculture, IoT sensors, supply chain management are all becoming relevant. My recommendation: don't choose between engineering and farming — combine them.",
            ],
            'weather-climate' => [
                "This is an important seasonal planning topic. Based on Pakistan Meteorological Department data and my agricultural forecasting experience: For Gujranwala and adjacent areas, a 3-week delayed monsoon typically means the Kharif season effectively shortens by 2-3 weeks. Practical adjustments: 1) Maize — switch to short-duration hybrids (75-80 days vs standard 95 days). 2) Rice — consider transplanting delay limit is 15 July; beyond this consider short-duration varieties only. 3) Cotton — 3-week delay is within acceptable range, cotton will benefit from monsoon rains at boll-forming stage. 4) Kharif vegetables — cucurbits and okra can still be successfully grown with compressed schedule.",
            ],
        ];

        // Regular farmer reply templates
        $farmerReplies = [
            "Mere paas bhi yahi masla tha pichle saal. Maine jo kiya woh aapke liye bhi helpful ho sakta hai. Multan area mein meine same spray used ki thi aur kaam aya. Lekin exact dose ka dhyan rakhein.",
            "Thanks for sharing this detailed information. I am going to try this approach on my 10-acre plot next season. Can you recommend any local dealer in Faisalabad who stocks these specific products?",
            "Zabardast post hai bhai! Main bhi is mushkil se guzra hoon. Aap ka jo solution hai woh bilkul sahi lagta hai. Aglay season main zaroor try karoon ga.",
            "Mujhe bhi yahi problem thi. Government se subsidy lene ki poori process kya hai? Kahan apply karna parta hai aur kuch months lagty hain kya?",
            "I have 25 acres in Sahiwal. This is exactly what I needed to read today. Going to implement the soil testing approach starting this Rabi season.",
            "Can someone share a contact number for getting soil testing done in Rahim Yar Khan district? District agriculture office — do they do it free or is there a fee?",
            "Very informative discussion. I shared this thread with my farming WhatsApp group. 40 farmers in our village are interested in this approach.",
            "Ek sawaal hai — kya yeh technique chhoti zameen (2 acres) pe bhi usi tarah kaam karti hai? Ya sirf bari zameen ke liye hai?",
            "Shukriya expert sahib ke liye itni detail advice. Hm jaise kisan apne aap se yeh sab nahi soch sakte. App ki baat sun ke hosla barhta hai.",
            "Bhai mera to experience bilkul mختلف raha. Usi fertilizer se meri fasal kharab hoi thi. Shayad meri zameen ka pH mختلف tha. Soil testing zaroor karwao pehlay.",
        ];

        $threads    = [];
        $replies    = [];
        $expertResp = [];

        $categoryMap = $categories->keyBy('slug');
        $farmerList  = $farmers->values();
        $expertList  = $expertRows->values();

        $slugKeys = array_keys($threadData);

        foreach ($slugKeys as $slug) {
            $category = $categoryMap->get($slug);
            if (!$category) {
                continue;
            }

            foreach ($threadData[$slug] as $ti => $tpl) {
                $farmer = $farmerList[($ti + strlen($slug)) % $farmerList->count()];
                $createdAt = Carbon::now()->subDays(rand(10, 180));

                $threads[] = [
                    'user_id'           => $farmer->id,
                    'forum_category_id' => $category->id,
                    'title'             => $tpl['title'],
                    'body'              => $tpl['body'],
                    'is_pinned'         => ($ti === 0), // Pin first thread per category
                    'is_locked'         => false,
                    'is_approved'       => true,
                    'views'             => rand(45, 2800),
                    'created_at'        => $createdAt,
                    'updated_at'        => Carbon::now()->subDays(rand(0, 9)),
                ];
            }
        }

        foreach (array_chunk($threads, 50) as $chunk) {
            DB::table('forum_threads')->insert($chunk);
        }

        // Load inserted threads
        $insertedThreads = DB::table('forum_threads')->orderBy('id')->get();

        $expertRepliesData = [];
        foreach ($slugKeys as $slug) {
            $data = $expertReplies[$slug] ?? [];
            foreach ($data as $reply) {
                $expertRepliesData[] = ['slug' => $slug, 'reply' => $reply];
            }
        }

        $expertReplyIndex = 0;

        foreach ($insertedThreads as $ti => $thread) {
            $numReplies = rand(3, 6);

            // Farmer replies
            for ($r = 0; $r < $numReplies; $r++) {
                $rFarmer    = $farmerList[($ti + $r + 1) % $farmerList->count()];
                $replyAt    = Carbon::parse($thread->created_at)->addHours(rand(2, 72 * ($r + 1)));
                $isExpert   = false;
                $expertId   = null;

                $replies[] = [
                    'thread_id'       => $thread->id,
                    'user_id'         => $rFarmer->id,
                    'body'            => $farmerReplies[($ti + $r) % count($farmerReplies)],
                    'is_approved'     => true,
                    'is_expert_reply' => false,
                    'expert_id'       => null,
                    'created_at'      => $replyAt,
                    'updated_at'      => $replyAt,
                ];
            }

            // Add one expert reply per thread (using matching slug if available)
            if ($expertList->isNotEmpty()) {
                $expert = $expertList[$ti % $expertList->count()];
                $expertUser = DB::table('users')->where('id', $expert->user_id)->first(['id']);

                // Find a relevant expert reply body
                $slugForThread = null;
                foreach ($slugKeys as $skey) {
                    $cat = $categoryMap->get($skey);
                    if ($cat && $thread->forum_category_id == $cat->id) {
                        $slugForThread = $skey;
                        break;
                    }
                }
                $expertReplySet = $expertReplies[$slugForThread] ?? [];
                $expertBody     = !empty($expertReplySet)
                    ? $expertReplySet[$expertReplyIndex % count($expertReplySet)]
                    : "Based on my expertise in Pakistani agriculture, I recommend conducting a proper soil test first. The symptoms described suggest nutrient deficiency or pathogen stress. Please provide GPS location for region-specific advice. In the meantime, a balanced foliar micronutrient spray can help stabilize the crop while diagnosis is confirmed.";

                $expertReplyAt = Carbon::parse($thread->created_at)->addHours(rand(4, 48));
                $expertReplyIndex++;

                $replies[] = [
                    'thread_id'       => $thread->id,
                    'user_id'         => $expertUser ? $expertUser->id : $expert->user_id,
                    'body'            => $expertBody,
                    'is_approved'     => true,
                    'is_expert_reply' => true,
                    'expert_id'       => $expert->id,
                    'created_at'      => $expertReplyAt,
                    'updated_at'      => $expertReplyAt,
                ];
            }
        }

        foreach (array_chunk($replies, 100) as $chunk) {
            DB::table('forum_replies')->insert($chunk);
        }

        // forum_expert_responses — link expert replies
        $expertReplyRows = DB::table('forum_replies')
            ->where('is_expert_reply', true)
            ->get(['id', 'expert_id']);

        foreach ($expertReplyRows as $er) {
            $expertResp[] = [
                'forum_reply_id'  => $er->id,
                'expert_id'       => $er->expert_id,
                'is_expert_advice' => true,
                'recommendation'  => 'Please follow the detailed protocol in my reply above. Monitor crop for 5-7 days and report back if symptoms persist. For urgent consultations, book an appointment through the Plantix Expert portal.',
                'helpful_votes'   => rand(3, 45),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        foreach (array_chunk($expertResp, 50) as $chunk) {
            DB::table('forum_expert_responses')->insertOrIgnore($chunk);
        }

        $this->command->info(sprintf(
            'ForumSeeder: %d threads, %d replies (%d expert), %d expert_response records seeded.',
            count($threads),
            count($replies),
            count($expertResp),
            count($expertResp)
        ));
    }
}
