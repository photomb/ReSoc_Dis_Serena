<?php
    $searchTag = $_POST['searchtags'];
    $url = "./tags.php?tag_id=".$searchTag;
    header("location:" . $url);
    exit();
?>