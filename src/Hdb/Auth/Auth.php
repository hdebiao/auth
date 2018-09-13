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
     * 链接数据库
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

    public function addRule($ruleData, $uid)
    {

    }

    public function editRule($ruleData, $uid)
    {

    }

    public function deleteRule($ruleId, $uid)
    {

    }

    /**
     * 根据用户ID获取用户数据
     * @param $uid
     * @return array
     */
    public function getUser($uid)
    {
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