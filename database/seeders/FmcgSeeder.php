<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FmcgSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // name, brand, category, barcode, mrp, hsn, tax%, track_stock, stock
            ['Himalaya Neem Face Wash 100ml',        'Himalaya',  'Face Wash',   '8901138511051', 165.00, '3304', 18.00, true,  48],
            ['Himalaya Purifying Neem Soap 125g',    'Himalaya',  'Soap',        '8901138511068', 45.00,  '3401', 18.00, true,  144],
            ['Himalaya Anti-Dandruff Shampoo 180ml', 'Himalaya',  'Shampoo',     '8901138511075', 130.00, '3305', 18.00, false, 0],
            ['Himalaya Baby Powder 200g',            'Himalaya',  'Skin Care',   '8901138511082', 145.00, '3304', 18.00, false, 0],
            ['Dabur Amla Hair Oil 275ml',            'Dabur',     'Hair Oil',    '8901207010012', 199.00, '3305', 18.00, true,  36],
            ['Dabur Red Toothpaste 200g',            'Dabur',     'Toothpaste',  '8901207010029', 145.00, '3306', 12.00, true,  72],
            ['Dabur Honey 500g',                     'Dabur',     'Health',      '8901207010036', 199.00, '0409', 5.00,  false, 0],
            ['Dabur Chyawanprash 500g',              'Dabur',     'Health',      '8901207010043', 215.00, '2106', 12.00, false, 0],
            ['Patanjali Kesh Kanti Hair Oil 120ml',  'Patanjali', 'Hair Oil',    '8904109410017', 130.00, '3305', 18.00, false, 0],
            ['Patanjali Dant Kanti Toothpaste 200g', 'Patanjali', 'Toothpaste',  '8904109410024', 100.00, '3306', 12.00, true,  60],
            ['Patanjali Aloe Vera Gel 150ml',        'Patanjali', 'Skin Care',   '8904109410031', 90.00,  '3304', 18.00, false, 0],
            ['Colgate Strong Teeth 200g',            'Colgate',   'Toothpaste',  '8901314010018', 122.00, '3306', 18.00, true,  96],
            ['Colgate MaxFresh Blue 150g',           'Colgate',   'Toothpaste',  '8901314010025', 105.00, '3306', 18.00, false, 0],
            ['Colgate ZigZag Toothbrush Medium',     'Colgate',   'Oral Care',   '8901314010032', 45.00,  '9603', 18.00, true,  120],
            ['Parle-G Gold 500g',                    'Parle',     'Biscuits',    '8901719100017', 60.00,  '1905', 18.00, true,  200],
            ['Parle Monaco Classic 200g',            'Parle',     'Biscuits',    '8901719100024', 40.00,  '1905', 18.00, false, 0],
            ['Parle Hide & Seek 200g',               'Parle',     'Biscuits',    '8901719100031', 50.00,  '1905', 18.00, false, 0],
            ['Britannia Good Day Cashew 200g',       'Britannia', 'Biscuits',    '8901063010013', 45.00,  '1905', 18.00, true,  150],
            ['Britannia Marie Gold 250g',            'Britannia', 'Biscuits',    '8901063010020', 40.00,  '1905', 18.00, false, 0],
            ['Britannia Bourbon 150g',               'Britannia', 'Biscuits',    '8901063010037', 35.00,  '1905', 18.00, false, 0],
            ['Emami BoroPlus Cream 80ml',            'Emami',     'Skin Care',   '8901248010015', 105.00, '3304', 18.00, false, 0],
            ['Emami Navratna Oil 200ml',             'Emami',     'Hair Oil',    '8901248010022', 135.00, '3305', 18.00, true,  40],
            ['Parachute Coconut Oil 250ml',          'Marico',    'Hair Oil',    '8901088010016', 146.00, '1513', 5.00,  true,  84],
            ['Hair & Care Damage Repair 200ml',      'Marico',    'Hair Oil',    '8901088010023', 120.00, '3305', 18.00, false, 0],
        ];

        foreach ($products as [$name, $brand, $category, $barcode, $mrp, $hsn, $tax, $trackStock, $stock]) {
            $brandId = Brand::firstOrCreate(['name' => $brand])->id;
            $categoryId = Category::firstOrCreate(['name' => $category])->id;

            Product::updateOrCreate(
                ['barcode' => $barcode],
                [
                    'name' => $name,
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'mrp' => $mrp,
                    'hsn_code' => $hsn,
                    'tax_percent' => $tax,
                    'tax_inclusive' => true,
                    'track_stock' => $trackStock,
                    'stock_qty' => $stock,
                    'is_visible' => true,
                    'rate_visible' => false,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Seeded: ' . Brand::count() . ' brands, ' . Category::count() . ' categories, ' . Product::count() . ' products.');
    }
}
