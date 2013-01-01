<?php

class Database
{

    function __construct($config)
    {
        $this->connect($config['host'], $config['user'], $config['pass']);
        $this->select_db($config['db']);
    }

    function connect($host, $user, $pass)
    {
        return mysql_connect($host, $user, $pass);
    }

    function select_db($db)
    {
        mysql_select_db($db);
    }

    function write()
    {
        $arg = func_get_args();
        $sql = $arg[0];
        $arg = array_slice($arg, 1);

        foreach($arg as $k=>$a)
        {
            $arg[$k] = mysql_real_escape_string($a);
        }

        $sql = vsprintf($sql, $arg);

        mysql_query($sql) or die(mysql_error());
    }

    private function read()
    {
        $arg = func_get_args();
        $sql = $arg[0];
        $arg = array_slice($arg, 1);

        foreach($arg as $k=>$a)
        {
            $arg[$k] = mysql_real_escape_string($a);
        }

        if(count($arg) > 0)
            $sql = vsprintf($sql, $arg);

        $ret = mysql_query($sql) or die(mysql_error());

        return $ret;
    }

    function read_array()
    {
        $arr = call_user_func_array(array($this, "read"), func_get_args());

        if(!$arr)
            return false;

        $retarr = array();

        while($item = mysql_fetch_array($arr))
        {
            $retarr[] = $item;
        }

        return $retarr;
    }

    function read_single()
    {
        $sing = call_user_func_array(array($this, "read"), func_get_args());

        if($sing)
            return mysql_fetch_array($sing);
    }


}
