<?php
/**
 * helperSosmed.php
 * Kumpulan fungsi untuk merapikan tampilan sosial media (Instagram, X,
 * YouTube, Lainnya) yang diisi user dalam bentuk bebas: bisa "@username",
 * "username" saja, atau link lengkap (https://instagram.com/username?utm=...).
 */

/**
 * Ubah satu nilai sosial media (IG / X / YouTube) jadi format "@username".
 * Jika nilainya berupa link, ambil bagian terakhir dari path URL sebagai username.
 *
 * @param string|null $value
 * @return array|null  ['label' => '@username', 'url' => string|null] atau null jika kosong
 */
function formatSosmedHandle($value) {
    $value = trim((string) $value);
    if ($value === '') return null;

    if (preg_match('#^https?://#i', $value)) {
        $url    = $value;
        $path   = trim((string) parse_url($value, PHP_URL_PATH), '/');
        $segmen = $path !== '' ? explode('/', $path) : [];
        $nama   = end($segmen);
        $nama   = ($nama !== false && $nama !== '') ? $nama : $url;
        return ['label' => '@' . $nama, 'url' => $url];
    }

    // Bukan link -> anggap sebagai username (boleh sudah pakai @ atau belum)
    $nama = ltrim($value, '@');
    return ['label' => '@' . $nama, 'url' => null];
}

/**
 * Untuk platform "Lainnya" (Linktree, TikTok, Website pribadi, dll).
 * Jika berupa link, tampilkan nama domainnya. Jika bukan link, tampilkan apa adanya.
 *
 * @param string|null $value
 * @return array|null  ['label' => string, 'url' => string|null] atau null jika kosong
 */
function formatSosmedLainnya($value) {
    $value = trim((string) $value);
    if ($value === '') return null;

    if (preg_match('#^https?://#i', $value)) {
        $host = parse_url($value, PHP_URL_HOST);
        return ['label' => $host ?: $value, 'url' => $value];
    }
    return ['label' => $value, 'url' => null];
}

/**
 * Kumpulkan semua sosial media dari satu baris data (array dari tempat_edukatif
 * atau pengajuan_tempat) menjadi list siap tampil.
 *
 * Kolom lama `sosial_media` (data lama) diperlakukan sebagai Instagram
 * jika kolom baru `sosmed_instagram` masih kosong.
 *
 * @param array $row
 * @return array  list of ['icon' => string, 'label' => string, 'url' => string|null, 'platform' => string]
 */
function daftarSosmed($row) {
    $hasil = [];

    // Instagram: kolom baru, fallback ke kolom lama sosial_media
    $ig = $row['sosmed_instagram'] ?? '';
    if ($ig === '' || $ig === null) {
        $ig = $row['sosial_media'] ?? '';
    }
    $f = formatSosmedHandle($ig);
    if ($f) $hasil[] = array_merge(['icon' => '📷', 'platform' => 'Instagram'], $f);

    // X (Twitter)
    $f = formatSosmedHandle($row['sosmed_x'] ?? '');
    if ($f) $hasil[] = array_merge(['icon' => '✖️', 'platform' => 'X'], $f);

    // YouTube
    $f = formatSosmedHandle($row['sosmed_youtube'] ?? '');
    if ($f) $hasil[] = array_merge(['icon' => '▶️', 'platform' => 'YouTube'], $f);

    // Lainnya
    $f = formatSosmedLainnya($row['sosmed_lainnya'] ?? '');
    if ($f) $hasil[] = array_merge(['icon' => '🔗', 'platform' => 'Lainnya'], $f);

    return $hasil;
}