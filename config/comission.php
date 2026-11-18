<?php

return [
    // Batas atas persentase yang boleh ditetapkan admin. Melindungi dari salah ketik (mis. 200 alih-alih 20).
    'max_percent' => (float) env('COMMISSION_MAX_PERCENT', 50),

    // Seberapa jauh ke depan tarif boleh dijadwalkan.
    'max_schedule_days' => 365,
];