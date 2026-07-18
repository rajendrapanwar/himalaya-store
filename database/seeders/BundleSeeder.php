<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BundleSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = [
            [
                'name' => 'Uttarakhand Breakfast Gift Box',
                'slug' => 'uttarakhand-breakfast-gift-box',
                'description' => 'A warm start from the hills with raw forest honey, fragrant herbal tea, stone-milled mandua and an orchard apple preserve. A thoughtful housewarming or family gift.',
                'price' => 1099,
                'image' => 'wild-forest-honey.webp',
                'products' => ['UK-HON-001' => 1, 'UK-TEA-001' => 1, 'UK-GRN-001' => 1, 'HP-PRV-001' => 1],
            ],
            [
                'name' => 'Mountain Tea Discovery Box',
                'slug' => 'mountain-tea-discovery-box',
                'description' => 'Four distinctive caffeine-free and whole-leaf infusions for slow mornings and quiet evenings: tulsi lemongrass, chamomile, buransh and Kumaon green tea.',
                'price' => 1149,
                'image' => 'himalayan-green-tea.webp',
                'products' => ['UK-TEA-001' => 1, 'UK-TEA-003' => 1, 'UK-TEA-005' => 1, 'UK-TEA-006' => 1],
            ],
            [
                'name' => 'Traditional Pahadi Dal Box',
                'slug' => 'traditional-pahadi-dal-box',
                'description' => 'An everyday mountain pantry of Kumaoni bhatt, Garhwali gahat, Munsiyari rajma and Uttarakhand red rice—selected for comforting dals and home-style meals.',
                'price' => 949,
                'image' => 'munsiyari-rajma.webp',
                'products' => ['UK-PLS-001' => 1, 'UK-PLS-002' => 1, 'UK-PLS-003' => 1, 'UK-GRN-002' => 1],
            ],
            [
                'name' => 'Himalayan Spice Kitchen Box',
                'slug' => 'himalayan-spice-kitchen-box',
                'description' => 'Aromatic hill-grown essentials for tadka, curries and chutneys, bringing together jakhya, turmeric, tejpatta, ginger and whole mountain chilli.',
                'price' => 899,
                'image' => 'jakhya-spice.webp',
                'products' => ['UK-SPC-001' => 1, 'UK-SPC-002' => 1, 'UK-SPC-003' => 1, 'UK-SPC-005' => 1, 'HP-SPC-002' => 1],
            ],
            [
                'name' => 'Himalayan Orchard Celebration Box',
                'slug' => 'himalayan-orchard-celebration-box',
                'description' => 'A generous orchard collection of Kinnaur walnuts, Himachali almonds, sun-dried apricots and tangy dried plums for celebrations, hosts and festive sharing.',
                'price' => 1799,
                'image' => 'mixed-dry-fruits.webp',
                'products' => ['HP-DRY-001' => 1, 'HP-DRY-002' => 1, 'HP-DRY-003' => 1, 'HP-DRY-004' => 1],
            ],
            [
                'name' => 'Honey and Herbal Wellness Box',
                'slug' => 'honey-and-herbal-wellness-box',
                'description' => 'Raw Himalayan honey paired with chamomile, nettle leaf and Brahmi mint infusions—a calming wellness gift for tea lovers and mindful routines.',
                'price' => 1199,
                'image' => 'chamomile-tea.webp',
                'products' => ['UK-HON-001' => 1, 'UK-TEA-002' => 1, 'UK-TEA-003' => 1, 'UK-TEA-004' => 1],
            ],
            [
                'name' => 'Taste of Uttarakhand Grand Box',
                'slug' => 'taste-of-uttarakhand-grand-box',
                'description' => 'A broad introduction to Uttarakhand with forest honey, Munsiyari rajma, gahat, jhangora, hill lemon pickle and floral buransh squash.',
                'price' => 1499,
                'image' => 'himalayan-collection-v2.webp',
                'image_directory' => 'hero',
                'products' => ['UK-HON-001' => 1, 'UK-PLS-002' => 1, 'UK-PLS-003' => 1, 'UK-GRN-003' => 1, 'UK-PRV-001' => 1, 'UK-PRV-003' => 1],
            ],
            [
                'name' => 'Flavours of Himachal Pantry Box',
                'slug' => 'flavours-of-himachal-pantry-box',
                'description' => 'A Himachali pantry journey featuring Kinnauri rajma, aromatic kala jeera, Lahaul buckwheat, apple cinnamon preserve and sun-dried apricots.',
                'price' => 1499,
                'image' => 'kinnauri-rajma.webp',
                'products' => ['HP-PLS-001' => 1, 'HP-SPC-001' => 1, 'HP-GRN-002' => 1, 'HP-PRV-001' => 1, 'HP-DRY-003' => 1],
            ],
            [
                'name' => 'Premium Himalayan Festive Hamper',
                'slug' => 'premium-himalayan-festive-hamper',
                'description' => 'A premium festive hamper with prized chilgoza, Himachali almonds, Kinnaur walnuts, raw forest honey and apple cinnamon preserve.',
                'price' => 2599,
                'image' => 'chilgoza-pine-nuts.webp',
                'products' => ['HP-DRY-005' => 1, 'HP-DRY-002' => 1, 'HP-DRY-001' => 1, 'UK-HON-001' => 1, 'HP-PRV-001' => 1],
            ],
            [
                'name' => 'Pahadi Home and Host Gift Box',
                'slug' => 'pahadi-home-and-host-gift-box',
                'description' => 'A useful host gift combining a handwoven ringal basket, woollen coasters, raw forest honey and tulsi lemongrass tea from the hills.',
                'price' => 1599,
                'image' => 'ringal-basket.webp',
                'products' => ['UK-CRT-001' => 1, 'UK-CRT-002' => 1, 'UK-HON-001' => 1, 'UK-TEA-001' => 1],
            ],
            [
                'name' => 'Hill Preserves and Pickles Box',
                'slug' => 'hill-preserves-and-pickles-box',
                'description' => 'A bright assortment of buransh squash, apple and apricot preserves, Garhwali lingda pickle and bold pahadi nimbu pickle.',
                'price' => 1199,
                'image' => 'apple-preserve.webp',
                'products' => ['UK-PRV-001' => 1, 'HP-PRV-001' => 1, 'HP-PRV-002' => 1, 'UK-PRV-002' => 1, 'UK-PRV-003' => 1],
            ],
            [
                'name' => 'Ancient Himalayan Grains Box',
                'slug' => 'ancient-himalayan-grains-box',
                'description' => 'A versatile whole-grain collection with red rice, jhangora, ramdana, buckwheat and mandua for nourishing everyday mountain-inspired cooking.',
                'price' => 949,
                'image' => 'jhangora-millet.webp',
                'products' => ['UK-GRN-002' => 1, 'UK-GRN-003' => 1, 'HP-GRN-001' => 1, 'HP-GRN-002' => 1, 'UK-GRN-001' => 1],
            ],
        ];

        $products = Product::whereIn('sku', collect($bundles)->flatMap(fn (array $bundle) => array_keys($bundle['products']))->unique())
            ->get()->keyBy('sku');

        foreach ($bundles as $definition) {
            $imageDirectory = $definition['image_directory'] ?? 'products';
            $imagePath = 'bundles/catalogue/'.$definition['slug'].'.webp';
            $source = public_path("assets/img/{$imageDirectory}/{$definition['image']}");
            Storage::disk('public')->put($imagePath, file_get_contents($source));

            $bundle = Bundle::updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'price' => $definition['price'],
                    'image' => $imagePath,
                    'is_active' => true,
                ],
            );

            $bundle->products()->sync(collect($definition['products'])->mapWithKeys(function (int $quantity, string $sku) use ($products): array {
                $product = $products->get($sku);

                return $product ? [$product->id => ['quantity' => $quantity]] : [];
            })->all());
        }
    }
}
