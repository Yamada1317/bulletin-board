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
        // データベース操作
        $link = mysqli_connect('mysql57.tcagame01.sakura.ne.jp', 'tcagame01', 'tcagame2021', 'tcagame01_20j70050');
        $result = mysqli_query($link, 'SELECT * FROM messages ORDER BY created_at DESC');
        $messages = mysqli_fetch_all($result);
        mysqli_close($link);
        // 文字列（json形式）にして返す
        $response->getBody()->write(json_encode($messages, JSON_UNESCAPED_UNICODE));
        return $response;
    });
    //新規投稿API
    $app->post('/api/page', function (Request $request, Response $response) {
        $params = $request->getParsedBody();

        $user = $params['user'];
        $message = $params['message'];

        // データベース操作
        $link = mysqli_connect('mysql57.tcagame01.sakura.ne.jp', 'tcagame01', 'tcagame2021', 'tcagame01_20j70050');
        $stmt = mysqli_prepare($link, "INSERT INTO messages (user, message) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $user, $message);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //更新API
    $app->post('/api/page/update', function (Request $request, Response $response) {
        $params = $request->getParsedBody();
        $id = $params['id'];
        $user = $params['user'];
        $message = $params['message'];

        // データベース操作
        $link = mysqli_connect('mysql57.tcagame01.sakura.ne.jp', 'tcagame01', 'tcagame2021', 'tcagame01_20j70050');
        $stmt = mysqli_prepare($link, "UPDATE messages SET user = ?, message = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $user, $message, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    //削除API
    $app->post('/api/page/delete', function (Request $request, Response $response) {
        $params = $request->getParsedBody();
        $id = $params['id'];

        // データベース操作
        $link = mysqli_connect('mysql57.tcagame01.sakura.ne.jp', 'tcagame01', 'tcagame2021', 'tcagame01_20j70050');
        $stmt = mysqli_prepare($link, "DELETE FROM messages WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
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
