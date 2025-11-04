<?php

return [
  'app_name' => 'Latsol SNBT Daily',
  'base_url' => 'https://latsolsnbt.candubelajar.my.id',
  'db_host'  => 'localhost',
  'db_name'  => 'latsol',
  'db_user'  => 'userdb',
  'db_pass'  => 'secret',
  'ai' => [
    'provider' => 'openai',
    'api_key' => 'XXXX',
    'model' => 'gpt-4o-mini',
    'timeout' => 20,
  ],
  'security' => [
    'csrf' => true,
    'rate_limit' => [ 'login' => [ 'max'=>5, 'window'=>60 ] ],
  ],
  'timers' => [
    'kejar_waktu' => 420,  // 7 menit untuk 5 soal
    'santai' => 1200      // 20 menit
  ]
];