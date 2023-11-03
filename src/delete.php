<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/homework/src");
require_once(ROOT."/lib/db_lib.php");

try {
    $conn = null;
    if(!my_conn($conn)) {
        throw new Exception("DB Error : PDO Instance");
    }

    $http_method = $_SERVER["REQUEST_METHOD"];

    if($http_method === "GET") {
        $id = isset($_GET["id"]) ? $_GET["id"] : "";
        $page = isset($_GET["page"]) ? $_GET["page"] : "";
        $arr_err_msg = [];
        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }
        if($page === "") {
            $arr_err_msg[] = "Parameter Error : page";
        }
        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

        $arr_param = [
            "id" => $id
        ];
        $result = db_select_boards_id($conn, $arr_param);

        if($result === false) {
            throw new Exception("DB Error : Select id");
        } else if(!(count($result) === 1)) {
            throw new Exception("DB Error : Select id Count");
        }
        $item = $result[0];
    } else {

        $id = isset($_POST["id"]) ? $_POST["id"] : "";
        $arr_err_msg = [];
        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }
        if($page === "") {
            $arr_err_msg[] = "Parameter Error : page";
        }
        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

        $conn -> beginTransaction();
        $arr_param = [
            "id" => $id
        ];
        $result = db_delete_boards_id($conn, $arr_param);

        if(!$result) {
            throw new Exception("DB Error : Delete Boards id");
        }
        $conn->commit();
        header("Location: list.php");
        exit;
    }
} catch(Exception $e) {
    if($http_method === "POST") {
        $conn->rollBack();
    }
    echo $e->getMessage(); 
    exit; 
    
} finally {
    db_destroy_conn($conn);
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/homework/src/css/common.css">
    <title>delete_page</title>
</head>
<body>
    <table class="delet_table">
        <caption>
            <p>정말 삭제하시겠습니까?</p>
        </caption>
        <tr>
            <th>게시글 번호</th>
            <td><?php echo $item["id"] ?></td>
        </tr>
        <tr>
            <th>작성일</th>
            <td><?php echo $item["create_date"] ?></td>
        </tr>
        <tr>
            <th>제목</th>
            <td><?php echo $item["title"] ?></td>
        </tr>
        <tr>
            <th>내용</th>
            <td>><?php echo $item["memo"] ?></td>
        </tr>
    </table>
    <div class="delet_div">
        <form class="delet_form" action="/homework/src/delete.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit">동의</button>
            <a href="/homework/src/list.php/?id = <?php echo $id; ?>&page=<?php echo $page; ?>">취소</a>
        </form>
    </div>
</body>
</html>