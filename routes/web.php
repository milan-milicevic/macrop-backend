<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/pusher', 'ExampleController@sendEvent');

$router->post('api/register', 'UserController@register');

$router->group(['prefix' => 'api/', ], function () use ($router) {

    //user routes
    $router->get('user/{user}', 'UserController@get');
    $router->get('/user', 'UserController@getUser');
    $router->get('users', 'UserController@index');
    $router->patch('user/{user}', 'UserController@update');
    $router->delete('/user/{user}', 'UserController@delete');
    $router->get('/user/{user}/project', 'ProjectController@index');
    $router->get('/users/search', 'UserController@filter');
    $router->get('/user/{user}/event', 'EventController@getUserEvents');

    //project routes
    $router->post('/user/{user}/project', 'ProjectController@store');
    $router->get('/project/{project}', 'ProjectController@get');
    $router->patch('/project/{project}', 'ProjectController@update');
    $router->delete('/project/{project}', 'ProjectController@delete');
    $router->get('/project/{project}/story', 'StoryController@index');
    $router->post('/project/{project}/role', 'ProjectController@addMember');

    //story routes
    $router->get('/story/{story}', 'StoryController@get');
    $router->post('/project/{project}/story', 'StoryController@store');
    $router->patch('/story/{story}', 'StoryController@update');
    $router->delete('/story/{story}', 'StoryController@delete');
    $router->get('/story/{story}/card', 'CardController@index');

    //card routes
    $router->get('/card/{card}', 'CardController@get');
    $router->post('/story/{story}/card', 'CardController@store');
    $router->patch('/card/{card}', 'CardController@update');
    $router->delete('/card/{card}', 'CardController@delete');
    $router->get('/card/{card}/task', 'TaskController@index');

    //task routes
    $router->get('/task/{task}', 'TaskController@get');
    $router->post('/project/{project}/user/{user}/task', 'TaskController@store');
    $router->patch('/task/{task}', 'TaskController@update');
    $router->delete('/task/{task}', 'TaskSController@delete');
    $router->post('/task/{task}/file', 'TaskController@uploadFile');
    //$router->get('/task/{task}/file', 'TaskController@getFiles');
    $router->post('/task/{task}/user/{user}', 'TaskController@assingnTask');
    $router->get('/task/{task}/user', 'TaskController@getTaskUsers');

    //events routes
    $router->post('/user/{user}/event', 'EventController@store');
    $router->patch('/event/{event}', 'EventController@update');
    $router->post('/event/{event}/addMembers', 'EventController@addUsersToEvent');
    $router->get('/event/{event}/users', 'EventController@getEventUsers');
    //$router->delete('/event/')

    $router->get('/calendar/user/{user_id}/project/{project_id}', 'CalendarController@getMonth');

});
