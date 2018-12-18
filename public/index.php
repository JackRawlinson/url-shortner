<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/../src/Controllers/ShortUrlController.php';
require_once __DIR__ . '/../src/util.php';

// Default index page
router('GET', '^/$', function() {
    http_response_code(200);

    echo '<html><form action="/shorten" method="post">
      URL: <input type="text" name="url"><br>
      <input type="submit" value="Submit">
    </form></html>';
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

    $formUrl = $_POST['url'];

    // Handle both Form Encoded and Json
    $url = null;
    if($formUrl !== null) {
        $url = $formUrl;
    } else if($json->url !== null){
        $url = $json->url;
    }

    if(!empty($url) && filter_var($url, FILTER_VALIDATE_URL) !== false)  {
        $result = $urlcontroller->shorten($url, $host);
        if(!is_null($result)) {
            http_response_code(201);
            echo '<html>Shortened: <a href="'. $result . '">'. $result . '</a></html>';
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