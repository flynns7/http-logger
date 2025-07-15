<?php
return [
    'endpoint' => env('LOG_ENDPOINT', 'http://10.16.4.3:8088/api/simulator-log/log'),
    'eventId' => env('LOG_EVENT_ID', 'ba6c3fd2-8ab7-48c5-a6e3-7a1fdc3d4f4b'),
    'service' => env('LOG_SERVICE_NAME', 'payment-gateway'),
    'userId' => env('LOG_USER_ID', 'user-10293'),
    'userType' => env('LOG_USER_TYPE', 'merchant'),
    //actionNameBy : 
    // route sample Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    // -route_name => users.edit
    // -action => App\Http\Controllers\UserController@edit
    // -uri => users/{id}/edit
    'actionNameBy' => env('LOG_ACTION_NAME_BY', "route_name"), 
];