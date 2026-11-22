<?php

namespace Database\Seeders;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Venue, lapangan, foto, fasilitas lapangan, jadwal mingguan, dan blokir. */
class DemoListingSeeder extends DemoBase
{
    public function run(): void
    {
        $this->ensureStarted();
        $this->seedListings();
    }

    private function seedListings(): void
    {
        $vid = 0;
        $fid = 0;
        $venueRows = $fieldRows = $photoRows = $facRows = $schedRows = [];
        $facilityNames = array_keys($this->facilityIds);
        $sportLabel = ['futsal' => 'Futsal', 'badminton' => 'Badminton', 'mini-soccer' => 'Mini Soccer', 'basket' => 'Basket', 'tenis' => 'Tenis', 'voli' => 'Voli', 'tenis-meja' => 'Tenis Meja'];

        foreach ($this->venueSpecs() as [$mk, $vname, $city, $addr, $lat, $lng, $open, $close, $vstatus, $live, $fieldSpecs]) {
            $vid++;
            $m = $this->mitra[$mk];
            $liveAt = $this->at($live)->setTime(mt_rand(9, 15), mt_rand(0, 59), 0);
            [$created, $submitted, $reviewed] = $this->listingDates($vstatus, $liveAt);
            $reason = $vstatus === 'rejected' ? 'Foto lapangan belum jelas dan titik peta tidak sesuai alamat. Mohon perbarui lalu ajukan ulang.' : null;

            $venueRows[] = [
                'id' => $vid, 'mitra_id' => $m['id'], 'city_id' => $this->cityIds[$city], 'nama_venue' => $vname,
                'alamat_lengkap' => $addr,
                'deskripsi' => $vname . ' berlokasi di ' . $city . '. Fasilitas lengkap, area parkir luas, dan pemesanan mudah lewat SportPlace.',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng,
                'jam_buka' => $open . ':00', 'jam_tutup' => $close . ':00', 'status_vanue' => $vstatus,
                'alasan_penolakan' => $reason,
                'reviewed_by' => $reviewed ? $this->adminId : null, 'reviewed_at' => $reviewed ? $this->dt($reviewed) : null,
                'created_at' => $this->dt($created), 'updated_at' => $this->dt($reviewed ?? $submitted ?? $created),
                'submitted_at' => $submitted ? $this->dt($submitted) : null,
            ];

            foreach ($fieldSpecs as $spec) {
                $fid++;
                [$fname, $sport, $price] = $spec;
                $fstatus = $spec[3] ?? ($vstatus === 'aktif' ? 'aktif' : ($vstatus === 'pending_review' ? 'pending_review' : 'draft'));
                $p = $this->sportProfile($sport);
                $fLive = $liveAt->modify('+' . mt_rand(0, 2) . ' hours');
                [$fCreated, $fSubmitted, $fReviewed] = $this->listingDates($fstatus, $fLive);
                $len = $p['len'][0] === $p['len'][1] ? $p['len'][0] : mt_rand((int) $p['len'][0], (int) $p['len'][1]);
                $wid = $p['wid'][0] === $p['wid'][1] ? $p['wid'][0] : mt_rand((int) $p['wid'][0], (int) $p['wid'][1]);
                $surface = $this->pick($p['surface']);
                if ($sport === 'voli' && stripos($fname, 'Pasir') !== false) {
                    $surface = 'Pasir pantai';
                }
                $type = stripos($fname, 'Pasir') !== false || $sport === 'tenis' || $sport === 'mini-soccer' ? 'outdoor' : $this->pick($p['type']);
                $siang = max(20000, (int) (floor($price * 0.8 / 5000) * 5000));
                $reasonF = $fstatus === 'rejected' ? 'Ukuran dan jenis permukaan lapangan tidak sesuai dengan foto yang dilampirkan.' : null;

                $fieldRows[] = [
                    'id' => $fid, 'venue_id' => $vid, 'sport_id' => $this->sportIds[$sport], 'nama_lapangan' => $fname,
                    'harga_perjam' => $price,
                    'deskripsi' => $fname . ' di ' . $vname . ': lapangan ' . strtolower($sportLabel[$sport]) . ' ' . $type . ' dengan permukaan ' . strtolower($surface) . ', terawat dan siap pakai.',
                    'tipe_bangunan' => $type, 'jenis_permukaan' => $surface, 'panjang' => $len, 'lebar' => $wid,
                    'kapasitas' => $p['cap'], 'aturan_lapangan' => $p['rules'],
                    'meta_harga' => json_encode(['siang' => $siang, 'malam' => $price]),
                    'status_lapangan' => $fstatus, 'alasan_penolakan' => $reasonF,
                    'reviewed_by' => $fReviewed ? $this->adminId : null, 'reviewed_at' => $fReviewed ? $this->dt($fReviewed) : null,
                    'created_at' => $this->dt($fCreated), 'updated_at' => $this->dt($fReviewed ?? $fSubmitted ?? $fCreated),
                    'submitted_at' => $fSubmitted ? $this->dt($fSubmitted) : null,
                ];

                // Foto asli (public/images/demo/lapangan): hingga 3 per lapangan, yang pertama utama.
                foreach ($this->fieldPhotoPaths($sport, $fname, $fid) as $i => $rel) {
                    $photoRows[] = ['field_id' => $fid, 'foto_url' => $rel, 'is_primary' => $i === 0 ? 1 : 0];
                }

                // Fasilitas: 4 sampai 7 per lapangan.
                $picked = $this->sample($facilityNames, mt_rand(4, 7));
                foreach (['Toilet & Shower', 'Parkir Luas'] as $must) {
                    if (! in_array($must, $picked, true)) {
                        $picked[] = $must;
                    }
                }
                foreach (array_unique($picked) as $fname2) {
                    $facRows[] = ['field_id' => $fid, 'facilities_id' => $this->facilityIds[$fname2]];
                }

                // Jadwal mingguan (1 = Senin ... 7 = Minggu).
                $sched = [];
                for ($d = 1; $d <= 7; $d++) {
                    $closeDay = $d === 7 && $this->chance(0.4) ? '21:00' : $close;
                    $status = ($d === 1 && $fname === 'Lapangan Utama') ? 'tutup' : 'buka';
                    $sched[$d] = ['status' => $status, 'open' => $this->minutes($open), 'close' => $this->minutes($closeDay)];
                    $schedRows[] = [
                        'field_id' => $fid, 'hari' => $d, 'jam_buka' => $open . ':00', 'jam_tutup' => $closeDay . ':00',
                        'durasi' => '01:00:00', 'status_hari' => $status,
                        'created_at' => $this->dt($fCreated), 'updated_at' => $this->dt($fCreated),
                    ];
                }

                $isBookable = $fstatus === 'aktif' && $vstatus === 'aktif' && $m['status'] === 'approved';
                $this->fields[$fid] = [
                    'id' => $fid, 'venue_id' => $vid, 'mitra_key' => $mk, 'mitra_id' => $m['id'], 'sport' => $sport,
                    'name' => $fname, 'venue' => $vname, 'price' => $price, 'sched' => $sched, 'live' => $fReviewed ?? $fLive,
                    'weight' => $p['weight'] * (0.6 + mt_rand(0, 120) / 100), 'status' => $fstatus, 'blocks' => [],
                ];
                if ($isBookable) {
                    $this->bookable[] = $fid;
                }
            }
        }

        $this->bulk('venues', $venueRows);
        $this->bulk('fields', $fieldRows);
        $this->bulk('field_photo', $photoRows);
        $this->bulk('field_facilities', $facRows);
        $this->bulk('field_schedule', $schedRows);
        $this->seedBlocks();
    }

