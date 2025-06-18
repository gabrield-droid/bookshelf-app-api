<?php
    $book_Id = substr($bookPath, 1);

    $result = pg_prepare($db_con, "get_the_book", 'SELECT id FROM books WHERE id = $1');
    $result = pg_execute($db_con, "get_the_book", array($book_Id));

    if (pg_num_rows($result) > 0) {
        $data_in_json = json_decode(file_get_contents('php://input'));

        $isName = isset($data_in_json->{'name'});
        $isReadPageLessOrEqualCount = $data_in_json->{'readPage'} <= $data_in_json->{'pageCount'};

        if ($isName && $isReadPageLessOrEqualCount) {
            $updatedAt = date_format(date_create(), 'Y-m-d\TH:i:s.v\V');

            $finished = ($data_in_json->{'pageCount'} === $data_in_json->{'readPage'}) ? 1 : 0;
            $reading = (int) $data_in_json->{'reading'};

            $result = pg_prepare($db_con, "edit_the_book", 'UPDATE books SET
                name = $1,
                year = $2,
                author = $3,
                summary = $4,
                publisher = $5,
                page_count = $6,
                read_page = $7,
                finished = $8,
                reading = $9,
                updated_at = $10
            WHERE id = $11');

            $result = pg_execute($db_con, "edit_the_book", array($data_in_json->{'name'}, $data_in_json->{'year'},
                $data_in_json->{'author'}, $data_in_json->{'summary'}, $data_in_json->{'publisher'},
                $data_in_json->{'pageCount'}, $data_in_json->{'readPage'}, $finished, $reading,
                $updatedAt, $book_Id)
            );

            http_response_code(200);
            $response = array("status" => "success", "message" => "Buku berhasil diperbarui");
            echo json_encode($response);
        } elseif (!$isName) {
            http_response_code(400);
            $response = array("status" => "fail", "message" => "Gagal memperbarui buku. Mohon isi nama buku");
            echo json_encode($response);
        } elseif (!$isReadPageLessOrEqualCount) {
            http_response_code(400);
            $response = array("status" => "fail", "message" => "Gagal memperbarui buku. readPage tidak boleh lebih besar dari pageCount");
            echo json_encode($response);
        }
    } else {
        http_response_code(404);
        $response = array("status" => "fail", "message" => "Gagal memperbarui buku. Id tidak ditemukan");
        echo json_encode($response);
    }

    pg_free_result($result);
    pg_close($db_con);
?>