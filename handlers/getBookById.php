<?php
    $book_Id = substr($bookPath, 1);

    $result = pg_prepare($db_con, "get_a_book", 'SELECT * FROM books WHERE id = $1');
    $result = pg_execute($db_con, "get_a_book", array($book_Id));

    if ($row = pg_fetch_assoc($result)) {
        $book['id'] = $row['id'];
        $book['name'] = $row['name'];
        $book['year'] = (int) $row['year'];
        $book['author'] = $row['author'];
        $book['summary'] = $row['summary'];
        $book['publisher'] = $row['publisher'];
        $book['pageCount'] = (int) $row['page_count'];
        $book['readPage'] = (int) $row['read_page'];
        $book['finished'] = ($row['finished'] == "t") ? true : false;
        $book['reading'] = ($row['reading'] == "t") ? true : false;
        $book['insertedAt'] = $row['inserted_at'];
        $book['updatedAt'] = $row['updated_at'];

        http_response_code(200);
        $response['status'] = 'success';
        $response['data']['book'] = $book;

        echo json_encode($response);
    } else {
        http_response_code(404);
        $response = array("status" => "fail", "message" => "Buku tidak ditemukan");

        echo json_encode($response);
    }

    pg_free_result($result);
    pg_close($db_con);
?>