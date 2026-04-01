<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('certificates')->insert([
            ['type' => 'internal', 'description' => 'internal certificate'],
            ['type' => 'external', 'description' => 'external certificate'],
        ]);
        $certificateTypes = [
            'internal' => [
                'en' => [
                    'type' => 'Internal Certificate',
                    'description' => 'Given from Internal Organization.'
                ],
                'ar' => [
                    'type' => 'شهادة داخلية',
                    'description' => 'تمنح من منظمة داخلية'
                ],
            ],
            'external' => [
                'en' => [
                    'type' => 'External Certificate',
                    'description' => 'Given from External Organization.'
                ],
                'ar' => [
                    'type' => 'شهادة خارجية',
                    'description' => 'تمنح من منظمة خارجية'
                ],
            ],
        ];
        foreach ($certificateTypes as $type => $translations) {
            $certificate = Certificate::whereRaw('LOWER(type) = ?', [strtolower($type)])->first();

            if (!$certificate) {
                $certificate = Certificate::create([
                    'type' => strtolower($type),
                ]);
            }
            foreach ($translations as $locale => $attrs) {
                $certificate->setTranslation('type', $locale, $attrs['type']);
                $certificate->setTranslation('description', $locale, $attrs['description']);
            }
            $certificate->save();
        }
    }
}
