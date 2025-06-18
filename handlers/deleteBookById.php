<?php
    $book_Id = substr($bookPath, 1);

    $result = pg_prepare($db_con, "get_the_book", 'SELECT id FROM books WHERE id = $1');
    $result = pg_execute($db_con, "get_the_book", array($book_Id));

    if (pg_num_rows($result) > 0) {
        $result = pg_prepare($db_con, "delete_a_book", 'DELETE FROM books WHERE id = $1');
        $result = pg_execute($db_con, "delete_a_book", array($book_Id));

        http_response_code(200);
        $response = array("status" => "success", "message" => "Buku berhasil dihapus");
        echo json_encode($response);
    } else {
        http_response_code(404);
        $response = array("status" => "fail", "message" => "Buku gagal dihapus. Id tidak ditemukan");
        echo json_encode($response);
    }

    pg_free_result($result);
    pg_close($db_con);
?>