    private function venueSpecs(): array
    {
        // [mitra, nama, kota, alamat, lat, lng, buka, tutup, status, live(hari), lapangan[]]
        // lapangan: [nama, olahraga, harga/jam, status]
        return [
            ['champion', 'Champion Futsal Arena', 'Denpasar', 'Jl. Teuku Umar Barat No. 88, Denpasar Barat', -8.6620, 115.1930, '08:00', '23:00', 'aktif', -205,
                [['Lapangan Vinyl A', 'futsal', 180000], ['Lapangan Vinyl B', 'futsal', 180000], ['Lapangan Sintetis C', 'futsal', 160000]]],
            ['champion', 'Champion Badminton Hall', 'Denpasar', 'Jl. Danau Tamblingan No. 21, Sanur, Denpasar Selatan', -8.6930, 115.2630, '07:00', '23:00', 'aktif', -195,
                [['Court 1', 'badminton', 75000], ['Court 2', 'badminton', 75000], ['Court 3', 'badminton', 70000], ['Court 4 (Karpet Turnamen)', 'badminton', 90000]]],
            ['champion', 'Champion Mini Soccer Park', 'Badung', 'Jl. Raya Canggu No. 45, Kuta Utara', -8.6478, 115.1385, '07:00', '23:00', 'aktif', -170,
                [['Lapangan Utama', 'mini-soccer', 420000], ['Lapangan Latihan', 'mini-soccer', 360000]]],
            ['viva', 'Viva Arena Futsal', 'Denpasar', 'Jl. Raya Puputan No. 12, Renon, Denpasar Selatan', -8.6740, 115.2370, '07:00', '23:00', 'aktif', -180,
                [['Lapangan Vip', 'futsal', 225000], ['Lapangan Reguler 1', 'futsal', 175000], ['Lapangan Reguler 2', 'futsal', 175000], ['Lapangan Premium D', 'futsal', 260000, 'pending_review']]],
            ['viva', 'Viva Badminton Center', 'Denpasar', 'Jl. Tukad Badung No. 7, Denpasar Timur', -8.6700, 115.2290, '08:00', '22:00', 'aktif', -150,
                [['Court A', 'badminton', 65000], ['Court B', 'badminton', 65000], ['Court C (VIP)', 'badminton', 85000]]],
            ['smash', 'Smash Center Arena', 'Badung', 'Jl. Goa Gong No. 3, Jimbaran, Kuta Selatan', -8.7970, 115.1670, '07:00', '23:00', 'aktif', -135,
                [['Lapangan A', 'badminton', 80000], ['Lapangan B', 'badminton', 80000], ['Lapangan C', 'badminton', 80000], ['Lapangan D (Turnamen)', 'badminton', 95000], ['Meja Pingpong 1', 'tenis-meja', 40000], ['Meja Pingpong 2', 'tenis-meja', 40000]]],
            ['hoops', 'Hoops Hall Kuta Selatan', 'Badung', 'Jl. Pantai Kedonganan No. 19, Kuta Selatan', -8.7560, 115.1640, '08:00', '23:00', 'aktif', -115,
                [['Full Court', 'basket', 300000], ['Half Court', 'basket', 160000], ['Lapangan Voli Indoor', 'voli', 140000]]],
            ['tabanan', 'Tabanan Tennis Club', 'Tabanan', 'Jl. Raya Kediri No. 66, Kediri, Tabanan', -8.5890, 115.1050, '06:00', '22:00', 'aktif', -105,
                [['Court 1 (Hard Court)', 'tenis', 150000], ['Court 2 (Hard Court)', 'tenis', 175000], ['Court 3 (Tanah Liat)', 'tenis', 200000, 'nonaktif']]],
            ['gianyar', 'Gianyar Sport Complex', 'Gianyar', 'Jl. Ngurah Rai No. 101, Gianyar', -8.5400, 115.3260, '07:00', '22:00', 'aktif', -70,
                [['Futsal Lapangan 1', 'futsal', 170000], ['Futsal Lapangan 2', 'futsal', 170000], ['Mini Soccer Rumput Sintetis', 'mini-soccer', 380000], ['Voli Pasir', 'voli', 120000, 'rejected']]],
            ['gianyar', 'Ubud Futsal & Mini Soccer', 'Gianyar', 'Jl. Raya Mas No. 58, Ubud, Gianyar', -8.5160, 115.2530, '08:00', '22:00', 'pending_review', -2,
                [['Lapangan Futsal Ubud', 'futsal', 190000, 'pending_review'], ['Lapangan Mini Soccer Ubud', 'mini-soccer', 400000, 'pending_review']]],
            ['hoops', 'Denpasar Basket Hall', 'Denpasar', 'Jl. Diponegoro No. 150, Denpasar Barat', -8.6710, 115.2060, '09:00', '22:00', 'rejected', -9,
                [['Lapangan Basket Indoor', 'basket', 280000, 'draft']]],
            ['tabanan', 'Sanur Tennis & Pickle Court', 'Denpasar', 'Jl. Sekar Waru No. 5, Sanur, Denpasar Selatan', -8.6800, 115.2620, '07:00', '21:00', 'draft', -1,
                [['Court Tenis Sanur', 'tenis', 190000, 'draft']]],
        ];
    }

