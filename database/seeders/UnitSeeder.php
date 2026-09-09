<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'piece', 'name' => 'Piece', 'symbol' => 'pc'],
            ['code' => 'set', 'name' => 'Set', 'symbol' => 'set'],
            ['code' => 'pair', 'name' => 'Pair', 'symbol' => 'pr'],
            ['code' => 'pack', 'name' => 'Pack', 'symbol' => 'pk'],
            ['code' => 'box', 'name' => 'Box', 'symbol' => 'box'],
            ['code' => 'bag', 'name' => 'Bag', 'symbol' => 'bag'],
            ['code' => 'sack', 'name' => 'Sack', 'symbol' => 'sack'],
            ['code' => 'carton', 'name' => 'Carton', 'symbol' => 'ctn'],
            ['code' => 'bundle', 'name' => 'Bundle', 'symbol' => 'bdl'],
            ['code' => 'roll', 'name' => 'Roll', 'symbol' => 'roll'],
            ['code' => 'sheet', 'name' => 'Sheet', 'symbol' => 'sht'],
            ['code' => 'length', 'name' => 'Length', 'symbol' => 'len'],
            ['code' => 'm', 'name' => 'Metre', 'symbol' => 'm'],
            ['code' => 'm2', 'name' => 'Square metre', 'symbol' => 'm²'],
            ['code' => 'm3', 'name' => 'Cubic metre', 'symbol' => 'm³'],
            ['code' => 'mm', 'name' => 'Millimetre', 'symbol' => 'mm'],
            ['code' => 'cm', 'name' => 'Centimetre', 'symbol' => 'cm'],
            ['code' => 'ft', 'name' => 'Foot', 'symbol' => 'ft'],
            ['code' => 'kg', 'name' => 'Kilogram', 'symbol' => 'kg'],
            ['code' => 'g', 'name' => 'Gram', 'symbol' => 'g'],
            ['code' => 'ton', 'name' => 'Tonne', 'symbol' => 't'],
            ['code' => 'l', 'name' => 'Litre', 'symbol' => 'L'],
            ['code' => 'ml', 'name' => 'Millilitre', 'symbol' => 'ml'],
            ['code' => 'tin', 'name' => 'Tin', 'symbol' => 'tin'],
            ['code' => 'can', 'name' => 'Can', 'symbol' => 'can'],
            ['code' => 'bottle', 'name' => 'Bottle', 'symbol' => 'btl'],
            ['code' => 'jar', 'name' => 'Jar', 'symbol' => 'jar'],
            ['code' => 'tub', 'name' => 'Tub', 'symbol' => 'tub'],
            ['code' => 'tube', 'name' => 'Tube', 'symbol' => 'tube'],
            ['code' => 'sachet', 'name' => 'Sachet', 'symbol' => 'sch'],
            ['code' => 'drum', 'name' => 'Drum', 'symbol' => 'drm'],
            ['code' => 'pallet', 'name' => 'Pallet', 'symbol' => 'plt'],
            ['code' => 'hour', 'name' => 'Hour', 'symbol' => 'hr'],
            ['code' => 'day', 'name' => 'Day', 'symbol' => 'day'],
            ['code' => 'job', 'name' => 'Job', 'symbol' => 'job'],
        ];

        foreach ($units as $unit) {
            Unit::query()->updateOrCreate(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'symbol' => $unit['symbol'],
                ],
            );
        }
    }
}
