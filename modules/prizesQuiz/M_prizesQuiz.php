<?php

/**
* create by mxl
* time 2017-2-21
* prizesquiz table status:
* 0:auction
* 1:prizesquiz
* 2:admin quit it, cnum<3
* 3:admin quit it, auction fail,
* 4: normal over
*/
class M_prizesQuiz extends My_Model{
	static $itemInfo = array();
	
	function __construct(){
		# code...
		parent::__construct();
		$this->load->driver('cache');
		if(!$this->cache->redis->is_supported()){
            $this->log->write_log('error', "redis not supported!!!");
        }

        $this->cache->redis->retain();
	}

	// user takes part in prizes quiz
	function partakeQuiz($itemId,$quizPrice,$userId){
		//$itemInfo = $this->getItemInfo($itemId);
		$itemInfo = $this->m_common->get_one('prizesquiz',array('goods_id'=>$itemId,'status'=>0));
		if (!empty($itemInfo)) {
			//the goods is auction, can't be quiz
			return "此商品正在拍卖，不能参与竞猜";
			exit;
		}
		$sql = "select count from mn_prizesuser where user_id = ? and goods_id = ?";
		$query = $this->db->query($sql,array($userId,$itemId));
		$count = $query->row_array();
		if (!empty($count) && $count['count'] == 1) {
			//judge count, if count = 1, forbidden user to partake quiz
			return "不能重复参与";
			exit;
		}
		//judge user balance
		$this->load->model('m_user');
		$userObj = $this->m_user->getSelfUserObj();
		//$userObj = $this->db->where('userId',$userId)->from('user')->get()->row();
		if ($userObj->balance < 5) {
			return "账户余额不足";
			exit;
		}
		//jugde user nums
		$prizesQuizObj = $this->db->where('goods_id',$itemId)->from('prizesquiz')->get()->row();
		if ($prizesQuizObj->currentNum >= $prizesQuizObj->limitNum) {
			//user over limit
			return "人数已满";
			exit;
		}

		$data = array('goods_id'=>$itemId,'user_id'=>$userId,'quiz_price'=>$quizPrice,'count'=>1,'part_time'=>date("y-m-d h:i:s"));
		$res = $this->db->insert('quizuser',$data);
		if ($res) {
			//success
			//user cost 5 yuan
			$balance = $userObj->balance - 5;
			$this->db->where('userId',$userId)->update('user',array('balance'=>$balance));
			//prize goods sum add 5
			$sum = $prizQuizObj->sum + 5;
			$currentNum = $prizesQuizObj->currentNum + 1;
			$this->db->where('goods_id',$itemId)->update('prizesquiz',array('sum'=>$sum,'currentNum'=>$currentNum));
			
		}else{
			//fail
			return "参与失败";
			exit;
		}
		
		//
	}

	// administrator quit the quiz
	function quitQuiz($itemId){
		//quiz user nums < 3
		$prizesQuizObj = $this->db->where('goods_id',$itemId)->from('prizesquiz')->get()->row();
		$currentNum = $prizesQuizObj->currentNum;
		if ($currentNum >= 3) {
			return '参与人数大于3，不能停止';
			exit;
		}
		if ($currentNum != 0) {
			$partUser = $this->where('goods_id',$itemId)->from('quizuser')->select('user_id')->get()->result_array();
			for ($i=0; $i <$currentNum ; $i++) { 
				$cuserid = $partUser[$i];
				$balance = $this->db->select('balance')->from('user')->where('userId',$cuserid)->get();
				$balance += 5;
				$this->db->where('userId',$cuserid)->update('user',array('balance'=>$balance));
			}
		}
		//set this quiz status 2
		$this->db->where('goods_id',$itemId)->update('prizesquiz',array('status'=>2));
	}

	//get auction object
	function getItemInfo($itemId){
		$item = null;
		if (isset(self::$itemInfo[$itemId])) {
			$item = self::$itemInfo[$itemId];
			return $item;
		}
		$key = CACHE_PREFIX_AUCTION.$itemId;
		$item = unserialize($this->cache->redis->get($key));
		if ($item) {
			self::$itemInfo[$itemId] = $item;
			return $item;
		}

		$res = $this->m_common->get_one('prizesquiz',array('id'=>$itemId));
		if (!empty($res)) {
			$item = new CAuction();
			$item->itemId = $itemId;
			$item->initWithDBData($res);
			$item->saveCache();
			self::$itemInfo[$itemId] = $item;
			return $item;
		}
		return $item;

	}

