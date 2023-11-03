<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/homework/src");
define("ERROR_MSG_PARAM", "%s : 필수 입력 사항입니다.");
require_once(ROOT."/lib/db_lib.php");

$conn = null;
$arr_post = $_POST;
$http_method = $_SERVER["REQUEST_METHOD"];
$arr_err_msg = [];
$title = "";
$memo = "";


if($http_method === "POST") {
    try {

        $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
        $memo = isset($_POST["memo"]) ? trim($_POST["memo"]) : "";

        if($title === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "제목");
        }
        if($memo === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용");
        }

        if(count($arr_err_msg) === 0) {
            if(!my_conn($conn)) {
                throw new Exception("DB Error : PDO Instance");
            }

        $conn -> beginTransaction();

        if(!db_insert_boards($conn, $arr_post)) {
            throw new Exception("DB Error : Insert Boards");
        }
        $conn -> commit();

        header("Location: list.php");
        exit;
        }
    } catch(Exception $e) {
        $conn->rollBack();
        echo $e->getMessage();
        exit;
    } finally {
        db_destroy_conn($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/homework/src/css/common.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+KR:wght@300" rel="stylesheet">
    <title>insert_page</title>
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
    
    <form class="insert_form" action="/homework/src/insert.php" method="post">
        <div class="form_txt">
            <label for="title">제목</label>
            <input type="text" name="title" id="title" value="<?php echo $title ?>">
    <br>
    <br>
            <label for="memo">내용</label>
            <textarea class ="insert_memo"name="memo" id="memo" cols="50" rows="10"><?php echo $memo ?></textarea>
        </div>
    <br>
    <br>
    
        <div class="form_btn">
            <button type="submit">작성</button>
            <a href="/homework/src/list.php">취소</a>
        </div>
    </form>

    </div>
</body>
</html>