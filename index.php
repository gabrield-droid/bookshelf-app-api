<?php
    header('Content-type: application/json; charset=utf-8');

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit(0);
    }

    $root_dir = __DIR__;
    $handlers_dir = $root_dir."/handlers";
    $functions_dir = $root_dir."/functions";
    $db_con_file = $root_dir."/postgresql/db_conn.php";

    require($db_con_file);

    if (substr($_SERVER['REQUEST_URI'], 0, 6) == "/books") {
        $bookPath = substr($_SERVER['REQUEST_URI'], 6);
        if ($bookPath) {
            if ($bookPath[0] == "/") {
                if (substr($bookPath, 1)) {
                    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                        require($handlers_dir."/getBookById.php"); exit;
                    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        require($handlers_dir."/editBookById.php"); exit;
                    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                        require($handlers_dir."/deleteBookById.php"); exit;
                    }
                } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    require($handlers_dir."/getAllBooks.php"); exit;
                } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    require($handlers_dir."/addBook.php"); exit;
                }
            } elseif ($bookPath[0] == "?") {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    require($handlers_dir."/getAllBooks.php"); exit;
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require($handlers_dir."/getAllBooks.php"); exit;  
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require($handlers_dir."/addBook.php"); exit;
        }
    }

    http_response_code(404);

    $response = array("statusCode" => http_response_code(), "error" => "Not Found", "message" => "Not Found");
    echo json_encode($response);

    pg_close($db_con);
?>