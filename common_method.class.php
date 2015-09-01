<?php
require_once('wideimage-sdk/WideImage.php');
class Common
{
	//-------------------------------------------------------------------
	// this function is encode with RFC3986 format.
	//-------------------------------------------------------------------
	public static function urlencode_RFC3986($str)
	{
		return str_replace('%7E', '‾', rawurlencode($str));
	}

	//-------------------------------------------------------------------
	// params = array(option_arr(array),name,selected ...
	//-------------------------------------------------------------------	
	public static function getSelectTag($params=array())
	{
		$options = '';
		if ( !isset($params['name']) ) {
			$name = 'select';
		} else {
			$name = $params['name'];
		}
		
		if (isset($params['header'])) $options = '<option val="">' .$params['header']. '</option>';

		foreach ($params['option_arr'] as $opt) {
			$sel = '';
			
			if (isset($params['selected']) && $opt == $params['selected']) $sel = ' selected="selected"';
			$options .= '<option' .$sel. '>' .$opt. '</option>'; 
		}
		
		return '<select name="' .$name. '" id="' .$name. '">' .$options. '</select>';
	}

	//-------------------------------------------------------------------
	// generate random phrase 
	//-------------------------------------------------------------------	
	public static function getRandomPhrase()
	{
		return md5(mt_rand());
	}
	
	public static function createThumbnail($src,$dst,$thumb_dir,$x,$y,$src_url_flg=true)
	{
		if ($src_url_flg) {
			$data = file_get_contents($src);
			$pi = pathinfo($src);
			$dst .= "." .$pi['extension'];

			file_put_contents($dst,$data);
		
		} else $dst = $src;

		$image = WideImage::load($dst);
		// resize
		$resized = $image->resize($x,$y,'outside');
		// crop
		$cropped = $resized->crop('center','center',$x,$y);
		$fbasename = basename($dst);
		$thumbnail = $thumb_dir .'/thumb_'. $fbasename;
		$cropped->saveToFile($thumbnail);

		$data = array();
		$data['original'] = $dst;
		$data['thumbnail'] = $thumbnail;

		return $data;
	}
     
	public static function paginator (  $current_page,     			// 現在ページ
						$last_page,          				// 最終ページ
						$list_num=7,     				// ページャーに表示するページ番号群の数。左右対称にするため奇数を指定
						$path,               				// ＵＲＬ
						$page_query_name="p",	// クエリー:ページ(GETパラメタ名)
						$query_key_value_arr=null )		// クエリー:任意配列
	{
		if (empty($last_page)) return false;
		$pager = '';
		if ($last_page < $list_num) {
			$start = 1;
			$end = $last_page;
		} else {
			if ($current_page <= (($list_num - 1) / 2)) {
				$start = 1;
				$end = $list_num;
			} else {
				if (($last_page - $current_page) <= (($list_num - 1) / 2)) {
					$start = $last_page - $list_num + 1;
					$end = $last_page;
				} else {
					$start = $current_page - ($list_num - 1) / 2;
					$end = $current_page + ($list_num - 1) / 2;
				}
			}
		}
		
		// 任意クエリー
		$add_query = '';
		if (count($query_key_value_arr)) {
			$add_query = '&';
			foreach ($query_key_value_arr as $k=>$v) {
				$add_query .= $k .'='. urlencode($v) .'&'; 
			}
		}

		for($i=$start;$i<=$end;$i++) {
     		$v = '';
			if ( $current_page == $i ) {
				$v = '<em>' .$i. '</em>';
			} else {
				$v = '<a href="' .$path. '?' .$page_query_name. '=' .$i. $add_query. '">' .$i. '</a>';
			}
			$pager .= '<li>' .$v. '</li>';
		}

		if ($current_page < $last_page) {
			$pager .= '<li><a href="' .$path. '?' .$page_query_name. '=' .($current_page+1). $add_query. '">' .'NEXT'. '</a></li>';
		}
		if ($current_page != '1') {
			$tmp = $pager;
			$pager = '';
			$pager .= '<li><a href="' .$path. '?' .$page_query_name. '=' .($current_page-1). $add_query. '">' .'PREV'. '</a></li>' .$tmp;
		}
		return $pager;
	}

	public static function getLimitAndOffsetValueFromPageNumber($page,$countPerPage)
	{
		$limit = $countPerPage;
		$offset = ($page - 1) * $countPerPage;
		$data['limit'] = $limit;
		$data['offset'] = $offset;

		return $data;
	}

	public static function shortenStringWithByte($limit_byte,$src,$add_str=null)
	{
		if (strlen($src) <= $limit_byte) return $src;
		// 文字数
		$chars = mb_strlen(mb_convert_encoding($src,"SJIS","UTF-8"));
		for($i=$chars;$chars>0;$chars--) {
			$str = mb_substr($src,0,$chars);
			$byte = strlen($str);
			if ($byte <= $limit_byte) break;
		}
		return $str .$add_str;
	}

}
?>
