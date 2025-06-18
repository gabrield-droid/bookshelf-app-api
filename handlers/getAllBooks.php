<?php
    $filterNum = 0;

    $filter = ["string" => "", "values" => []];

    if (isset($_GET['name'])) {
        $name = '%'.$_GET['name'].'%';
        if ($filterNum == 0) {
            $filter['string'] = " WHERE name ILIKE $1";
            $filter['values'][0] = $name;
            $filterNum++;
        }
    }

    if (isset($_GET['reading'])) {
        $reading = ($_GET['reading'] == "1") ? "t" : "f";
        if ($filterNum == 0) {
            $filter['string'] = " WHERE reading = $1";
            $filter['values'][0] = $reading;
            $filterNum++;
        } else {
            $filter['string'] .= " AND reading = $2";
            array_push($filter['values'], $reading);
            $filterNum++;
        }
    }

    if (isset($_GET['finished'])) {
        $finished = ($_GET['finished'] == "1") ? "t" : "f";
        if ($filterNum == 0) {
            $filter['string'] = " WHERE finished = $1";
            $filter['values'][0] = $finished;
            $filterNum++;
        } else {
            $filter['string'] .= " AND finished = $".($filterNum+1);
            array_push($filter['values'], $finished);
        }
    }

    $result = false;

    if ($filterNum > 0) {
        $result = pg_prepare($db_con, "get_all_books", "SELECT id, name, publisher FROM books $filter[string]");
        $result = pg_execute($db_con, "get_all_books", $filter['values']);
    } else {
        $result = pg_query($db_con, 'SELECT id, name, publisher FROM books');
    }

    $books = [];
    while ($row = pg_fetch_assoc($result)) {
        $book['id'] = $row['id'];
        $book['name'] = $row['name'];
        $book['publisher'] = $row['publisher'];

        array_push($books, $book);
    }

    http_response_code(200);
    $response['status'] = 'success';
    $response['data']['books'] = $books;
    echo json_encode($response);

    pg_free_result($result);
    pg_close($db_con);
?>