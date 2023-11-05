<?php
// 상수 및 필요한 라이브러리를 정의
define("ROOT", $_SERVER["DOCUMENT_ROOT"] . "/homework/src");
require_once(ROOT . "/lib/db_lib.php");

$conn = null;

$total_page = 5; 
$list_cnt = 5; 
$page_num = 1; 

try {
    // 데이터베이스 연결 확인
    if (!my_conn($conn)) {
        throw new Exception("DB Error : PDO Instance");
    }

    // 전체 게시글 수 조회
    $boards_cnt = db_select_boards_cnt($conn);
    if ($boards_cnt === false) {
        throw new Exception("DB Error : SELECT Count");
    }

    $max_page_num = ceil($boards_cnt / $list_cnt); 

    // 현재 페이지 번호 설정
    if (isset($_GET["page"])) {
        $page_num = $_GET["page"];
    }
    
    $offset = ($page_num - 1) * $list_cnt; 

    // 이전 페이지 번호 계산
    $prev_page_num = $page_num - 1;
    if ($prev_page_num === 0) {
        $prev_page_num = 1;
    }

    // 다음 페이지 번호 계산
    $next_page_num = $page_num + 1;
    if ($next_page_num > $max_page_num) {
        $next_page_num = $max_page_num;
    } 

    $total_block = ceil($page_num / $total_page); 
    
    $start_block = ($total_block - 1) * $total_page + 1;

    $end_block = $start_block + $total_page - 1;

    if ($max_page_num > $total_page) {
        $total_block = 1;
    } 

    // DB 조회에 사용할 데이터 배열
    $arr_param = [
        "list_cnt" => $list_cnt,
        "offset" => $offset
    ];

    // 게시글 리스트 조회
    $result = db_select_boards_pasging($conn, $arr_param);
    
    // 게시글 조회 에러
    if (!$result) {
        throw new Exception("DB Error : SELECT boards Paging");
    }
}
catch (Exception $e) {
    echo $e->getMessage(); 
    exit;
}
finally {
    db_destroy_conn($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/homework/src/css/common.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+KR:wght@300" rel="stylesheet">
    <title>list_page</title>
</head>
<body>
    <div class="head text_align">
        <a href="/homework/src/list.php">BOARD</a>
    </div>

    <div class="main">
        <table class="text_align tab_main">
            <colgroup>
                <col width="20%">
                <col width="60%">
                <col width="20%">
            </colgroup>
            <tr class="list">
                <th class="list_title">번호</th>
                <th class="list_title">제목</th>
                <th class="list_title">작성일자</th>
            </tr>
            <?php
            foreach ($result as $item) {
            ?>
            <tr class="main_txt">
                <td><?php echo $item["id"]; ?></td>
                <td>
                    <a href="/homework/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                        <?php echo $item["title"]; ?>
                    </a>
                </td>
                <td><?php echo $item["create_date"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="btn text_align">
        <section>
            <a class="page_btn" href="/homework/src/list.php/?page=<?php echo $prev_page_num ?>">이전</a>
            <?php
            for ($i = 1; $i <= $max_page_num; $i++) {
                $str = (int)$page_num === $i ? "bk-a" : "";
            ?>
            <a class="page_btn <?php echo $str ?>" href="/homework/src/list.php/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php    
            }
            ?>
            
            <a class="page_btn" href="/homework/src/list.php/?page=<?php echo $next_page_num ?>">
            <?php if ($i === $total_block)?>다음<?php ?></a>
        </section>
    </div>
    <div class="insert_btn2">
        <a href="/homework/src/insert.php" class="text_align insert_btn">작성하기</a>
    </div>
</body>
</html>
