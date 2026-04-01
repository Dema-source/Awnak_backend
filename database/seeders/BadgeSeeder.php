<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badgeTypes = [
            'bronze' => [
                'en' => [
                    'type' => 'Bronze Badge',
                    'description' => 'Awarded for completing 10 volunteer hours.'
                ],
                'ar' => [
                    'type' => 'شارة البرونز',
                    'description' => 'تمنح بعد إكمال 10 ساعات تطوعية'
                ],
            ],
            'Silver' => [
                'en' => [
                    'type' => 'Silver Badge',
                    'description' => 'Awarded for completing 50 volunteer hours.'
                ],
                'ar' => [
                    'type' => 'شارةالفضة',
                    'description' => 'تمنح بعد إكمال 50 ساعة تطوعية'
                ],
            ],
            'Gold' => [
                'en' => [
                    'type' => 'Gold Badge',
                    'description' => 'Awarded for completing 100 volunteer hours.'
                ],
                'ar' => [
                    'type' => 'شارةالذهب',
                    'description' => 'تمنح بعد إكمال 100 ساعة تطوعية'
                ],
            ],
        ];
        foreach ($badgeTypes as $type => $translations) {
            $badge = Badge::whereRaw('LOWER(type) = ?', [strtolower($type)])->first();

            if (!$badge) {
                $badge = Badge::create([
                    'type' => strtolower($type),
                ]);
            }
            foreach ($translations as $locale => $attrs) {
                $badge->setTranslation('type', $locale, $attrs['type']);
                $badge->setTranslation('description', $locale, $attrs['description']);
            }
            $badge->save();
        }
    }
}
