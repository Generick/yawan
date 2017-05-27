<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-6-1
 * Time: 上午9:53
 */

class CUserBaseInfo extends IExtractInfo {
    public static $fields = null;
    private static $staticConstructed = false;
    public static function staticConstruct()
    {
        if (self::$staticConstructed)
        {
            return;
        }

        self::$fields = array(
            "name",
            "gender",
            "smallIcon",
            "telephone",
            "balance",
        );
    }

    public static function registerFields($fields)
    {
        self::$fields = array_merge(self::$fields, $fields);
    }
}

class CUserInfo extends IExtractInfo {
    public static $fields = null;
    private static $staticConstructed = false;
    public static function staticConstruct()
    {
        if (self::$staticConstructed)
        {
            return;
        }

        self::$fields = array(
            "userId",
            "name",
            "gender",
            "smallIcon",
            "icon",
            "telephone",
            "registerTime",
            "balance",
            "sms_obtain_status",
            "sms_beyond_status",
            "sms_over_status",
            "deposit_cash",
        );
    }

    public static function registerFields($fields)
    {
        self::$fields = array_merge(self::$fields, $fields);
    }
}

class CUser extends IUserBase {
    // 属性的保存方式
    // Array中保存的是array(objField => CField, ...)
    private static $fields = null;
    // 修改属性的配置：type(修改属性) => 关联的属性field
    private static $modInfo = null;
    private static $staticConstructed = false;

    private static $registeredExtFunctions = array();

    public static function staticConstruct()
    {
        if (self::$staticConstructed)
        {
            return;
        }

        // 字段（和数据库对应），同时和类型对应
        self::$fields = array(
            "userId" =>       new CField(FIELD_TYPE_NORMAL),
            "name" =>       new CField(FIELD_TYPE_NORMAL),
            "gender" =>       new CField(FIELD_TYPE_NORMAL),
            "smallIcon" =>       new CField(FIELD_TYPE_NORMAL),
            "icon" =>       new CField(FIELD_TYPE_NORMAL),
            "telephone" =>  new CField(FIELD_TYPE_NORMAL),
            "registerTime" =>  new CField(FIELD_TYPE_NORMAL),
            "sms_obtain_status" =>  new CField(FIELD_TYPE_NORMAL),
            "sms_beyond_status" =>  new CField(FIELD_TYPE_NORMAL),
            "sms_over_status" =>  new CField(FIELD_TYPE_NORMAL),
            "deposit_cash" =>  new CField(FIELD_TYPE_NORMAL),
        );

        // 可被修改的字段，对应到用户身上的哪个字段（这里的数值主要用于modInfo，还有一些数据的修改和相应的逻辑有关）
        // 注意：这里的modInfo对应的字段值可以是Normal或者CNormalKV类
        self::$modInfo = array(
        );

        self::$staticConstructed = true;
    }

    // 注册新的字段，用于扩展
    public static function registerFields($fields)
    {
        self::$fields = array_merge(self::$fields, $fields);
    }

    public static function registerExtFunction($functionName, $function)
    {
        self::$registeredExtFunctions[$functionName] = $function;
    }

    //////////////////////////////////////////
    function __construct()
    {
        parent::__construct();
    }

    function __call($method, $args)
    {
        if (isset(self::$registeredExtFunctions[$method]))
        {
            $function = self::$registeredExtFunctions[$method];
            $p = array($this);
            $p = array_merge($p, $args);
            return call_user_func_array($function, $p);
        }
    }
    /**
     * 使用数据库的数据进行对象的初始化
     * @param $dbResult
     */
    public function initWithDBData($dbResult)
    {
        parent::defaultInitWithDBData($dbResult, self::$fields, array('userId'));
    }

    /**
     * 保存到DB中，这个函数会在每次会话结束的时候统一调用
     * @return bool 表示保存成功
     */
    public function saveDB()
    {
        // 获取超级对象
        $CI = &get_instance();
        $updateFields = $this->generateUpdateFields(self::$fields);

        if (count($updateFields) == 0)
        {
            return true;
        }

        $affectedRows = $CI->m_common->update('user', $updateFields, array('userId' => $this->userId));
        if ($affectedRows < 1)
        {
            $CI->log->write_log('error', "Update user failed: {$this->userId}");
            return false;
        }

        $this->saveCache();
        return true;
    }

    //region 用户资料
    ////////////////////////////////////////////////////////////////////////////////
    /**
     * 修改用户属性：只包括Normal字段或者类型为CNormalKV的字段
     * @param $info     数组：{key => value}
     * @return bool
     */
    public function modInfo($info)
    {
        return parent::defaultModInfo($info, self::$modInfo, self::$fields);
    }

    public function modInfoWithPrivilege($info)
    {
        parent::defaultModInfoWithPrivilege($info, self::$fields);
    }

    //endregion

    /**
     * 组一份简易信息发给客户端，只包含最关键的内容
     */
    public function getUserBaseData()
    {
        $data = new CUserBaseInfo();
        $data->copyFromObj(CUserBaseInfo::$fields, $this);
        return $data;
    }

    public function getUserSelfData()
    {
        $data = new CUserInfo();
        $data->copyFromObj(CUserInfo::$fields, $this);
        return $data;
    }
}

// 在最后执行静态构造函数
CUser::staticConstruct();
CUserInfo::staticConstruct();
CUserBaseInfo::staticConstruct();