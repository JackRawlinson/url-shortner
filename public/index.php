<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../src/ShortUrlController.php';
require_once __DIR__ . '/../src/util.php';

// Default index page
router('GET', '^/$', function() {
    $short_url = new ShortUrlController();
    $results = $short_url->get();
    http_response_code(200);
    echo json_encode($results, JSON_UNESCAPED_SLASHES);
});

router('GET', '^/(?<hash>.+)$', function($params) {
    $hashid = $params["hash"];
    $urlcontroller = new ShortUrlController();

    if(strlen($hashid) === 8) {
        $url = $urlcontroller->getForHashId($hashid);
        header('Location: '.$url, true, 302);
        die();
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Not Found"));
    }
});

router('POST', '^/shorten$', function() {
    $post = file_get_contents('php://input');
    $json = json_decode($post);
    $urlcontroller = new ShortUrlController();
    $host = $_SERVER['HTTP_HOST'];

    if(!empty($json->url) && filter_var($json->url, FILTER_VALIDATE_URL) !== false)  {
        $result = $urlcontroller->shorten($json->url, $host);
        if(!is_null($result)) {
            http_response_code(201);
            echo json_encode(array("url" => $result), JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Internal Server Error"));
        }

    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Missing or Invalid URL to shorten."));
    }
});


header("HTTP/1.0 404 Not Found");
echo '404 Not Found';