<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;

class PropertySeeder extends Seeder
{
    // Curated picsum seed IDs that look like real estate photos
    private const PHOTO_SEEDS = [10, 11, 12, 20, 22, 24, 30, 36, 40, 42, 50, 51, 60, 63, 70, 72, 80, 87, 90, 95, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190];
    private const PLAN_SEEDS  = [200, 210, 220, 230, 240, 250, 260, 270, 280, 290];

    public function run(): void
    {
        $adminId  = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->value('id');
        $agentIds = User::whereHas('role', fn($q) => $q->where('name', 'agente'))->pluck('id')->toArray();

        $photoIdx = 0;
        $planIdx  = 0;

        $properties = [
            [
                'title'       => 'Piso luminoso en el centro histórico',
                'description' => 'Precioso piso reformado en pleno centro, con techos altos y mucha luz natural.',
                'type'        => 'piso',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 185000,
                'surface'   => 82,
                'rooms' => 3,
                'bathrooms' => 1,
                'address'     => 'Calle Mayor, 14, 3º B',
                'city'        => 'Valencia',
                'locality' => 'Ciutat Vella',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4699,
                'longitude' => -0.3763,
            ],
            [
                'title'       => 'Ático con terraza y vistas al mar',
                'description' => 'Espectacular ático con amplia terraza de 40m² y vistas panorámicas al Mediterráneo.',
                'type'        => 'piso',
                'operation' => 'venta',
                'status' => 'reservado',
                'price'       => 320000,
                'surface'   => 95,
                'rooms' => 2,
                'bathrooms' => 2,
                'address'     => 'Avenida del Puerto, 78, 8º A',
                'city'        => 'Valencia',
                'locality' => 'El Grau',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4602,
                'longitude' => -0.3280,
            ],
            [
                'title'       => 'Casa adosada con jardín y piscina',
                'description' => 'Amplia vivienda adosada en urbanización privada con jardín y piscina comunitaria.',
                'type'        => 'casa',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 275000,
                'surface'   => 180,
                'rooms' => 4,
                'bathrooms' => 3,
                'address'     => 'Urbanización Los Pinos, 12',
                'city'        => 'Paterna',
                'locality' => 'La Canyada',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.5031,
                'longitude' => -0.4406,
            ],
            [
                'title'       => 'Local comercial zona alto tráfico',
                'description' => 'Local diáfano en planta baja con escaparate a dos calles, ideal para comercio.',
                'type'        => 'local',
                'operation' => 'alquiler',
                'status' => 'disponible',
                'price'       => 1200,
                'surface'   => 110,
                'rooms' => null,
                'bathrooms' => 1,
                'address'     => 'Calle Colón, 32',
                'city'        => 'Valencia',
                'locality' => 'L\'Eixample',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4686,
                'longitude' => -0.3756,
            ],
            [
                'title'       => 'Oficina diáfana en edificio empresarial',
                'description' => 'Planta de 200m² en edificio de oficinas con parking y acceso 24h.',
                'type'        => 'oficina',
                'operation' => 'alquiler',
                'status' => 'disponible',
                'price'       => 2400,
                'surface'   => 200,
                'rooms' => null,
                'bathrooms' => 2,
                'address'     => 'Av. Cortes Valencianas, 50, Planta 4',
                'city'        => 'Valencia',
                'locality' => 'Quatre Carreres',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4831,
                'longitude' => -0.3521,
            ],
            [
                'title'       => 'Piso estudiantes cerca universidad',
                'description' => 'Piso de 3 habitaciones amueblado, a 5 minutos de la Universidad Politécnica.',
                'type'        => 'piso',
                'operation' => 'alquiler',
                'status' => 'alquilado',
                'price'       => 850,
                'surface'   => 70,
                'rooms' => 3,
                'bathrooms' => 1,
                'address'     => 'Calle Vera, 8, 2º C',
                'city'        => 'Valencia',
                'locality' => 'Camins al Grau',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4825,
                'longitude' => -0.3460,
            ],
            [
                'title'       => 'Chalet independiente con gran parcela',
                'description' => 'Chalet con parcela de 800m², piscina privada y garaje para dos coches.',
                'type'        => 'casa',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 490000,
                'surface'   => 320,
                'rooms' => 5,
                'bathrooms' => 4,
                'address'     => 'Camino de la Albufera, 23',
                'city'        => 'Albal',
                'locality' => 'Albal',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.3894,
                'longitude' => -0.4025,
            ],
            [
                'title'       => 'Garaje aparcamiento subterráneo',
                'description' => 'Plaza amplia en parking subterráneo con vigilancia 24h.',
                'type'        => 'garaje',
                'operation' => 'venta',
                'status' => 'vendido',
                'price'       => 22000,
                'surface'   => 22,
                'rooms' => null,
                'bathrooms' => null,
                'address'     => 'Calle Cirilo Amorós, 15 - Plaza 42',
                'city'        => 'Valencia',
                'locality' => 'Pla del Real',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4711,
                'longitude' => -0.3762,
            ],
            [
                'title'       => 'Terreno edificable área residencial',
                'description' => 'Solar urbano con licencia para 4 viviendas unifamiliares. Servicios disponibles.',
                'type'        => 'terreno',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 145000,
                'surface'   => 600,
                'rooms' => null,
                'bathrooms' => null,
                'address'     => 'Polígono Residencial Sur, Parcela 8',
                'city'        => 'Torrent',
                'locality' => 'Torrent Sud',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4352,
                'longitude' => -0.4616,
            ],
            [
                'title'       => 'Piso reformado con parking incluido',
                'description' => 'Piso reformado en 2023, cocina abierta, armarios empotrados, plaza de garaje.',
                'type'        => 'piso',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 210000,
                'surface'   => 90,
                'rooms' => 3,
                'bathrooms' => 2,
                'address'     => 'Calle Músico Peydró, 5, 4º D',
                'city'        => 'Valencia',
                'locality' => 'Extramurs',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4730,
                'longitude' => -0.3794,
            ],
            [
                'title'       => 'Estudio céntrico recién reformado',
                'description' => 'Estudio de 35m² reformado, ideal para inversión. Muy bien comunicado.',
                'type'        => 'piso',
                'operation' => 'alquiler',
                'status' => 'disponible',
                'price'       => 650,
                'surface'   => 35,
                'rooms' => 1,
                'bathrooms' => 1,
                'address'     => 'Plaza del Ayuntamiento, 8, 1º A',
                'city'        => 'Valencia',
                'locality' => 'Ciutat Vella',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4697,
                'longitude' => -0.3773,
            ],
            [
                'title'       => 'Casa rural con encanto',
                'description' => 'Masía restaurada de 450m² con vigas originales, bodega, huerto y vistas a la montaña.',
                'type'        => 'casa',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 380000,
                'surface'   => 450,
                'rooms' => 6,
                'bathrooms' => 3,
                'address'     => 'Partida Rural El Palmar, s/n',
                'city'        => 'Llíria',
                'locality' => 'Partida El Palmar',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.6247,
                'longitude' => -0.5973,
            ],
            [
                'title'       => 'Piso moderno con vistas al jardín',
                'description' => 'Piso de diseño contemporáneo con amplias ventanas y orientación sur.',
                'type'        => 'piso',
                'operation' => 'alquiler',
                'status' => 'disponible',
                'price'       => 950,
                'surface'   => 75,
                'rooms' => 2,
                'bathrooms' => 1,
                'address'     => 'Avenida Blasco Ibáñez, 12, 5º B',
                'city'        => 'Valencia',
                'locality' => 'El Pla del Real',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4817,
                'longitude' => -0.3601,
            ],
            [
                'title'       => 'Nave industrial con oficinas',
                'description' => 'Nave de 500m² con zona de oficinas, muelles de carga y acceso de camiones.',
                'type'        => 'otro',
                'operation' => 'alquiler',
                'status' => 'disponible',
                'price'       => 3500,
                'surface'   => 500,
                'rooms' => null,
                'bathrooms' => 2,
                'address'     => 'Polígono Fuente del Jarro, Nave 18',
                'city'        => 'Paterna',
                'locality' => 'Fuente del Jarro',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.5218,
                'longitude' => -0.4712,
            ],
            [
                'title'       => 'Dúplex con terraza privada',
                'description' => 'Dúplex de 120m² distribuido en dos plantas con terraza de 25m² y solarium.',
                'type'        => 'piso',
                'operation' => 'venta',
                'status' => 'disponible',
                'price'       => 245000,
                'surface'   => 120,
                'rooms' => 3,
                'bathrooms' => 2,
                'address'     => 'Calle Pintor Sorolla, 7, 6º A',
                'city'        => 'Valencia',
                'locality' => 'Russafa',
                'province' => 'Valencia',
                'country' => 'España',
                'latitude'    => 39.4712,
                'longitude' => -0.3780,
            ],
        ];

        foreach ($properties as $i => $data) {
            $data['user_id'] = $i % 3 === 0
                ? $adminId
                : ($agentIds[$i % count($agentIds)] ?? $adminId);

            $mainSeed = self::PHOTO_SEEDS[$photoIdx % count(self::PHOTO_SEEDS)];


            $images = [

                'piso' => [
                    'https://images.unsplash.com/photo-1568605114967-8130f3a36994',
                    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb',
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be',
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c',
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c',
                    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6',
                    'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde',
                ],

                'casa' => [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9',
                    'https://images.unsplash.com/photo-1576941089067-2de3c901e126',
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6',
                    'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6',
                    'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf',
                    'https://images.unsplash.com/photo-1600585152915-d208bec867a1',
                    'https://images.unsplash.com/photo-1600585152220-90363fe7e115',
                    'https://images.unsplash.com/photo-1605146769289-440113cc3d00',
                    'https://images.unsplash.com/photo-1572120360610-d971b9d7767c',
                    'https://images.unsplash.com/photo-1600607687644-c7171b42498f',
                ],

                'oficina' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c',
                    'https://images.unsplash.com/photo-1497366811353-6870744d04b2',
                    'https://images.unsplash.com/photo-1504384308090-c894fdcc538d',
                    'https://images.unsplash.com/photo-1497215728101-856f4ea42174',
                    'https://images.unsplash.com/photo-1492724441997-5dc865305da7',
                    'https://images.unsplash.com/photo-1524758631624-e2822e304c36',
                    'https://images.unsplash.com/photo-1507679799987-c73779587ccf',
                    'https://images.unsplash.com/photo-1557804506-669a67965ba0',
                    'https://images.unsplash.com/photo-1542744173-8e7e53415bb0',
                    'https://images.unsplash.com/photo-1531973576160-7125cd663d86',
                ],

                'local' => [
                    'https://images.unsplash.com/photo-1441986300917-64674bd600d8',
                    'https://images.unsplash.com/photo-1521335629791-ce4aec67dd47',
                    'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a',
                    'https://images.unsplash.com/photo-1515169067868-5387ec356754',
                    'https://images.unsplash.com/photo-1470309864661-68328b2cd0a5',
                    'https://images.unsplash.com/photo-1542838132-92c53300491e',
                    'https://images.unsplash.com/photo-1556740738-b6a63e27c4df',
                    'https://images.unsplash.com/photo-1524758631624-e2822e304c36',
                    'https://images.unsplash.com/photo-1600566752355-35792bedcfea',
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be',
                ],

                'garaje' => [
                    'https://images.unsplash.com/photo-1503376780353-7e6692767b70',
                    'https://images.unsplash.com/photo-1487754180451-c456f719a1fc',
                    'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2',
                    'https://images.unsplash.com/photo-1583267746897-2cf415887172',
                    'https://images.unsplash.com/photo-1504215680853-026ed2a45def',
                    'https://images.unsplash.com/photo-1486006920555-c77dcf18193c',
                    'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf',
                    'https://images.unsplash.com/photo-1558981285-6f0c94958bb6',
                    'https://images.unsplash.com/photo-1549924231-f129b911e442',
                    'https://images.unsplash.com/photo-1592853598061-bb3f6f3e3a9f',
                ],

                'terreno' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef',
                    'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                    'https://images.unsplash.com/photo-1441974231531-c6227db76b6e',
                    'https://images.unsplash.com/photo-1470770841072-f978cf4d019e',
                    'https://images.unsplash.com/photo-1501785888041-af3ef285b470',
                    'https://images.unsplash.com/photo-1439853949127-fa647821eba0',
                    'https://images.unsplash.com/photo-1441974231531-c6227db76b6e',
                    'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429',
                    'https://images.unsplash.com/photo-1469474968028-56623f02e42e',
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef',
                ],

                'plano' => [
                    'https://images.unsplash.com/photo-1600585152915-d208bec867a1',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c',
                    'https://images.unsplash.com/photo-1582407947304-fd86f028f716',
                    'https://images.unsplash.com/photo-1600585153490-76fb20a32601',
                    'https://images.unsplash.com/photo-1580587771525-78b9dba3b914',
                    'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d',
                    'https://images.unsplash.com/photo-1600607688969-a5bfcd646154',
                    'https://images.unsplash.com/photo-1600607687644-c7171b42498f',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c',
                    'https://images.unsplash.com/photo-1600607688969-a5bfcd646154',
                ],
                'otro' => [
                    'https://images.unsplash.com/photo-1503387762-592deb58ef4e',
                    'https://images.unsplash.com/photo-1494526585095-c41746248156',
                    'https://images.unsplash.com/photo-1449157291145-7efd050a4d0e',
                    'https://images.unsplash.com/photo-1460472178825-e5240623afd5',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab',
                    'https://images.unsplash.com/photo-1479839672679-a46483c0e7c8',
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858',
                    'https://images.unsplash.com/photo-1482192505345-5655af888cc4',
                    'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5',
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511',
                ],
            ];
            $type = $data['type'];

            $data['main_image'] = $images[$type][array_rand($images[$type])];
            $photoIdx++;

            $property = Property::create($data);

            // Secondary images (2–3 per property)
            $secondaryCount = ($i % 2 === 0) ? 3 : 2;
            for ($j = 0; $j < $secondaryCount; $j++) {
                $seed = self::PHOTO_SEEDS[$photoIdx % count(self::PHOTO_SEEDS)];
                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $images[$type][array_rand($images[$type])],
                    'type'        => 'secondary',
                    'order'       => $j,
                ]);
                $photoIdx++;
            }

            // Floor plans (1–2 per property, grayscale)
            $planCount = ($i % 3 === 0) ? 2 : 1;
            for ($j = 0; $j < $planCount; $j++) {
                $seed = self::PLAN_SEEDS[$planIdx % count(self::PLAN_SEEDS)];
                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $images['plano'][array_rand($images['plano'])],
                    'type'        => 'floor_plan',
                    'order'       => $j,
                ]);
                $planIdx++;
            }
        }
    }
}
