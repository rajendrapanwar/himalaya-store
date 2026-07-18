<?php

namespace Database\Seeders;

use App\Models\JournalPost;
use Illuminate\Database\Seeder;

class JournalPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'The Story Behind Himalayan Honey',
                'slug' => 'the-story-behind-himalayan-honey',
                'category' => 'Ingredients',
                'excerpt' => 'How flowering seasons, origin and careful handling shape the colour, aroma and character of every jar.',
                'featured_image' => 'assets/img/products/wild-forest-honey.webp',
                'image_alt' => 'A jar of amber Himalayan forest honey with a wooden honey dipper',
                'is_featured' => true,
                'published_at' => '2026-07-05 09:00:00',
                'seo_title' => 'Himalayan Honey: Origin, Taste and Storage Guide',
                'meta_description' => 'Learn how origin, flowering seasons and careful handling shape Himalayan honey, plus practical advice for storage, crystallisation and everyday use.',
                'content' => <<<'TEXT'
## A landscape that changes with the season

Forest edges, orchards, terraces and village farms give bees a changing mix of blossoms across Uttarakhand. A multiflora honey reflects both a place and a period rather than one fixed flavour. Rainfall, temperature and the plants in bloom may influence its aroma, colour and texture.

Natural variation is part of the product. Two harvests sold under the same broad name can look and taste different without either being inferior. Origin and batch information help explain those differences and make the source more visible.

## What “raw” should tell a shopper

The word raw is most useful when it comes with clear handling information. Look for details about straining, warming during packing and whether anything has been added. A short ingredient list and a named source are more informative than a claim on the front label alone.

Crystallisation is a normal physical change in many honeys. Its speed depends partly on the natural balance of sugars and the storage temperature. It is not, by itself, evidence that honey has spoiled or been adulterated.

## Storing and using honey well

Keep the jar tightly closed in a cool, dry cupboard and use a clean, dry spoon. If crystallised honey needs softening, place the closed jar in comfortably warm water. Avoid boiling it or allowing water to enter the jar.

