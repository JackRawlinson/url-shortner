<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../src/ShortUrlController.php';
require_once __DIR__ . '/../src/util.php';

// Default index page
router('GET', '^/$', function() {
    $short_url = new ShortUrlController();
    $results = $short_url->read();

    http_response_code(200);
    echo 'Get' . json_encode($results);
});

router('GET', '^/(?<hash>.+)$', function($params) {
    echo $_SERVER['SERVER_NAME'];
    echo "Hash: ";
    var_dump($params);
});

router('POST', '^/shorten$', function() {
    $post = file_get_contents('php://input');
    $json = json_decode($post);
    $short_url = new ShortUrlController();
    $host = $_SERVER['HTTP_HOST'];

    if(!empty($json->url) && filter_var($json->url, FILTER_VALIDATE_URL) !== false)  {
        $result = $short_url->shorten($json->url, $host);
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