    private function sportProfile(string $slug): array
    {
        return [
            'futsal' => ['type' => ['indoor'], 'surface' => ['Vinyl', 'Rumput sintetis', 'Interlock', 'Parket'], 'len' => [25, 40], 'wid' => [15, 20], 'cap' => '10 pemain + cadangan', 'weight' => 1.6,
                'rules' => 'Wajib memakai sepatu futsal atau sepatu non-marking. Dilarang membawa makanan dan minuman ke area lapangan. Keterlambatan tidak memperpanjang durasi sewa.'],
            'badminton' => ['type' => ['indoor'], 'surface' => ['Karpet PVC (standar BWF)', 'Parket kayu', 'Vinyl'], 'len' => [13.4, 13.4], 'wid' => [6.1, 6.1], 'cap' => '2 sampai 4 pemain', 'weight' => 0.75,
                'rules' => 'Gunakan sepatu indoor non-marking. Shuttlecock dibawa sendiri atau disewa di resepsionis. Mohon menjaga kebersihan lantai.'],
            'mini-soccer' => ['type' => ['outdoor'], 'surface' => ['Rumput sintetis'], 'len' => [50, 60], 'wid' => [30, 40], 'cap' => '14 sampai 16 pemain', 'weight' => 1.0,
                'rules' => 'Dilarang memakai sepatu bersol logam. Lapangan dapat ditutup sementara saat hujan deras atau petir demi keselamatan pemain.'],
            'basket' => ['type' => ['indoor'], 'surface' => ['Parket', 'Cor semen dengan lapisan akrilik'], 'len' => [28, 28], 'wid' => [15, 15], 'cap' => '10 sampai 14 pemain', 'weight' => 0.9,
                'rules' => 'Gunakan sepatu basket indoor. Dilarang bergelantungan pada ring. Bola dapat dipinjam dengan jaminan identitas.'],
            'tenis' => ['type' => ['outdoor'], 'surface' => ['Hard court', 'Tanah liat'], 'len' => [23.77, 23.77], 'wid' => [10.97, 10.97], 'cap' => '2 sampai 4 pemain', 'weight' => 0.8,
                'rules' => 'Wajib memakai sepatu tenis. Pemain membawa raket dan bola sendiri. Pembatalan karena hujan mengikuti kebijakan venue.'],
            'voli' => ['type' => ['indoor'], 'surface' => ['Parket', 'Pasir pantai'], 'len' => [18, 18], 'wid' => [9, 9], 'cap' => '12 pemain', 'weight' => 0.7,
                'rules' => 'Tersedia net standar. Pemain wajib melepas sepatu pada lapangan pasir. Dilarang membawa minuman manis ke area lapangan.'],
            'tenis-meja' => ['type' => ['indoor'], 'surface' => ['Lantai vinyl, meja standar ITTF'], 'len' => [2.74, 2.74], 'wid' => [1.53, 1.53], 'cap' => '2 sampai 4 pemain', 'weight' => 0.5,
                'rules' => 'Bet dan bola dapat disewa. Mohon tidak bersandar atau duduk di atas meja.'],
        ][$slug];
    }

