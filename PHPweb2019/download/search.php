<?php
session_start();

$num = NULL;
if(isset($_REQUEST['num'])){
    $num = $_REQUEST['num'];
}

$page = 0;
if(isset($_REQUEST['page'])){
    $page = $_REQUEST['page'];
}

$find = NULL;
if(isset($_REQUEST['find'])){
    $find = $_REQUEST['find'];
}

$search = NULL;
if(isset($_REQUEST['search'])){
    $search = $_REQUEST['search'];
}

$scale = 5;   // 한 화면에 표시되는 글 수

include "../dbconn.php";

$sql = "select * from down_board where $find like '%$search%'
           order by group_num desc, ord asc";

$result = $connect->query($sql) or die($this->_connect->error);
$count = mysqli_num_rows($result);
$row = $result->fetch_array();

if($count==0){
    echo("<script>window.alert('등록된 게시물이 없습니다.'); history.go(-1) </script>");
    exit;
}
?>
<html>
<META http-equiv="Content-Type" content="text/html; charset=Korean">
<head>
    <title> :: 골드문트의 PHP 5 :: </title>
    <link rel="stylesheet" href="../style.css" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table border=0 cellspacing=0 cellpdding=0 width='776' align='center'>
    <tr>
        <td colspan="5" height=25><img src="img/down_title.gif"></td>
    </tr>
    <tr>
        <td background="img/bbs_bg.gif">
            <img border="0" src="img/blank.gif" width="1" height="3"></td>
    </tr>
    <tr>
        <td height=10></td>
        <?php $total_record = mysqli_num_rows($result); // 전체 글 수?>
    <tr>
        <td align="right" colspan="5" height=20>전체
            <?php echo $total_record; ?>건
        </td></tr>
    <tr>
        <td>
            <table border=0 cellspacing=0 cellpdding=0 width='100%' class="txt">
                <tr bgcolor="#5AB2C8">
                    <td colspan="7" height=1></td>
                </tr>
                <tr bgcolor="#D2EAF0" height=25>
                    <td width="40" align="center"><strong>번호</strong></td>
                    <td width="400" align=center><strong>제목</strong></td>
                    <td width="40" align=center><strong>파일</strong></td>
                    <td width="50" align=center><strong>크기</strong></td>
                    <td width="76" align=center><strong>글쓴이</strong></td>
                    <td width="80" align=center><strong>작성일</strong></td>
                    <td width="50" align=center><strong>조회</strong></td>
                </tr>
                <tr bgcolor="#5AB2C8">
                    <td colspan="7" height=1></td>
                </tr>

                <?php
                // 전체 페이지 수($total_page) 계산
                if ($total_record % $scale == 0)
                    // $total_record를 $scale로 나눈 나머지 계산
                    $total_page = floor($total_record/$scale);
                // 나머지가 0일 때
                else
                    $total_page = floor($total_record/$scale) + 1;
                // 나머지가 0이 아닐 때

                if (!$page)
                    // 페이지번호($page)가 0 일 때
                    $page = 1;
                // 페이지 번호를 1로 초기화

                $start = ($page - 1) * $scale;
                // 표시할 페이지($page)에 따라 $start 계산

                $number = $total_record - $start;

                for ($i=$start; $i<$start+$scale && $i < $total_record; $i++)
                {
                    mysqli_data_seek($result, $i);
                    // 가져올 레코드로 위치(포인터) 이동
                    $row = $result->fetch_array();
                    // 하나의 레코드 가져오기

                    $day = substr($row[regist_day], 0, 10);

                    $sql = "select * from down_ripple where parent = '$row[num]'";

                    $result2 = $connect->query($sql) or die($this->_connect->error);
                    $num_ripple = mysqli_num_rows($result2);

                    $space = "";

                    for ($j=0; $j<$row[depth]; $j++)
                        $space = "&nbsp;&nbsp;".$space;

                    // 레코드 화면에 출력하기
                    echo "
        <tr height=25>
          <td align=center>$number</td>
          <td> $space 
             ";
                    if ($row[depth]>0)
                        echo "<img src='img/reply_head.gif' border=0>";
                    else
                        echo "<img src='img/record_id.gif' border=0>";

                    echo "<a href='view.php?num=$row[num]&page=$page'>
      &nbsp;$row[subject]";

                    if ($num_ripple) echo " <font color=blue>[$num_ripple]</font>";

                    echo "
            </a></td>
           ";

                    if ($row[filename])
                    {
                        echo "
          <td align=center><a href='data/$row[filename]'>
            <img src='img/file.gif' border=0></a></td>
          <td align=center>$row[filesize]</td>
           ";
                    }
                    else
                    {
                        echo "
          <td align=center></td>
          <td align=center></td>
           ";
                    }

                    echo "
           <td align=center>$row[name] </td>
           <td align=center>$day</td>
           <td align=center>$row[hit]</td>
         </tr>
         <tr bgcolor='#CCCCCC' height=1> 
           <td colspan='7'></td>
         </tr>
            ";
                    $number--;
                }
                ?>

                <tr>
                    <td colspan="7" height=20></td>
                </tr>

                <tr height=25>
                    <td colspan=7 align=center>
                        <?php
                        // 게시판 목록 하단에 페이지 링크 번호 출력
                        for ($i=1; $i<=$total_page; $i++)
                        {
                            if ($page == $i)     // 현재 페이지 번호 링크 안함
                            {
                                echo "
             <font color='4C5317'><b>[$i]</b></font>
           ";
                            }
                            else
                            {
                                echo "
             <a href='search.php?find=$find&search=$search&page=$i'>
             <font color='4C5317'>[$i]</font></a>
           ";
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#CCCCCC" height=1>
                    <td colspan="7"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width=766 align=center border=0 cellpadding=0 cellspacing=0
       bgcolor="#D2EAF0">
    <tr height=5>
        <td></td>
    </tr>
    <!-- 검색하기 시작 -->
    <form name=searchForm method=post action="search.php">
        <tr>
            <td>&nbsp;&nbsp;
                <select name="find" class="txt">
                    <option value="subject">제목에서</option>
                    <option value="content">본문에서</option>
                    <option value="name">글쓴이에서</option>
                </select>

                <input type="text" name="search" size=10>
                <input type="image" src="img/i_search.gif" align=absmiddle border=0>
            </td>
            <td align=right>
                <a href='write_form.php'><img src='img/i_write.gif' align=absmiddle
                                              border=0></a>
                &nbsp;<a href="list.php"><img src="img/i_list.gif" border=0></a>
                &nbsp;</td>
        </tr>
    </form>
    <tr height=5>
        <td></td>
    </tr>
    <tr bgcolor="#5AB2C8" height=1>
        <td colspan=2></td>
    </tr>
</table>
<!-- 검색하기 끝 -->

</body>
</html>
