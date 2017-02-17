<?php
/**
 * Created by PhpStorm.
 * User: Saturn
 * Date: 16-12-15
 * Time: 下午9:17
 */
class U_recharge extends User_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("m_recharge");
        $this->load->model("m_user");
    }

    /**
     * 充值
     */
    function recharge()
    {
        if(!$this->checkParam(array("money")))
        {
            $this->responseError(ERROR_PARAM);
            return;
        }

        $rechargeData = array(
            "userId" => $this->m_user->getSelfUserId,
            "price" => floatval($this->input->post("price")),
            "coins" => (floatval($this->input->post("price")) * COIN_MONEY_RATE),
            "payChanel" => 0,//默认微信 后续可扩展
            "rechargeTime" => now(),
        );

        $rechargeInfo = null;
        $retCode = $this->m_rechare->recharge($rechargeData, $rechargeInfo);
        if($retCode != ERROR_OK)
        {
            $this->responseError($retCode);
            return;
        }

        $this->responseSuccess(array("rechargeInfo" => $rechargeInfo));
        return;
    }
}