<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
    ];

    public array $required = [
        'before' => [],
        'after'  => [
            'toolbar',
        ],
    ];

    // csrf aktif global untuk semua POST
    public array $globals = [
        'before' => [
            'csrf',
        ],
        'after' => [
            'secureheaders',
        ],
    ];

    public array $methods = [];
    public array $filters = [];
}