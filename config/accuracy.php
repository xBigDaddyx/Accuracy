<?php

use Domain\Users\Models\Company;
use Domain\Users\Models\User;

return [
    'tenant' => Company::class,
    'tenant_foreign_key' => 'company_id',
    'tenant_other_key' => 'id',
    'models' => [
        'user' => User::class,
    ]
];
