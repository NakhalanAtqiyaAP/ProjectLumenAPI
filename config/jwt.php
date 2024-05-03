<?php
return [
    'ttl' => env('JWT_TTL', 60), // Waktu kedaluwarsa dalam menit (misalnya, 60 menit atau 1 jam)
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // TTL untuk token refresh dalam menit (misalnya, 14 hari)
    'algo' => 'HS256', // Algoritma enkripsi untuk token
    'secret' => env('JWT_SECRET'),
];