    /** @return array{0:DateTimeImmutable,1:?DateTimeImmutable,2:?DateTimeImmutable} dibuat, diajukan, ditinjau */
    private function listingDates(string $status, DateTimeImmutable $live): array
    {
        if (in_array($status, ['aktif', 'nonaktif'], true)) {
            return [$live->modify('-4 days'), $live->modify('-2 days'), $live];
        }
        if ($status === 'rejected') {
            $submitted = $live->modify('-1 day');
            return [$submitted->modify('-2 days'), $submitted, $live];
        }
        if ($status === 'pending_review') {
            $submitted = $this->now->modify('-' . mt_rand(5, 60) . ' hours');
            return [$submitted->modify('-1 day'), $submitted, null];
        }

        return [$this->now->modify('-' . mt_rand(20, 50) . ' hours'), null, null]; // draft
    }

    private function seedBlocks(): void
    {
        $reasons = ['Perawatan lantai dan pengecatan garis', 'Penggantian jaring dan tiang', 'Turnamen internal komunitas', 'Perbaikan lampu penerangan', 'Dipakai kegiatan sekolah (booking langsung)', 'Pembersihan dan pemeliharaan rutin'];
        $rows = [];
        $targets = $this->sample($this->bookable, 7);
        $offsets = [-40, -26, -12, 3, 5, 9, 0];
        foreach ($targets as $i => $fid) {
            $start = $this->today->modify(($offsets[$i] >= 0 ? '+' : '') . $offsets[$i] . ' days');
            $days = $this->pick([0, 0, 1, 2]);
            $end = $start->modify('+' . $days . ' days');
            $from = $this->pick([8, 9, 13, 14]) * 60;
            $to = $from + $this->pick([2, 3, 4]) * 60;
            if ($offsets[$i] === 0) {
                $from = 13 * 60;
                $to = 16 * 60;
            }
            $this->fields[$fid]['blocks'][] = [$start, $end, $from, $to];
            $rows[] = [
                'field_id' => $fid, 'tanggal_mulai' => $this->dt($start), 'tanggal_selesai' => $this->dt($end),
                'jam_mulai' => $this->hm($from) . ':00', 'jam_selesai' => $this->hm($to) . ':00',
                'alasan_pemblokiran' => $reasons[$i % count($reasons)],
                'created_at' => $this->dt($start->modify('-6 days')), 'updated_at' => $this->dt($start->modify('-6 days')),
            ];
        }
        $this->bulk('field_blocks', $rows);
    }
}