Honey works particularly well where its own character can still be noticed: over porridge, with fruit, in a light dressing or stirred into a drink after the water has cooled slightly. Honey should never be given to infants under twelve months.
TEXT,
            ],
            [
                'title' => 'A Field Guide to Uttarakhand’s Mountain Pulses',
                'slug' => 'field-guide-uttarakhand-mountain-pulses',
                'category' => 'Ingredients',
                'excerpt' => 'Meet gahat, bhatt and hill rajma—three distinctive pulses shaped by mountain kitchens and growing conditions.',
                'featured_image' => 'assets/img/products/munsiyari-rajma.webp',
                'image_alt' => 'Freshly prepared red hill rajma beans from the Munsiyari region',
                'is_featured' => true,
                'published_at' => '2026-07-02 10:00:00',
                'seo_title' => 'Uttarakhand Mountain Pulses: Gahat, Bhatt and Rajma',
                'meta_description' => 'Discover gahat, bhatt and Munsiyari rajma, with practical soaking, cooking and storage advice for traditional Uttarakhand mountain pulses.',
                'content' => <<<'TEXT'
## Small crops with deep local roots

Mountain pulses have long suited mixed farms where families grow several crops across small terraces. Gahat, also called horse gram, bhatt or black soybean, and regional rajma varieties each have their own cooking character. They are not interchangeable, but all reward patient preparation.

Names can vary between valleys and markets. That makes a clear product label especially useful: it should identify the pulse, growing region where known, pack date and storage guidance rather than relying only on the broad word “pahadi”.

## Soaking and cooking

Sort and rinse pulses before use. Rajma and gahat generally benefit from an overnight soak, followed by fresh cooking water and thorough boiling until completely tender. Cooking time varies with age, variety, water hardness and altitude, so texture is a better guide than a rigid timer.

Bhatt develops a rich, earthy character when gently roasted before simmering. Traditional dishes often build flavour with jakhya, cumin, garlic or warming whole spices. Start simply when trying a new batch so its own taste remains easy to understand.

## Buying and storing thoughtfully

Choose a pack size your household can finish while the pulse is still in good condition. Store it in a dry, airtight container away from direct light. If your kitchen is humid, inspect the container periodically and avoid mixing a new batch into an older one.

Natural size and colour variation is expected in minimally sorted produce. A clean aroma, intact packaging and transparent batch information matter more than perfectly uniform beans.
TEXT,
            ],
            [
                'title' => 'Jhangora, Mandua and Ramdana: Millets of the Hills',
                'slug' => 'jhangora-mandua-ramdana-hill-grains-guide',
                'category' => 'Food Traditions',
                'excerpt' => 'An introduction to three resilient hill staples and practical ways to welcome them into an everyday pantry.',
                'featured_image' => 'assets/img/products/jhangora-millet.webp',
                'image_alt' => 'Small pale jhangora millet grains in a rustic bowl',
                'is_featured' => true,
                'published_at' => '2026-06-28 09:30:00',
                'seo_title' => 'Jhangora, Mandua and Ramdana: Uttarakhand Grain Guide',
                'meta_description' => 'Explore jhangora, mandua and ramdana from Uttarakhand, including their traditional uses, cooking character and practical pantry storage.',
                'content' => <<<'TEXT'
## Three names, three different ingredients

Jhangora is barnyard millet, mandua is finger millet and ramdana is amaranth. They are often grouped together as mountain grains, but their textures and kitchen roles differ. Jhangora cooks into soft individual grains, mandua is commonly milled into a dark flour, and ramdana may be popped or simmered.

These crops are associated with hill agriculture because they can fit diverse terrace systems and local food traditions. Their value comes from flavour, cultural continuity and versatility—not from treating any one ingredient as a miracle food.

## Bringing them into daily cooking

Rinse jhangora in a fine sieve and cook it gently with water until tender. It can replace rice in a simple meal or form the base of a lightly sweetened pudding. Mandua flour has an earthy taste and little gluten-forming ability, so beginners often combine it with wheat flour for easier flatbreads.

Ramdana can be dry-popped in a hot pan in small batches or cooked like a tiny seed. Because each product is processed differently, follow the pack guidance first and adjust water gradually.

## Freshness matters

Whole grains generally keep longer than flour. Buy mandua flour in quantities you can use promptly, close the packet well and store it away from warmth and moisture. A clean, mild aroma is expected; stale or oily odours are a reason not to use it.

Record the water ratio that works in your kitchen. Grain age, grind and even cookware affect results, and one useful note makes the next meal much easier.
TEXT,
            ],
            [
                'title' => 'How to Brew a Balanced Herbal Tea',
                'slug' => 'how-to-brew-balanced-herbal-tea',
                'category' => 'How-to Guides',
                'excerpt' => 'A flexible method for drawing clear flavour from mountain herbs, flowers and leaf blends without overwhelming the cup.',
                'featured_image' => 'assets/img/products/chamomile-tea.webp',
                'image_alt' => 'A warm cup of chamomile herbal tea with dried flowers',
                'is_featured' => false,
                'published_at' => '2026-06-22 08:00:00',
                'seo_title' => 'How to Brew Herbal Tea: A Practical Step-by-Step Guide',
                'meta_description' => 'Learn a flexible method for brewing herbal tea, including water, quantity, steeping time, storage and ways to adjust flavour without bitterness.',
                'content' => <<<'TEXT'
## Start with the ingredient, not a rigid rule

Flowers, roots and tender leaves release flavour at different speeds. Chamomile can become overly strong when left too long, while a robust nettle or lemongrass blend may benefit from a longer infusion. Begin with the guidance on the pack, then adjust one variable at a time.

Fresh-tasting water matters. Bring it to a boil, allow the most aggressive bubbling to settle and pour it directly over the herbs unless the pack recommends another temperature.

## A dependable starting method

Use roughly one level teaspoon of loose herbal material for a 200–250 ml cup. Cover the cup and steep for five to seven minutes. Covering helps retain aromas that would otherwise leave with the steam.

Strain and taste before adding anything else. If the cup is weak, increase the amount of herb before extending the steep dramatically; excessive steeping can draw out harsh flavours.

## Build flavour thoughtfully

Honey can round out earthy blends, while lemon can brighten mint and lemongrass. Add either only after tasting so the base infusion remains the guide. For an iced drink, brew slightly stronger and cool it promptly before serving over ice.

Store dried herbs tightly closed, away from sunlight, moisture and strong kitchen aromas. Herbal tea is food and general enjoyment, not a substitute for medical care. Anyone pregnant, taking medication or managing a health condition should check whether a specific herb is appropriate.
TEXT,
            ],
            [
                'title' => 'Jakhya, Tejpatta and Hill Turmeric: A Pahadi Spice Shelf',
                'slug' => 'pahadi-spices-jakhya-tejpatta-turmeric',
                'category' => 'Ingredients',
                'excerpt' => 'Three Uttarakhand pantry staples, the flavours they bring and the small techniques that help each one shine.',
                'featured_image' => 'assets/img/products/jakhya-spice.webp',
                'image_alt' => 'Whole jakhya seeds used as a traditional Uttarakhand tempering spice',
                'is_featured' => false,
                'published_at' => '2026-06-18 11:00:00',
                'seo_title' => 'Pahadi Spices Guide: Jakhya, Tejpatta and Turmeric',
                'meta_description' => 'Learn how jakhya, tejpatta and hill turmeric are used in Uttarakhand cooking, with tips for tempering, buying and storing whole spices.',
                'content' => <<<'TEXT'
## Jakhya: a tiny seed with a crisp finish

Jakhya is valued in Uttarakhand for the nutty crunch it develops in hot fat. Add a small spoonful to oil or ghee and let the seeds crackle before introducing potatoes, greens or cooked pulses. The aim is a fragrant tempering, not prolonged frying.

Its role is textural as well as aromatic. Begin with less than you think you need; a restrained tempering lets the main ingredient remain the centre of the dish.

## Tejpatta and turmeric

Indian bay leaf, commonly called tejpatta, has a warm fragrance distinct from Mediterranean bay. Add a whole leaf early to rice, dal or a slow-cooked gravy, then remove it before serving. Whole leaves should smell pleasantly aromatic when gently crushed.

Turmeric varies in colour and intensity with variety and processing. Use it for its earthy flavour and colour rather than assuming a deeper orange powder is automatically better. A product page should clearly state whether the pack contains pure ground turmeric or a blend.

## Protecting aroma in storage

Keep whole and ground spices away from the stove, where heat and steam shorten their useful life. Airtight containers in a dark cupboard are ideal. Buy ground spices in smaller quantities and use a dry spoon every time.

If a spice smells flat, damp or musty, replacing it is more useful than adding an excessive quantity to the dish. Freshness should create clarity, not harshness.
TEXT,
            ],
            [
                'title' => 'Buransh in Bloom: Understanding Uttarakhand’s Rhododendron Tradition',
                'slug' => 'buransh-rhododendron-uttarakhand-tradition',
                'category' => 'Food Traditions',
                'excerpt' => 'A grounded look at the vivid spring flower, its place in local drinks and the importance of careful sourcing.',
                'featured_image' => 'assets/img/products/buransh-squash.webp',
                'image_alt' => 'A ruby red buransh rhododendron drink served chilled',
                'is_featured' => false,
                'published_at' => '2026-06-12 10:30:00',
                'seo_title' => 'Buransh: Uttarakhand Rhododendron Drink and Tradition',
                'meta_description' => 'Discover buransh, Uttarakhand’s rhododendron flower tradition, how the drink is prepared and what to look for on a transparent product label.',
                'content' => <<<'TEXT'
## A sign of spring in the middle hills

Buransh is the local name commonly used for Rhododendron arboreum, whose red flowers brighten parts of Uttarakhand in spring. The flowers have become closely associated with the state and are often prepared as a sweet-tart squash or syrup.

Regional tradition deserves more than a scenic label. Responsible storytelling should name the ingredient accurately, avoid unsupported health promises and explain whether a product is a concentrate, ready-to-drink beverage or infusion.

## From flower to bottle

Recipes differ between households and producers. Petals may be cleaned, steeped or gently processed before their extract is combined with sugar, water and sometimes an acidity regulator. A commercial label should make the full ingredient list and dilution instructions easy to find.

Colour can vary by harvest and recipe. An intense red shade alone does not prove quality, just as natural variation does not mean a product is inconsistent.

## Serving and buying well

Dilute squash according to the pack, then adjust with chilled water rather than adding extra concentrate immediately. It can also be used in a light spritzer or poured over ice with a squeeze of citrus.

Look for sealed packaging, a batch date, ingredient transparency and clear storage instructions after opening. Refrigerate when directed and use a clean cap and bottle neck to protect the remaining drink.
TEXT,
            ],
            [
                'title' => 'From Orchard to Pantry: Preserves from the Himalayan Hills',
                'slug' => 'himalayan-orchard-preserves-guide',
                'category' => 'Food Traditions',
                'excerpt' => 'How seasonal apples, apricots and plums become useful pantry preserves—and what a clear label should tell you.',
                'featured_image' => 'assets/img/products/apricot-preserve.webp',
                'image_alt' => 'Golden Himalayan apricot preserve in a glass jar',
                'is_featured' => false,
                'published_at' => '2026-06-08 09:00:00',
                'seo_title' => 'Himalayan Fruit Preserves: Apple, Apricot and Plum Guide',
                'meta_description' => 'Learn how Himalayan orchard fruit becomes preserves, what to check on labels and how to store and serve apple, apricot and plum products.',
                'content' => <<<'TEXT'
## Extending a short harvest

Apples, apricots and plums arrive in distinct seasonal windows across Himalayan growing regions. Preserving turns ripe fruit into something that can travel and be enjoyed beyond harvest, while giving growers and small processors another way to use sound but visually imperfect produce.

The character of a preserve depends on fruit maturity, recipe and cooking time. Some are soft and bright, others thick and caramelised. Neither style is automatically more authentic; the ingredient list tells a clearer story.

## Reading the jar

Check the order of ingredients, net weight, batch information and storage directions. Fruit should be identified by name. Added sugar, pectin, spices or permitted acidity regulators should not be hidden behind vague language.

Natural fruit products may vary slightly in colour. A photograph is a serving suggestion, while the label is where composition and quantity must be clear.

## Beyond breakfast toast

A tart plum preserve can accompany a cheese board, while apricot works in a glaze or a simple yoghurt bowl. Apple preserve can be folded into porridge or served with a lightly spiced flatbread.

Always use a clean, dry spoon. Follow the refrigeration instruction after opening and discard a jar with a broken seal, unusual fermentation, visible mould or an unexpected odour.
TEXT,
            ],
            [
                'title' => 'A Practical Guide to Mindful Pantry Shopping',
                'slug' => 'practical-guide-mindful-pantry-shopping',
                'category' => 'Buying Guides',
                'excerpt' => 'Simple checks that help you choose useful quantities, understand origin and reduce forgotten packets in the cupboard.',
                'featured_image' => 'assets/img/products/mixed-dry-fruits.webp',
                'image_alt' => 'An organised selection of dried fruits and nuts for a mindful pantry',
                'is_featured' => false,
                'published_at' => '2026-06-03 08:30:00',
                'seo_title' => 'Mindful Pantry Shopping: A Practical Buying Guide',
                'meta_description' => 'Use these practical checks for ingredients, origin, pack size, storage and versatility to build a more useful pantry with less avoidable waste.',
                'content' => <<<'TEXT'
## Read beyond the front label

Start with four questions: what is in the pack, where was it produced, how much will you realistically use and how should it be stored? Ingredients, weight and origin usually say more than broad words such as natural, premium or traditional.

For farm and forest products, some variation is expected. Clear batch and producer information makes that variation easier to understand and gives the source greater visibility.

## Choose quantities that fit your kitchen

A larger pack is not better value if it loses freshness before you finish it. Consider how often you cook the ingredient, whether your storage is dry and airtight, and whether you can share a larger order with family.

Versatile staples such as pulses, grains and whole spices can work across several meals. Planning two or three uses before ordering reduces forgotten packets and makes delivery more efficient.

## Look for useful transparency

A trustworthy product page should make price, weight, ingredients, availability, returns and contact options easy to find. It should distinguish verified details from information that is not yet available.

Mindful commerce includes service after checkout. Keep your invoice, inspect food packages on arrival and report leakage, damage or an incorrect item promptly with clear photographs.
TEXT,
            ],
            [
                'title' => 'Kinnaur’s Dry Fruits: Walnuts, Apricots and Chilgoza',
                'slug' => 'kinnaur-dry-fruits-walnuts-apricots-chilgoza',
                'category' => 'Ingredients',
                'excerpt' => 'A buyer’s introduction to three treasured foods from Himachal’s high valleys, with storage and quality checks.',
                'featured_image' => 'assets/img/products/dried-apricots.webp',
                'image_alt' => 'Bowls of dried apricots, nuts and berries arranged on a light surface',
                'is_featured' => false,
                'published_at' => '2026-05-28 10:00:00',
                'seo_title' => 'Kinnaur Dry Fruits: Walnuts, Apricots and Chilgoza Guide',
                'meta_description' => 'A practical guide to Kinnaur walnuts, dried apricots and chilgoza pine nuts, including flavour, buying checks, storage and serving ideas.',
                'content' => <<<'TEXT'
## Foods shaped by a high-valley climate

Kinnaur is widely known for orchard produce and dry fruits. Walnuts, apricots and chilgoza pine nuts each carry a strong regional association, but shoppers should still look for specific origin details rather than assuming every product with a mountain name comes from the named place.

Harvest, drying method and storage all influence flavour. Genuine regional sourcing and good post-harvest care need to work together.

## What to notice

Walnut kernels should smell clean and nutty, not paint-like or bitter. Dried apricots range from golden to deep brown depending on variety and processing; colour alone is not a quality grade. Chilgoza is naturally rich and should be protected from warmth once shelled.

Packaging should state net weight, packing date, best-before guidance and whether preservatives or other ingredients are present. Broken pieces can still taste excellent, but the grade should be represented honestly.

## Storage and serving

Keep dry fruits tightly closed in a cool cupboard for short-term use. In warm weather or for longer storage, refrigeration can help protect aroma and texture; follow the pack instructions first.

Serve small quantities where their character is easy to notice: walnuts over porridge, chopped apricots in a grain salad, or chilgoza scattered over a simple dish just before serving.
TEXT,
            ],
            [
                'title' => 'Ringal Craft: The Quiet Skill Behind a Mountain Basket',
                'slug' => 'ringal-craft-uttarakhand-mountain-baskets',
                'category' => 'Craft & People',
                'excerpt' => 'Why a handwoven ringal basket carries the marks of its material, maker and place—and how to care for it at home.',
                'featured_image' => 'assets/img/products/ringal-basket.webp',
                'image_alt' => 'A handwoven natural-fibre ringal basket made by a mountain artisan',
                'is_featured' => false,
                'published_at' => '2026-05-20 09:00:00',
                'seo_title' => 'Ringal Craft: Uttarakhand Basket Weaving and Care',
                'meta_description' => 'Learn about Uttarakhand ringal basket craft, natural variation, responsible product information and simple care for handwoven mountain products.',
                'content' => <<<'TEXT'
## A material suited to useful objects

Ringal is a mountain bamboo traditionally used for baskets and household objects in parts of Uttarakhand. The craft brings together knowledge of selecting material, splitting it evenly and controlling tension through the weave.

A finished basket is both practical and expressive. Slight shifts in tone, line and dimension are evidence of natural material and handwork, not defects that need to be disguised.

## What transparent craft retail should show

A useful listing names the material, dimensions, maker or region where known, and intended use. It should not imply that every object is food-safe, waterproof or suitable for heavy loads unless those claims have been verified.

Clear photographs should show the rim, base and weave closely. Because handmade dimensions can vary, a reasonable tolerance is more honest than a promise of machine-level uniformity.

## Caring for a woven basket

Keep ringal craft dry and away from prolonged direct sunlight. Dust with a soft brush or dry cloth. If the maker’s instructions allow a damp wipe, use very little water and let the basket dry fully in moving air before storing it.

Do not seal damp natural fibre inside a cupboard. Thoughtful care respects both the material and the time invested by the person who made it.
TEXT,
            ],
        ];

        foreach ($posts as $post) {
            JournalPost::updateOrCreate(['slug' => $post['slug']], $post + [
                'author_name' => 'Rajendra Panwar',
                'is_published' => true,
            ]);
        }
    }
}
