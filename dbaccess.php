<?php
/* DB access module */
require_once('const.php');
require_once('common_methods.class.php');

class DbAccess
{
	private $conn;

	public function __construct()
	{
		$this->conn = new PDO(DSN,USER,PASSWORD);
	}

        public function authentication($loginid,$password)
        {
        	$sql = "select * from auth where delete_flg=false and user_id=?and password=?";
        	$stmt = $this->conn->prepare($sql);
        	$stmt->execute(array($loginid,$password));
        	$res = array();
        	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        		array_push($res,$row);
        	}
        	return $res;
        }

	public function getNews($params=array())
	{
		$sql = "select * from news where delete_flg=false order by id desc limit ?";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($params['limit']));

		$res = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($res,$row);
		}
		return $res;
	}

	public function getScript($params=array())
	{
		if ( strstr($params['order'],"random") !== false ) {
			$sql = "select * from ad_script where section=? and delete_flg=? order by random() limit ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($params['section'],$params['delete_flg'],$params['limit']));
		} else {
			$sql = "select * from ad_script where section=? and delete_flg=? order by ? limit ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($params['section'],$params['delete_flg'],$params['order'],$params['limit']));
		}
		$res = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($res,$row);
		}
		return $res;
	}
	
	public function getCategoryWithKeyValueArray()
	{
		$sql = "select * from category where delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$res = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tmp = array($row['code']=>$row['name']);
			$res = array_merge_recursive($res,$tmp);
		}
		return $res;
	}
	public function insertReview($requests)
	{
		// 初回登録か
		$sql = "select id,sns_user_id from member where sns_user_id=? and sns=? and delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($requests['sns_user_id'],$requests['sns']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$first_reg_flg = false;
		if (empty($row)) { // 初回
			$sql = "insert into member (nickname,mailaddress,sns,sns_user_id,sns_username) values (?,?,?,?,?)";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt->execute(array($requests['nickname'],$requests['mail'],$requests['sns'],$requests['sns_user_id'],$requests['sns_username']))) return false;
			$first_reg_flg = true;
		} else { // 過去に投稿履歴あり・更新
			$sql = "update member set nickname=?, mailaddress=?, sns=?, sns_username=?, modified=now() where id=? and delete_flg=false";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt->execute(array($requests['nickname'],$requests['mail'],$requests['sns'],$requests['sns_username'],$row['id']))) return false;
		}
		
		// review テーブル登録
		if (isset($requests['review_id']) && is_numeric($requests['review_id'])) {
			// 論理削除
			$sql = "update review set delete_flg=true,modified=now() where id=? and delete_flg=false";
			$stmt = $this->conn->prepare($sql);
			$ret = $stmt->execute(array($requests['review_id']));
			if ($ret == false) return false;
		}
	
		// member.id の取得
		if ($first_reg_flg) {
			$sql = "select id from member where sns_user_id=? and sns=? and delete_flg=false";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($requests['sns_user_id'],$requests['sns']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		$member_id = $row['id'];
		
		// カテゴリ名からコードを逆引き
		$category_arr = $this->getCategoryWithKeyValueArray();
		foreach($category_arr as $k => $v) {
			if ($requests['category'] == $v) {
				$category_code = $k;
				break;
			}
		}

		$sql = "insert into review (member_id,	product_id,nickname,	mail_address,category,	product_name,price,	maker,img_path,thumbnail_path,delete_password,review_comment,
			score_name_01,score_value_01,score_name_02,score_value_02,score_name_03,	score_value_03,score_name_04,score_value_04,
			score_name_05,score_value_05,score_name_06,score_value_06,score_name_07,	score_value_07,score_name_08,score_value_08,
			score_name_09,score_value_09,score_name_10,score_value_10) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		$stmt = $this->conn->prepare($sql);
		
		// ASIN
		$product_id = (isset($_POST['asin'])) ? $_POST['asin'] : '';
		
		if (isset($_POST['img_url']) && strlen($_POST['img_url'])) {
			$src = $_POST['img_url'];
			$dst = 'review_data/reviewimg_' .date('YmdHis');
			$thumb_dir = 'review_data';
			$x = $y = 80;
			$src_url_flg = true;
			$data = Common::createThumbnail($src,$dst,$thumb_dir,$x,$y,$src_url_flg);
			$img_path = $data['original'];
			$thumb_path = $data['thumbnail'];
			
		}

		// グラフ.スコア 初期化
		for($i=1;$i<=10;$i++) {
			$ip = 'score_item_' . sprintf("%02d",$i);
			$sp = 'score_value_' . sprintf("%02d",$i);
			$$ip = '';
			$$sp = 50; //default score
		}

		// グラフ.スコア セットされている値をバインド
		if (isset($_POST['score']) && $count = count($_POST['score'])) {
			for($i=0,$m=1;$i<$count;$i++,$m++) {
				$ip = 'score_item_' . sprintf("%02d",$m);
				$sp = 'score_value_' . sprintf("%02d",$m);
				$$ip = $_POST['score_item'][$i];
				$$sp = $_POST['score'][$i];
			}
		}

		$ret = $stmt->execute(array(
				$member_id,$product_id,	$_POST['nickname'],$_POST['mail'],$category_code,$_POST['product_name'],$_POST['price'],	$_POST['maker'],$img_path,$thumb_path,
				$_POST['delpasswd'],$_POST['editor1'],$score_item_01,$score_value_01,$score_item_02,$score_value_02,$score_item_03,$score_value_03,
				$score_item_04,$score_value_04,	$score_item_05,$score_value_05,	$score_item_06,$score_value_06,$score_item_07,$score_value_07,
				$score_item_08,$score_value_08,	$score_item_09,$score_value_09,	$score_item_10,$score_value_10)
		);
		if ( !$ret ) print_r($this->conn->errorInfo());
		return $ret;
	}
	
	public function getReviewRecordByReviewId($id,$option=array() )
	{		
		$sql = "select r.id as rid,r.nickname as rnickname,r.*,r.created as rcreated,m.* from
				 review r inner join member m on r.member_id = m.id where r.id=? and r.delete_flg=false and m.delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (empty($row)) {
			return false;
		} else {
			return $row;
		}
	}
	
	public function getReviewListLatest($limit=10)
	{		
		$sql = "select r.id as rid,r.nickname as rnickname,r.created as rcreated,r.*,m.* from 
				review r inner join member m on r.member_id = m.id where r.delete_flg=false and m.delete_flg=false order by r.created desc limit ?";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($limit));
		$res = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($res,$row);
		}
		return $res;
	}
	public function getReviewListRandom($limit=10)
	{		
		$sql = "select r.id as rid,r.nickname as rnickname,r.created as rcreated,r.*,m.*
				 from review r inner join member m on r.member_id = m.id where r.delete_flg=false and m.delete_flg=false order by random() desc limit ?";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($limit));
		$res = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($res,$row);
		}
		return $res;
	}
	
	public function getCategoryName($code)
	{
		$sql = "select name from category where code=? and delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($code));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['name'];
	}
	
	public function getReviewListAllWithPager($path,$current_page=1,$data_num_per_page=1,$pager_index_num=7)
	{
		$param = Common::getLimitAndOffsetValueFromPageNumber($current_page,$data_num_per_page);

		$sql = "select count(r.id) as count from review r inner join member m on r.member_id = m.id where r.delete_flg=false and m.delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$fetch_count = $row['count'];
		$res = array();

		if ($fetch_count > 0) {
			$sql = "select r.id as rid,r.nickname as rnickname,r.created as rcreated,r.*,m.*
					 from review r inner join member m on r.member_id = m.id where  r.delete_flg=false and m.delete_flg=false order by r.created desc limit ? offset ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($param['limit'],$param['offset']));
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($res,$row);
			}
		}
		$data['res'] = $res;
		$last_page = ceil($fetch_count / $data_num_per_page);
		$data['pager'] = Common::paginator($current_page,$last_page,$pager_index_num,$path,"p");
		
		return $data;
	}

	public function getReviewListByCategoryWithPager($path,$category,$current_page=1,$data_num_per_page=1,$pager_index_num=7)
	{
		$param = Common::getLimitAndOffsetValueFromPageNumber($current_page,$data_num_per_page);

		$sql = "select count(r.id) as count from review r inner join member m on r.member_id = m.id where r.category=? and r.delete_flg=false and m.delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(array($category));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$fetch_count = $row['count'];
		$res = array();

		if ($fetch_count > 0) {
			$sql = "select r.id as rid,r.nickname as rnickname,r.created as rcreated,r.*,m.*
					 from review r inner join member m on r.member_id = m.id where  r.category=? and r.delete_flg=false and m.delete_flg=false order by r.created desc limit ? offset ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($category,$param['limit'],$param['offset']));
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($res,$row);
			}
		}
		$data['res'] = $res;
		$last_page = ceil($fetch_count / $data_num_per_page);
		$data['pager'] = Common::paginator($current_page,$last_page,$pager_index_num,$path,"p",array('category'=>$category));
		
		return $data;
	}
	
	public function getReviewListByMultipleKeyword($path,$keywords,$cols_arr=array('r.title','r.maker'),$current_page=1,$data_num_per_page=1,$pager_index_num=7)
	{
		//件数
		$param = Common::getLimitAndOffsetValueFromPageNumber($current_page,$data_num_per_page);

		$str = mb_convert_encoding($keywords, 'UTF-8', 'auto');
		$str = mb_ereg_replace("　", " ", $str);
		$uni = explode(" ",$str);
		//$uni = str_replace("'","''",$uni); // 無毒化
		
		$cond = array();
		$count = count($cols_arr);
		$tmp = '';
		
		for ($i=0;$i<$count;$i++) {
			foreach ($uni as $k=>$v) {
				$q[$k] = " " .$cols_arr[$i]. " like '%" .$v. "%' ";
			}
			$cond[$i] = join('AND',$q);
			$tmp .= "(" .$cond[$i]. ") ";
			if ($i != ($count-1)) $tmp .= " or ";
		}
		$where = " (" .$tmp. ") ";

		$sql = "select count(r.id) as count from review r inner join member m on r.member_id = m.id where  " .$where. " and r.delete_flg=false and m.delete_flg=false";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$fetch_count = $row['count'];
		$res = array();

// for debug echo $fetch_count;exit();
		if ($fetch_count > 0) {	
			$sql = "select r.id as rid,r.nickname as rnickname,r.created as rcreated,r.*,m.*
						 from review r inner join member m on r.member_id = m.id where  " .$where. " and r.delete_flg=false and m.delete_flg=false order by r.created desc limit ? offset ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($param['limit'],$param['offset']));

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push($res,$row);
			}
		}
		
		$data['res'] = $res;
		$last_page = ceil($fetch_count / $data_num_per_page);
		$data['pager'] = Common::paginator($current_page,$last_page,$pager_index_num,$path,"p",array('keywords'=>$keywords));		

		return $data;
	}

}
