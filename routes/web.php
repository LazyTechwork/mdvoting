<?php

Route::get('/', 'MainController@home')->name('home');
Route::get('/vi', 'MainController@vi')->name('vi');
Route::post('/sv', 'VoteController@startVoting');
Route::post('/ev', 'VoteController@endVoting');

// All guest routes
Route::middleware('guest')->group(function () {
//    Login
    Route::get('login', 'UserController@loginPage')->name('login');
    Route::post('login', 'UserController@login')->name('login');

//    Register
    Route::get('register', 'UserController@registerPage')->name('register');
    Route::post('register', 'UserController@register')->name('register');
});

// All authentificated routes
Route::middleware('auth')->group(function () {
    Route::get('logout', 'UserController@logout')->name('logout');
    Route::get('/gp', 'MainController@getParticipantsAPI');
    Route::get('/pl', 'VoteController@participantLink');

    Route::prefix('votings')->name('votings.')->group(function () {
//        New votings
        Route::get('new', 'MainController@newVotingPage')->name('new');
        Route::post('new', 'MainController@newVoting')->name('new');

//        Showing votings
        Route::get('/{id}', 'MainController@showVoting')->name('show');
        Route::get('/{id}/reset', 'MainController@resetCode')->name('resetcode');

//        Manipulating with variants
        Route::get('/{id}/variants', 'MainController@variantsPage')->name('variants');
        Route::post('/{id}/variants', 'MainController@variants')->name('variants');

//        Edit pages
        Route::get('/{id}/edit', 'MainController@editPage')->name('edit');
        Route::post('/{id}/edit', 'MainController@edit')->name('edit');

//        Locking changes
        Route::get('/{id}/lock', 'MainController@lock')->name('lock');
        Route::get('/{id}/dashboard', 'MainController@dashboard')->name('dashboard');

//        Participants
        Route::get('/{id}/participants', 'MainController@participantsPage')->name('participants');
        Route::post('/{id}/participants', 'MainController@participants')->name('participants');
    });
});
