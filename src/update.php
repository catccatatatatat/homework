<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/homework/src");
define("ERROR_MSG_PARAM", "%s : 필수 입력 사항입니다.");
require_once(ROOT."/lib/db_lib.php");

$conn = null;
$http_method = $_SERVER["REQUEST_METHOD"];
$arr_err_msg = [];
$title = "";
$memo = "";
$id = "";

try {
    if(!my_conn($conn)) {
        throw new Exception("DB Error : PDO Instance");
    }

    if($http_method === "GET") {

        $id = isset($_GET["id"]) ? $_GET["id"] : $_POST["id"];
        $page = isset($_GET["page"]) ? $_GET["page"] : $_POST["page"];
        if($id === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
        }
        if($page === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
        }
        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

    } else {
        $id = isset($_POST["id"]) ? $_POST["id"] : "";
        $page = isset($_POST["page"]) ? $_POST["page"] : "";
        $memo = isset($_POST["memo"]) ? $_POST["memo"] : "";
        $title = isset($_POST["title"]) ? $_POST["title"] : "";

        if($id === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
        }
        if($page === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
        }
        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

        if($title === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "title");
        }
        if($memo === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "memo");
        }

        if(count($arr_err_msg) === 0) {
            
        $arr_param = [
            "id" => $id
            ,"title" => $_POST["title"]
            ,"memo" => $_POST["memo"]
        ];

        $conn->beginTransaction();

        if(!db_update_boards_id($conn, $arr_param)) {
            throw new Exception("DB Error : Update_Boards_id");
        }
        $conn->commit();

        header("Location: detail.php/?id={$id}&page={$page}");
        exit;
        }
    }
    
    $arr_param = [
        "id" => $id
    ];

    $result = db_select_boards_id($conn, $arr_param);

        if($result === false) {
            throw new Exception("DB Error : PDO Select_id");
        } else if(!(count($result)) === 1) {
            throw new Exception("DB Error : PDO Select_id count".count($result));
        }
        $item = $result[0];

} catch(Exception $e) {
    if($http_method === "POST") {
        $conn -> rollBack();
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
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+KR:wght@300" rel="stylesheet">
    <title>update_page</title>
</head>
<body>

    <div class="head text_align">
        <a href="/homework/src/list.php">BOARD</a>
    </div>

    <div>
        <?php
            foreach($arr_err_msg as $val) {
        ?>
                <p><?php echo $val?></p>        
        <?php            
            }
        ?>
    
    <form class="update_form text_ailgn" action="/homework/src/update.php" method="post">
        <table class="update_table">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
        
            <tr>
                <th>글번호</th>
                <td><?php echo $item["id"]; ?></td>
            </tr>
            <tr>
                <th>제목</th>
                <td><input class="update_input" type="text" name="title" value="<?php echo $item["title"] ?>"></td>
            </tr>
            <tr>
                <th>내용</th>
                <td><textarea class="update_memo" name="memo" id="memo" cols="70" rows="10"><?php echo $item["memo"]?></textarea></td>
            </tr>
        <!-- </div> -->
        </table>
    
        <div class="update_btn">
            <button type="submit">수정</button>
            <a href="/homework/src/list.php/?page=<?php echo $id; ?>&page=<?php echo $page; ?>">취소</a>
        </div>
    </form>
    
    </div>

</body>
</html>