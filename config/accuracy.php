<?php



return [
    'tenant' => Domain\Users\Models\Company::class,
    'tenant_foreign_key' => 'company_id',
    'tenant_other_key' => 'id',
    'models' => [
        'user' => Domain\Users\Models\User::class,
    ]
];
