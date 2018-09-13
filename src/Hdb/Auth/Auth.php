<?php

namespace Hdb\Auth;

class Auth
{
    public $db;
    public $dbConfig;

    public function __construct($config)
    {
        self::connectDatabase($config);
    }

    /**
     * 连接数据库
     * @param $config
     * @return bool|\PDO
     */
    public function connectDatabase($config)
    {
        $servername = $config['host'];
        $username = $config['user'];
        $password = $config['password'];
        $dbname = $config['database'];
        if ($this->db === null) {
            try {
                $_opts_values = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
                $this->db = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $_opts_values);
            } catch (\PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return $this->db;
    }


    public function getDb()
    {
        return $this->db;
    }


    /**
     * 根据规则名称和用户id验证规则
     * @param $ruleName
     * @param $uid
     * @return bool
     */
    public function checkRule($ruleName, $uid)
    {
        $rule = self::getRule($ruleName);
        if (empty($rule)) {
            return false;
        }
        $user = self::getUser($uid);
        if (empty($user)) {
            return false;
        }
        $ruleId = $rule['id'];
        $userGroup = $user['groups'];
        $checkRuleArray = self::getRuleByGroupId(explode(',', $userGroup));
        if (empty($checkRuleArray)) {
            return false;
        }
        foreach ($checkRuleArray as $v) {
            $ruleArr = explode(',', $v['rules']);
            if (in_array($ruleId, $ruleArr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 增加规则
     * @param $ruleData
     * @param $uid
     * @return bool
     */
    public function addRule($ruleData, $uid)
    {
        $checkRule = self::checkRule('addRule', $uid);
        if (!$checkRule) {
            return false;
        }
        $name = $ruleData['name'];
        $title = $ruleData['title'];
        $createUserId = $uid;
        if (mb_strlen($name, 'utf-8') > 255) {
            return false;
        }
        if (mb_strlen($title, 'utf-8') > 100) {
            return false;
        }
        $sql = 'INSERT INTO auth.auth_rule ( name, title, creatUserId, status) VALUES (:name,:title,:createUserId,1)';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('name' => $name,'title'=> $title,'createUserId' => $createUserId));
        return true;
    }

    /**
     * 编辑规则
     * @param $editRuleData
     * @param $uid
     * @return bool
     */
    public function editRule($editRuleData, $uid)
    {
        $checkRule = self::checkRule('editRule', $uid);
        if (!$checkRule) {
            return false;
        }
        $ruleId = (int)$editRuleData['id'];
        $ruleData = self::getRuleById($ruleId);
        if (empty($ruleData)) {
            return false;
        }

        if ($ruleData['createUserId'] !== (int)$uid) {
            return false;
        }

        $name = $ruleData['name'];
        $title = $ruleData['title'];
        $status = $ruleData['status'];
        if (mb_strlen($name, 'utf-8') > 255) {
            return false;
        }
        if (mb_strlen($title, 'utf-8') > 100) {
            return false;
        }

        $sql = 'update auth_rule set name=:name,title=:title,status=:status where id=:id';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('id' => $ruleId,'name' =>$name,'title' => $title,'status' => $status));

        return true;
    }

    /**
     * 删除规则
     * @param $ruleId
     * @param $uid
     * @return bool
     */
    public function deleteRule($ruleId, $uid)
    {

        $ruleData = self::getRuleById($ruleId);
        if (empty($ruleData)) {
            return false;
        }

        if ($ruleData['createUserId'] !== (int)$uid) {
            return false;
        }

        $checkRule = self::checkRule('deleteRule', $uid);

        if (!$checkRule) {
            return false;
        }
        $sql = 'delete from auth_rule where id=:id';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('id' => (int)$ruleId));
        $sth->fetchAll(\PDO::FETCH_ASSOC);
        return true;
    }

    /**
     * 根据用户ID获取用户数据
     * @param $uid
     * @return array
     */
    public function getUser($uid)
    {
        $uid = (int)$uid;
        $sql = 'select * from auth_user where id=:uid';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('uid' => $uid));
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $data = $data[0];
        } else {
            $data = array();
        }
        return $data;
    }


    /**
     * 根据规则名称获取规则数据
     * @param $name
     * @return array
     */
    public function getRule($name)
    {
        $name = addslashes($name);
        $sql = 'select * from auth_rule where name=:name';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('name' => $name));
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $data = $data[0];
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * 根据id获取规则
     * @param $id
     * @return array
     */
    public function getRuleById($id)
    {
        $id = intval($id);
        $sql = 'select * from auth_rule where id=:id';
        $sth = $this->db->prepare($sql);
        $sth->execute(array('id' => $id));
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $data = $data[0];
        } else {
            $data = array();
        }
        return $data;
    }


    /**
     *根据组id获取多个规则
     * @param $idArr
     * @return mixed
     */
    public function getRuleByGroupId($idArr)
    {
        $idArr = array_values(array_unique($idArr));
        $ids = implode(',', $idArr);
        $sth = $this->db->prepare('select * from auth_group where id in (' . $ids . ')');
        $sth->execute();
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

}