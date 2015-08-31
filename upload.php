<?php
	define('REVIEW_IMG_PREFIX','reviewer_img_');
	$img_dir = "/var/www/si_html/review_data/";
	$img_url = "review_data/"; // 相対パス
	$ext_white_list = array("jpg","jpeg","gif","png");
	#アップロードされたファイルが存在するか
	if ( !is_uploaded_file( $_FILES['upload_file']['tmp_name'] ) ) {
		echo '<div>ファイルが選択されていません。</div>';
		exit();
	}
	// 拡張子チェック
	$pi = pathinfo($_FILES["upload_file"]["name"]);
	$ext = strtolower($pi['extension']);
	if ( !in_array($ext,$ext_white_list) ) {
		echo 'サポート対象外のファイルタイプです';
		exit();
	}
	
	$dst = $img_dir .REVIEW_IMG_PREFIX .date("YmdHis"). "." .$ext;
	$ret = move_uploaded_file($_FILES["upload_file"]["tmp_name"], $dst);
	if ( $ret ) {
		echo '<img height="140" src="' .$img_url .basename($dst). '"><input type="hidden" name="image" id="image" value="' .basename($dst). '"/>';
	} else {
		echo 'fail to uploaded_file';
	}
?>
