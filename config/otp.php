<?php

// Parameter OTP (GAP-09). Perlu Konfirmasi di PRD FR-AUTH-002; asumsi default
// dicatat di docs/DECISIONS.md (2026-10-24). Ubah di sini, bukan hardcode di kode.
return [
    'length' => (int) env('OTP_LENGTH', 6),
    'ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
    'resend_cooldown_seconds' => (int) env('OTP_RESEND_COOLDOWN_SECONDS', 60),
];