	//quiz normal over
	function quizOver($itemId){
		//get quiz goods purchase
		$purchasePrice_Sum = $this->db->select('purchasePrice,sum')->from('prizesquiz')->where('goods_id',$itemId)->get()->result_array();
		$userId_quizPrice = $this->db->select('user_id,quiz_price')->from('quizuser')->where('goods_id',$itemId)->get()->result_array();
		$awardUser = $this->getFTUserId($purchasePrice_Sum['purchasePrice'],$userId_quizPrice);

		//award every award-user money
		for ($i=0; $i <count($awardUser) ; $i++) { 
			switch ($i) {
				case 0:
					//first prize users obtain award
					$awardMoney = $purchasePrice_Sum['sum'] / count($awardUser[$i]) * 6 / 10;
					$award = 1;
					break;
				case 1:
					//second prize users obtain award
					$awardMoney = $purchasePrice_Sum['sum'] / count($awardUser[$i]) * 3 / 10;
					$award = 2;
					break;
				case 2:
					//third prize users obtain award
					$awardMoney = $purchasePrice_Sum['sum'] / count($awardUser[$i]) * 1 / 10;
					$award = 3;
					break;
				default:
					$awardMoney = 0;
					break;
			}
			
			for ($j=0; $j <count($awardUser[$i]) ; $j++) { 
				$cuserid = $awardUser[$i][$j];
				$balance = $this->db->select('balance')->from('user')->where('userId',$cuserid)->get();
				$balance += $awardMoney;
				//update award-user balance and award level
				$this->db->where('userId',$cuserid)->update('user',array('balance'=>$balance));
				$this->db->where('user_id',$cuserid)->update('quizuser',array('award'=>$award));
			}
		}
		$this->db->where('goods_id',$itemId)->update('prizesquiz',array('status'=>4));
		
	}

	//get first three userid
	function getFTUserId($purchasePrice,$UID_Price_array){
		$UID_Price_diff = $UID_Price_array;
		$diff_arr = array();
		for ($i=0; $i < count($UID_Price_array); $i++) { 
			$sub = $purchasePrice - $UID_Price_array[$i]['quiz_price'];
			$diff = abs($sub);
			$UID_Price_diff[$i]['quiz_price'] = $diff;
			array_push($diff_arr,$diff);
		}

		sort($diff_arr);
		$diff_arr_sort = array_unique($diff_arr);
		$f_userid = $s_userid = $t_userid = $awardUser = array();
		for ($i=0; $i < count($UID_Price_diff); $i++) { 
			if ($UID_Price_diff[$i]['quiz_price'] == $diff_arr_sort[0]) {
				$f_userid[] = $UID_Price_diff[$i]['user_id'];
			}
			if ($UID_Price_diff[$i]['quiz_price'] == $diff_arr_sort[1]) {
				$s_userid[] = $UID_Price_diff[$i]['user_id'];
			}
			if ($UID_Price_diff[$i]['quiz_price'] == $diff_arr_sort[2]) {
				$t_userid[] = $UID_Price_diff[$i]['user_id'];
			}
		}

		array_push($awardUser,$f_userid,$s_userid,$t_userid);
		return $awardUser;
	}

	//get prizes quiz lists
	function getQuizList(){
		$data = $this->db->get('prizesquiz')->result_array();
		return $data;
	}

	//get quiz user list
	//contain user icon,telphone,part_time,quiz_price,award
	function getQuizUserList($itemId){
		$data = $this->db->from('quizuser')->where('goods_id',$itemId)->join('user',"quizuser.user_id = user.userId")->select('icon,telephone,part_time,quiz_price,award')->get()->result_array();
		return $data;
	}

	function test(){
		return array(1,array(1,5));
		$price = 25;
		$testdata = array(array('user_id'=>1,'quiz_price'=>20),array('user_id'=>2,'quiz_price'=>15),array('user_id'=>3,'quiz_price'=>50),array('user_id'=>4,'quiz_price'=>15),array('user_id'=>5,'quiz_price'=>18));
		$res = $this->getFTUserId($price,$testdata);
		var_dump($res);die;
		$somedata = $this->db->select('currentPrice,bidsNum')->from('auctionitems')->where('isRemind',2)->get()->result_array();
		var_dump($somedata);
	}

}