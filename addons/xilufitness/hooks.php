<?php

$default_listener = [
    'xilufitness_user_login_success' => [
        'addons\\xilufitness\\listener\\User'
    ],
    'xilufitness_user_account_change' => [
        'addons\\xilufitness\\listener\\User'
    ],
    'xilufitness_user_point_change' => [
        'addons\\xilufitness\\listener\\User'
    ],
    'xilufitness_medal_unlocking' => [
        'addons\\xilufitness\\listener\\User'
    ],
    'xilufitness_user_share' => [
        'addons\\xilufitness\\listener\\User'
    ]
];
return $default_listener;