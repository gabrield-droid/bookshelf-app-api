<?php
    require($functions_dir."/id_generator.php");

    $data_in_json = json_decode(file_get_contents('php://input'));

    $result = pg_prepare($db_con, "add_book", 'INSERT INTO books (id, name, year, author,
        summary, publisher, page_count, read_page, finished, reading, inserted_at, updated_at)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)');

    $id = generate_id(16);

    $finished = ($data_in_json->{'pageCount'} === $data_in_json->{'readPage'}) ? 1 : 0;
    $reading = (int) $data_in_json->{'reading'};

    $insertedAt = date_format(date_create(), 'Y-m-d\TH:i:s.v\V');
    $updatedAt = $insertedAt;

    $isName = isset($data_in_json->{'name'});
    $isReadPageLessOrEqualPageCount = $data_in_json->{'readPage'} <= $data_in_json->{'pageCount'};

    if ($isName && $isReadPageLessOrEqualPageCount) {
        $result = pg_execute($db_con, "add_book", array($id, $data_in_json->{'name'}, $data_in_json->{'year'},
            $data_in_json->{'author'}, $data_in_json->{'summary'}, $data_in_json->{'publisher'},
            $data_in_json->{'pageCount'}, $data_in_json->{'readPage'}, $finished, $reading,
            $insertedAt, $updatedAt)
        );
        $isSuccess = pg_num_rows(pg_query_params($db_con, 'SELECT id FROM books WHERE id = $1', array($id))) > 0;
        if ($isSuccess) {
            http_response_code(201);
            $response = array("status" => "success", "message" => "Buku berhasil ditambahkan");
            $response['data']['bookId'] = $id;
            echo json_encode($response);
        }
    } elseif (!$isName) {
        http_response_code(400);
        $response = array("status" => "fail", "message" => "Gagal menambahkan buku. Mohon isi nama buku");
        echo json_encode($response);
    } elseif (!$isReadPageLessOrEqualPageCount) {
        http_response_code(400);
        $response = array("status" => "fail", "message" => "Gagal menambahkan buku. readPage tidak boleh lebih besar dari pageCount");
        echo json_encode($response);
    } else {
        http_response_code(500);
        $response = array("status" => "fail", "message" => "Gagal menambahkan buku");
        echo json_encode($response);
    }

    pg_free_result($result);
    pg_close($db_con);
?>