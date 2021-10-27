<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    //投稿一覧の取得API
    $app->get('/api/page', function (Request $request, Response $response) {
        $link = mysqli_connect('localhost','root','','bulletin-board');
        $result = mysqli_query($link,'SELECT * FROM messages');
        $messages = mysqli_fetch_all($result;);
        mysqli_close($link);

        //文字列(json形式)にして返す
        $response->getBody()->write(json_encode($messages,JSON_UNESCAPED_UNICODE));
        return $response;

    });

    //新規投稿API
    $app->post('/api/page', function (Request $request, Response $response) {
        
        $link = mysqli_connect('localhost','root','','bulletin-board');
        $stmt = mysqli_prepare($link,"INSERT INTO massages (title, message) VALUES (?,?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $kana);
        mysqli_close($link);
    });


    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
