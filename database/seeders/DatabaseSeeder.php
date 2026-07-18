<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ServiceablePincode;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(JournalPostSeeder::class);

        // User::factory(10)->create();

        $adminEmail = strtolower(env('ADMIN_EMAIL', 'admin@example.com'));
        $legacyAdmin = User::where('email', 'admin@himalayastore.test')->where('role', 'administrator')->first();
        if ($legacyAdmin && ! User::where('email', $adminEmail)->exists()) {
            $legacyAdmin->forceFill(['email' => $adminEmail, 'email_verified_at' => now()])->save();
        }
        $administrator = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Rajendra Panwar',
                'password' => Hash::make(env('ADMIN_INITIAL_PASSWORD', 'ChangeMe!123')),
                'is_admin' => true,
                'role' => 'administrator',
                'referral_code' => 'HIMALAYA',
                'email_verified_at' => now(),
            ]
        );
        if ($administrator->name === 'Store Administrator') {
            $administrator->update(['name' => 'Rajendra Panwar']);
        }

        $serviceablePincodes = [
            ['pincode' => '248001', 'city' => 'Dehradun', 'shipping_amount' => 49, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true],
            ['pincode' => '248002', 'city' => 'Dehradun', 'shipping_amount' => 49, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true],
            ['pincode' => '249201', 'city' => 'Rishikesh', 'shipping_amount' => 59, 'estimated_days_min' => 2, 'estimated_days_max' => 4, 'cod_available' => true],
            ['pincode' => '249401', 'city' => 'Haridwar', 'shipping_amount' => 59, 'estimated_days_min' => 2, 'estimated_days_max' => 4, 'cod_available' => true],
            ['pincode' => '263001', 'city' => 'Nainital', 'shipping_amount' => 79, 'estimated_days_min' => 3, 'estimated_days_max' => 6, 'cod_available' => true],
            ['pincode' => '263139', 'city' => 'Haldwani', 'shipping_amount' => 69, 'estimated_days_min' => 2, 'estimated_days_max' => 5, 'cod_available' => true],
            ['pincode' => '246001', 'city' => 'Pauri', 'shipping_amount' => 89, 'estimated_days_min' => 3, 'estimated_days_max' => 7, 'cod_available' => false],
            ['pincode' => '263601', 'city' => 'Almora', 'shipping_amount' => 89, 'estimated_days_min' => 3, 'estimated_days_max' => 7, 'cod_available' => false],
        ];
        foreach ($serviceablePincodes as $pincode) {
            ServiceablePincode::updateOrCreate(['pincode' => $pincode['pincode']], $pincode + ['state' => 'Uttarakhand', 'is_active' => true]);
        }

        $catalogue = [
            'honey' => [
                'name' => 'Wild Himalayan Honey',
                'description' => 'Raw, unprocessed honey collected from the forests of Uttarakhand.',
                'products' => [
                    ['name' => 'Raw Wild Forest Honey', 'price' => 499, 'sale_price' => 449, 'sku' => 'UK-HON-001', 'stock' => 40, 'weight' => 500, 'featured' => true],
                    ['name' => 'Himalayan Multiflora Honey', 'price' => 399, 'sale_price' => null, 'sku' => 'UK-HON-002', 'stock' => 35, 'weight' => 350, 'featured' => true],
                ],
            ],
            'herbal-teas' => [
                'name' => 'Herbal Teas',
                'description' => 'Naturally fragrant herbal blends from the mountain slopes.',
                'products' => [
                    ['name' => 'Tulsi Lemongrass Herbal Tea', 'price' => 299, 'sale_price' => null, 'sku' => 'UK-TEA-001', 'stock' => 50, 'weight' => 100, 'featured' => true],
                    ['name' => 'Brahmi Mint Wellness Tea', 'price' => 329, 'sale_price' => 299, 'sku' => 'UK-TEA-002', 'stock' => 30, 'weight' => 100, 'featured' => false],
                    ['name' => 'Himalayan Chamomile Tea', 'description' => 'Whole chamomile flowers with a soft floral aroma, packed as a naturally caffeine-free evening infusion.', 'price' => 349, 'sale_price' => 319, 'sku' => 'UK-TEA-003', 'stock' => 34, 'weight' => 50, 'featured' => true, 'origin' => 'Mukteshwar, Uttarakhand', 'producer' => 'Kumaon herb grower collective', 'ingredients' => 'Dried chamomile flowers'],
                    ['name' => 'Uttarakhand Nettle Leaf Tea', 'description' => 'Cleaned and shade-dried Himalayan nettle leaves with an earthy, green character for herbal infusions.', 'price' => 299, 'sale_price' => null, 'sku' => 'UK-TEA-004', 'stock' => 28, 'weight' => 50, 'featured' => false, 'origin' => 'Pauri Garhwal, Uttarakhand', 'producer' => 'Garhwal herb grower collective', 'ingredients' => 'Dried Himalayan nettle leaves'],
                    ['name' => 'Buransh Rhododendron Infusion', 'description' => 'A bright, floral tisane made with dried buransh petals inspired by Uttarakhand springtime.', 'price' => 379, 'sale_price' => 349, 'sku' => 'UK-TEA-005', 'stock' => 26, 'weight' => 50, 'featured' => true, 'origin' => 'Chamoli, Uttarakhand', 'producer' => 'Chamoli forest-produce collective', 'ingredients' => 'Dried rhododendron petals'],
                    ['name' => 'Kumaon Himalayan Green Tea', 'description' => 'A clean, brisk whole-leaf green tea grown in the cool Kumaon hills and packed in small batches.', 'price' => 429, 'sale_price' => null, 'sku' => 'UK-TEA-006', 'stock' => 38, 'weight' => 100, 'featured' => true, 'origin' => 'Kausani, Uttarakhand', 'producer' => 'Kumaon tea grower collective', 'ingredients' => 'Whole-leaf green tea'],
                ],
            ],
            'spices' => [
                'name' => 'Mountain Spices',
                'description' => 'Aromatic spices grown and hand-sorted in Uttarakhand.',
                'products' => [
                    ['name' => 'Pahadi Jakhya Seeds', 'price' => 189, 'sale_price' => null, 'sku' => 'UK-SPC-001', 'stock' => 60, 'weight' => 100, 'featured' => true],
                    ['name' => 'Organic Turmeric Powder', 'price' => 249, 'sale_price' => null, 'sku' => 'UK-SPC-002', 'stock' => 45, 'weight' => 200, 'featured' => false],
                    ['name' => 'Uttarakhand Tejpatta Bay Leaf', 'description' => 'Hand-sorted dried tejpatta with warm cinnamon-like notes for dals, rice dishes and slow-cooked curries.', 'price' => 149, 'sale_price' => null, 'sku' => 'UK-SPC-003', 'stock' => 56, 'weight' => 50, 'featured' => false, 'origin' => 'Almora, Uttarakhand', 'producer' => 'Kumaon spice grower collective', 'ingredients' => 'Whole dried tejpatta leaves'],
                    ['name' => 'Himachali Kala Jeera', 'description' => 'Aromatic black cumin selected in the high hills for tempering, breads, potatoes and traditional recipes.', 'price' => 449, 'sale_price' => 419, 'sku' => 'HP-SPC-001', 'stock' => 22, 'weight' => 50, 'featured' => true, 'origin' => 'Kinnaur, Himachal Pradesh', 'producer' => 'Kinnaur spice grower collective', 'ingredients' => 'Whole kala jeera seeds'],
                    ['name' => 'Pahadi Coriander Seeds', 'description' => 'Sun-dried whole coriander seeds with a citrusy aroma, suited to fresh grinding and everyday tadka.', 'price' => 169, 'sale_price' => null, 'sku' => 'UK-SPC-004', 'stock' => 48, 'weight' => 150, 'featured' => false, 'origin' => 'Tehri Garhwal, Uttarakhand', 'producer' => 'Tehri hill-farm collective', 'ingredients' => 'Whole coriander seeds'],
                    ['name' => 'Whole Mountain Red Chilli', 'description' => 'Naturally sun-dried whole red chillies with balanced heat, hand-sorted for chutneys, pickles and tempering.', 'price' => 219, 'sale_price' => null, 'sku' => 'HP-SPC-002', 'stock' => 42, 'weight' => 100, 'featured' => true, 'origin' => 'Chamba, Himachal Pradesh', 'producer' => 'Chamba spice grower collective', 'ingredients' => 'Whole dried red chillies'],
                    ['name' => 'Himalayan Ginger Powder', 'description' => 'Small-batch dried ginger powder with a warming aroma for tea, baking, marinades and everyday cooking.', 'price' => 239, 'sale_price' => 219, 'sku' => 'UK-SPC-005', 'stock' => 44, 'weight' => 100, 'featured' => false, 'origin' => 'Pauri Garhwal, Uttarakhand', 'producer' => 'Garhwal spice grower collective', 'ingredients' => 'Dried ginger powder'],
                ],
            ],
            'mountain-pulses' => [
                'name' => 'Mountain Pulses',
                'description' => 'Traditional beans and pulses grown in rain-fed Himalayan farms.',
                'products' => [
                    ['name' => 'Kumaoni Bhatt Black Soybean', 'description' => 'A traditional Kumaoni black soybean used in bhatt ki churkani and warming dals.', 'price' => 249, 'sale_price' => null, 'sku' => 'UK-PLS-001', 'stock' => 45, 'weight' => 500, 'featured' => true, 'origin' => 'Kumaon, Uttarakhand', 'producer' => 'Kumaon smallholder collective'],
                    ['name' => 'Pahadi Gahat Horse Gram', 'description' => 'Earthy whole gahat, also called kulath, cleaned and sorted for dal and ras preparations.', 'price' => 229, 'sale_price' => 209, 'sku' => 'UK-PLS-002', 'stock' => 40, 'weight' => 500, 'featured' => true, 'origin' => 'Garhwal, Uttarakhand', 'producer' => 'Garhwal smallholder collective'],
                    ['name' => 'Munsiyari Speckled Rajma', 'description' => 'Small-batch speckled kidney beans sourced from the high valleys around Munsiyari.', 'price' => 349, 'sale_price' => 319, 'sku' => 'UK-PLS-003', 'stock' => 32, 'weight' => 500, 'featured' => true, 'origin' => 'Munsiyari, Uttarakhand', 'producer' => 'Johar Valley grower collective'],
                    ['name' => 'Kinnauri Mountain Rajma', 'description' => 'Creamy high-altitude kidney beans selected from Kinnaur for slow-cooked curries.', 'price' => 399, 'sale_price' => null, 'sku' => 'HP-PLS-001', 'stock' => 28, 'weight' => 500, 'featured' => true, 'origin' => 'Kinnaur, Himachal Pradesh', 'producer' => 'Kinnaur grower collective'],
                    ['name' => 'Himachali Desi Chana', 'description' => 'Whole brown gram from Himachal hill farms, suited to curries, sprouts and roasted snacks.', 'price' => 219, 'sale_price' => null, 'sku' => 'HP-PLS-002', 'stock' => 48, 'weight' => 500, 'featured' => false, 'origin' => 'Mandi, Himachal Pradesh', 'producer' => 'Mandi hill-farm collective'],
                    ['name' => 'Spiti Valley Dry Peas', 'description' => 'Whole dry peas from cold-desert farms, ideal for soups, stews and traditional dal.', 'price' => 329, 'sale_price' => null, 'sku' => 'HP-PLS-003', 'stock' => 24, 'weight' => 500, 'featured' => false, 'origin' => 'Spiti Valley, Himachal Pradesh', 'producer' => 'Spiti grower collective'],
                    ['name' => 'Himachali Whole Mash Dal', 'description' => 'Whole black gram traditionally cooked into slow-simmered Himachali dals and madra.', 'price' => 239, 'sale_price' => null, 'sku' => 'HP-PLS-004', 'stock' => 38, 'weight' => 500, 'featured' => false, 'origin' => 'Kangra, Himachal Pradesh', 'producer' => 'Kangra smallholder collective'],
                ],
            ],
            'grains' => [
                'name' => 'Himalayan Grains',
                'description' => 'Wholesome traditional grains from high-altitude farms.',
                'products' => [
                    ['name' => 'Mandua Ragi Flour', 'price' => 179, 'sale_price' => null, 'sku' => 'UK-GRN-001', 'stock' => 55, 'weight' => 1000, 'featured' => true],
                    ['name' => 'Uttarakhand Red Rice', 'price' => 299, 'sale_price' => 269, 'sku' => 'UK-GRN-002', 'stock' => 40, 'weight' => 1000, 'featured' => false],
                    ['name' => 'Uttarakhand Jhangora Millet', 'description' => 'Whole barnyard millet, a light traditional grain used for khichdi, kheer and fasting meals.', 'price' => 199, 'sale_price' => null, 'sku' => 'UK-GRN-003', 'stock' => 52, 'weight' => 500, 'featured' => true, 'origin' => 'Garhwal, Uttarakhand', 'producer' => 'Garhwal millet collective'],
                    ['name' => 'Himalayan Ramdana Amaranth', 'description' => 'Tiny whole amaranth seeds for popping, laddus, porridge and gluten-free flour blends.', 'price' => 229, 'sale_price' => null, 'sku' => 'HP-GRN-001', 'stock' => 42, 'weight' => 500, 'featured' => true, 'origin' => 'Kullu, Himachal Pradesh', 'producer' => 'Kullu hill-farm collective'],
                    ['name' => 'Himachali Kuttu Buckwheat', 'description' => 'High-hill buckwheat groats for flour, porridge and rustic Himalayan preparations.', 'price' => 249, 'sale_price' => 229, 'sku' => 'HP-GRN-002', 'stock' => 36, 'weight' => 500, 'featured' => false, 'origin' => 'Lahaul, Himachal Pradesh', 'producer' => 'Lahaul grower collective'],
                    ['name' => 'Himachali Kangni Foxtail Millet', 'description' => 'A traditional lesser millet with a gentle nutty flavour for pilafs and breakfast bowls.', 'price' => 219, 'sale_price' => null, 'sku' => 'HP-GRN-003', 'stock' => 40, 'weight' => 500, 'featured' => false, 'origin' => 'Sirmaur, Himachal Pradesh', 'producer' => 'Sirmaur millet collective'],
                ],
            ],
            'hill-flours' => [
                'name' => 'Hill Flours',
                'description' => 'Freshly milled traditional flours for everyday Himalayan cooking.',
                'products' => [
                    ['name' => 'Jhangora Barnyard Millet Flour', 'description' => 'Finely milled barnyard millet flour for rotis, pancakes, porridge and fasting recipes.', 'price' => 199, 'sale_price' => null, 'sku' => 'UK-FLR-001', 'stock' => 44, 'weight' => 500, 'featured' => true, 'origin' => 'Garhwal, Uttarakhand', 'producer' => 'Garhwal millet collective', 'ingredients' => 'Barnyard millet flour'],
                    ['name' => 'Ramdana Amaranth Flour', 'description' => 'Stone-milled amaranth flour with a gently nutty flavour for rotis, laddus and baking blends.', 'price' => 229, 'sale_price' => 209, 'sku' => 'UK-FLR-002', 'stock' => 40, 'weight' => 500, 'featured' => false, 'origin' => 'Chamoli, Uttarakhand', 'producer' => 'Chamoli millet collective', 'ingredients' => 'Amaranth flour'],
                    ['name' => 'Himachali Kuttu Flour', 'description' => 'High-hill buckwheat milled in small batches for pancakes, breads and traditional fasting meals.', 'price' => 249, 'sale_price' => null, 'sku' => 'HP-FLR-001', 'stock' => 35, 'weight' => 500, 'featured' => true, 'origin' => 'Lahaul, Himachal Pradesh', 'producer' => 'Lahaul grower collective', 'ingredients' => 'Buckwheat flour'],
                    ['name' => 'Stoneground Pahadi Maize Flour', 'description' => 'Whole maize slowly stoneground for rustic makki rotis, porridges and savoury bakes.', 'price' => 189, 'sale_price' => null, 'sku' => 'HP-FLR-002', 'stock' => 48, 'weight' => 1000, 'featured' => false, 'origin' => 'Mandi, Himachal Pradesh', 'producer' => 'Mandi hill-farm collective', 'ingredients' => 'Whole maize flour'],
                    ['name' => 'Himalayan Barley Flour', 'description' => 'Whole-grain barley flour with a mild toasted character for rotis, sattu blends and hearty baking.', 'price' => 199, 'sale_price' => 179, 'sku' => 'HP-FLR-003', 'stock' => 41, 'weight' => 500, 'featured' => false, 'origin' => 'Kullu, Himachal Pradesh', 'producer' => 'Kullu grain grower collective', 'ingredients' => 'Whole barley flour'],
                ],
            ],
            'dry-fruits-seeds' => [
                'name' => 'Dry Fruits & Nuts',
                'description' => 'Naturally rich nuts and sun-dried orchard fruit from Himalayan valleys.',
                'products' => [
                    ['name' => 'Kinnaur Inshell Walnuts', 'description' => 'Premium inshell walnuts from high-altitude orchards, naturally dried and carefully graded.', 'price' => 599, 'sale_price' => 549, 'sku' => 'HP-DRY-001', 'stock' => 30, 'weight' => 500, 'featured' => true, 'origin' => 'Kinnaur, Himachal Pradesh', 'producer' => 'Kinnaur orchard collective', 'ingredients' => 'Inshell walnuts'],
                    ['name' => 'Himachali Almonds', 'description' => 'Crunchy whole almonds selected from Himachal orchards for snacking, breakfast and gifting.', 'price' => 749, 'sale_price' => null, 'sku' => 'HP-DRY-002', 'stock' => 27, 'weight' => 500, 'featured' => true, 'origin' => 'Kinnaur, Himachal Pradesh', 'producer' => 'Kinnaur orchard collective', 'ingredients' => 'Whole almonds'],
                    ['name' => 'Sun-Dried Himalayan Apricots', 'description' => 'Naturally sweet dried apricots with no added colour, made for trail snacks and breakfast bowls.', 'price' => 399, 'sale_price' => 369, 'sku' => 'HP-DRY-003', 'stock' => 34, 'weight' => 250, 'featured' => true, 'origin' => 'Lahaul-Spiti, Himachal Pradesh', 'producer' => 'Lahaul orchard collective', 'ingredients' => 'Sun-dried apricots'],
                    ['name' => 'Himachali Dried Plums', 'description' => 'Tangy-sweet dried plums prepared in small seasonal batches from hill-grown fruit.', 'price' => 349, 'sale_price' => null, 'sku' => 'HP-DRY-004', 'stock' => 29, 'weight' => 250, 'featured' => false, 'origin' => 'Kullu, Himachal Pradesh', 'producer' => 'Kullu orchard collective', 'ingredients' => 'Dried plums'],
                    ['name' => 'Chamba Chilgoza Pine Nuts', 'description' => 'A prized high-altitude pine nut with a delicate buttery taste, packed in a compact premium pouch.', 'price' => 899, 'sale_price' => null, 'sku' => 'HP-DRY-005', 'stock' => 16, 'weight' => 100, 'featured' => true, 'origin' => 'Chamba, Himachal Pradesh', 'producer' => 'Chamba forest-produce collective', 'ingredients' => 'Chilgoza pine nuts'],
                    ['name' => 'Himalayan Orchard Mix', 'description' => 'A gift-ready mix of walnuts, almonds, dried apricots and dried plums from Himalayan orchards.', 'price' => 649, 'sale_price' => 599, 'sku' => 'HP-DRY-006', 'stock' => 24, 'weight' => 400, 'featured' => false, 'origin' => 'Himachal Pradesh', 'producer' => 'Himalayan orchard collective', 'ingredients' => 'Walnuts, almonds, dried apricots and dried plums'],
                ],
            ],
            'preserves-pickles' => [
                'name' => 'Preserves & Pickles',
                'description' => 'Small-batch fruit preserves, floral drinks and spirited hill pickles.',
                'products' => [
                    ['name' => 'Buransh Rhododendron Squash', 'description' => 'A floral Uttarakhand summer drink concentrate inspired by hand-harvested buransh blossoms.', 'price' => 329, 'sale_price' => 299, 'sku' => 'UK-PRV-001', 'stock' => 38, 'weight' => 750, 'featured' => true, 'origin' => 'Chamoli, Uttarakhand', 'producer' => 'Chamoli forest-produce collective', 'ingredients' => 'Rhododendron petal extract, cane sugar, water and lemon juice'],
                    ['name' => 'Kinnaur Apple Cinnamon Preserve', 'description' => 'Orchard apples slow-cooked with cane sugar and cinnamon for toast, yoghurt and desserts.', 'price' => 299, 'sale_price' => null, 'sku' => 'HP-PRV-001', 'stock' => 42, 'weight' => 300, 'featured' => true, 'origin' => 'Kinnaur, Himachal Pradesh', 'producer' => 'Kinnaur orchard collective', 'ingredients' => 'Apple, cane sugar, lemon juice and cinnamon'],
                    ['name' => 'Himachali Apricot Preserve', 'description' => 'A bright, fruit-forward preserve made in small batches with ripe Himalayan apricots.', 'price' => 319, 'sale_price' => 289, 'sku' => 'HP-PRV-002', 'stock' => 35, 'weight' => 300, 'featured' => false, 'origin' => 'Lahaul, Himachal Pradesh', 'producer' => 'Lahaul orchard collective', 'ingredients' => 'Apricot, cane sugar and lemon juice'],
                    ['name' => 'Himalayan Plum Preserve', 'description' => 'Tangy hill plums gently cooked into a richly coloured spread with a clean fruit finish.', 'price' => 299, 'sale_price' => null, 'sku' => 'HP-PRV-003', 'stock' => 33, 'weight' => 300, 'featured' => false, 'origin' => 'Kullu, Himachal Pradesh', 'producer' => 'Kullu orchard collective', 'ingredients' => 'Plum, cane sugar and lemon juice'],
                    ['name' => 'Garhwali Lingda Pickle', 'description' => 'A distinctive fiddlehead fern pickle seasoned in the Garhwali style for dal, rice and parathas.', 'price' => 279, 'sale_price' => 259, 'sku' => 'UK-PRV-002', 'stock' => 31, 'weight' => 250, 'featured' => true, 'origin' => 'Pauri Garhwal, Uttarakhand', 'producer' => 'Garhwal women\'s food collective', 'ingredients' => 'Lingda fern, mustard oil, salt and spices'],
                    ['name' => 'Pahadi Nimbu Pickle', 'description' => 'Chunky hill lemon pickle matured with mustard oil and aromatic spices for a bold, tangy bite.', 'price' => 249, 'sale_price' => null, 'sku' => 'UK-PRV-003', 'stock' => 39, 'weight' => 300, 'featured' => false, 'origin' => 'Almora, Uttarakhand', 'producer' => 'Kumaon women\'s food collective', 'ingredients' => 'Hill lemon, mustard oil, salt and spices'],
                ],
            ],
            'handmade-crafts' => [
                'name' => 'Handmade Crafts',
                'description' => 'Useful, beautiful goods made by local Uttarakhand artisans.',
                'products' => [
                    ['name' => 'Handwoven Ringal Bamboo Basket', 'price' => 699, 'sale_price' => null, 'sku' => 'UK-CRT-001', 'stock' => 15, 'weight' => 450, 'featured' => true],
                    ['name' => 'Woollen Pahadi Coaster Set', 'price' => 349, 'sale_price' => null, 'sku' => 'UK-CRT-002', 'stock' => 25, 'weight' => 200, 'featured' => false],
                ],
            ],
        ];

        $productArtwork = [
            'UK-HON-001' => 'wild-forest-honey.webp',
            'UK-HON-002' => 'multiflora-honey.jpg',
            'UK-TEA-001' => 'tulsi-tea.jpg',
            'UK-TEA-002' => 'brahmi-tea.jpg',
            'UK-TEA-003' => 'chamomile-tea.jpg',
            'UK-TEA-004' => 'nettle-tea.jpg',
            'UK-TEA-005' => 'buransh-infusion.jpg',
            'UK-TEA-006' => 'himalayan-green-tea.jpg',
            'UK-SPC-001' => 'jakhya-spice.jpg',
            'UK-SPC-002' => 'turmeric.jpg',
            'UK-SPC-003' => 'tejpatta.jpg',
            'HP-SPC-001' => 'jakhya-spice.jpg',
            'UK-SPC-004' => 'jakhya-spice.jpg',
            'HP-SPC-002' => 'whole-red-chilli.jpg',
            'UK-SPC-005' => 'ginger-powder.jpg',
            'UK-PLS-001' => 'bhatt-black-soybean.jpg',
            'UK-PLS-002' => 'gahat-horse-gram.jpg',
            'UK-PLS-003' => 'munsiyari-rajma.jpg',
            'HP-PLS-001' => 'kinnauri-rajma.jpg',
            'HP-PLS-002' => 'himachali-chana.jpg',
            'HP-PLS-003' => 'spiti-peas.jpg',
            'HP-PLS-004' => 'himachali-mash.jpg',
            'UK-GRN-001' => 'ragi-grain.jpg',
            'UK-GRN-002' => 'red-rice.jpg',
            'UK-GRN-003' => 'jhangora-millet.jpg',
            'HP-GRN-001' => 'ramdana-amaranth.jpg',
            'HP-GRN-002' => 'kuttu-buckwheat.jpg',
            'HP-GRN-003' => 'jhangora-millet.jpg',
            'UK-FLR-001' => 'jhangora-millet.jpg',
            'UK-FLR-002' => 'ramdana-amaranth.jpg',
            'HP-FLR-001' => 'kuttu-buckwheat.jpg',
            'HP-FLR-002' => 'maize-flour.jpg',
            'HP-FLR-003' => 'jhangora-millet.jpg',
            'HP-DRY-001' => 'kinnaur-walnuts.jpg',
            'HP-DRY-002' => 'himachali-almonds.jpg',
            'HP-DRY-003' => 'dried-apricots.jpg',
            'HP-DRY-004' => 'dried-plums.jpg',
            'HP-DRY-005' => 'chilgoza-pine-nuts.jpg',
            'HP-DRY-006' => 'mixed-dry-fruits.jpg',
            'UK-PRV-001' => 'buransh-squash.jpg',
            'HP-PRV-001' => 'apple-preserve.jpg',
            'HP-PRV-002' => 'apricot-preserve.jpg',
            'HP-PRV-003' => 'plum-preserve.jpg',
            'UK-PRV-002' => 'lingda-pickle.jpg',
            'UK-PRV-003' => 'nimbu-pickle.jpg',
            'UK-CRT-001' => 'ringal-basket.jpg',
            'UK-CRT-002' => 'wool-craft.jpg',
        ];

        $productArtwork = array_map(function (string $artwork): string {
            $webp = pathinfo($artwork, PATHINFO_FILENAME).'.webp';

            return file_exists(public_path("assets/img/products/{$webp}")) ? $webp : $artwork;
        }, $productArtwork);

        foreach ($catalogue as $slug => $categoryData) {
            $categoryArtwork = $productArtwork[$categoryData['products'][0]['sku']];
            $categoryArtworkPath = "categories/catalogue/{$slug}.".pathinfo($categoryArtwork, PATHINFO_EXTENSION);
            $categoryArtworkSource = public_path("assets/img/products/{$categoryArtwork}");
            Storage::disk('public')->put($categoryArtworkPath, file_get_contents($categoryArtworkSource));
            $category = Category::updateOrCreate(['slug' => $slug], [
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'status' => true,
                'image' => $categoryArtworkPath,
            ]);

            foreach ($categoryData['products'] as $item) {
                $artworkFile = $productArtwork[$item['sku']];
                $artworkPath = "products/catalogue/{$artworkFile}";
                $artworkSource = public_path("assets/img/products/{$artworkFile}");
                Storage::disk('public')->put($artworkPath, file_get_contents($artworkSource));
                $product = Product::updateOrCreate(['sku' => $item['sku']], [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'short_description' => $item['description'] ?? $categoryData['description'],
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'stock_quantity' => $item['stock'],
                    'weight' => $item['weight'],
                    'origin_region' => $item['origin'] ?? 'Uttarakhand',
                    'producer_name' => $item['producer'] ?? null,
                    'producer_location' => $item['origin'] ?? 'Uttarakhand',
                    'ingredients' => $item['ingredients'] ?? $item['name'],
                    'is_featured' => $item['featured'],
                    'status' => true,
                ]);
                if (pathinfo($artworkFile, PATHINFO_EXTENSION) === 'webp') {
                    $legacyArtworkPath = 'products/catalogue/'.pathinfo($artworkFile, PATHINFO_FILENAME).'.jpg';
                    $product->images()->where('image', $legacyArtworkPath)->delete();
                    Storage::disk('public')->delete($legacyArtworkPath);
                }
                $product->images()->update(['is_main' => false]);
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'image' => $artworkPath],
                    ['is_main' => true, 'sort_order' => 0]
                );
            }
        }

        $this->call(BundleSeeder::class);
    }
}
