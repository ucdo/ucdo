<?php

namespace Ucdo\Update\Controller;

use Ucdo\Update\Config\config;

/**
 * pack sql data
 */
require_once "../config.php";
class PackSql
{
    /**
     * @return array
     */
    public function packSql():array
    {
        $config = [
            'HOSTNAME' => '127.0.0.1',
            'DATABASE' => 'local',
            'USERNAME' => 'root',
            'PASSWORD' => 'root'
        ];
        $sql = sprintf('SELECT `TABLE_NAME`,`ENGINE`,`TABLE_COMMENT`,`TABLE_COLLATION` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = \'%s\'',$config['DATABASE']);
        $conn= (new \mysqli($config['HOSTNAME'],$config['USERNAME'],$config['PASSWORD'],$config['DATABASE']));
        if($conn){
            $data = $conn->query($sql);
            while($res = $data->fetch_assoc()){

                $tables[$res['TABLE_NAME']] = $res;
            }
        }
        $this->getAllColumns($conn,$tables);
        $this->getIndex($conn,$tables);

        return $tables;
    }

    public function getAllColumns($conn,array &$tables): void
    {
        $string = $this->keys($tables);
        $sql = sprintf('SELECT `TABLE_NAME`,`COLUMN_TYPE`,`IS_NULLABLE`,`COLUMN_DEFAULT`,`COLUMN_COMMENT` FROM `information_schema`.`COLUMNS` WHERE `TABLE_NAME` IN %s',$string);
        $data = $conn->query($sql);
        while($res = $data->fetch_assoc()){
            $tables[$res['TABLE_NAME']]['COLUMNS'][] = $res;
        }
    }

    public function getIndex($conn, array &$tables): void
    {
        $keys =array_keys($tables);
        foreach ($keys as $k){
            $sql = sprintf('SHOW INDEX FROM %s',$k);
            $data = $conn->query($sql);
            while($res = $data->fetch_assoc()){
                $index[] = [
                    'Non_unique' => $res['Non_unique'],
                    'Null' => $res['Null'],
                    'Index_type' => $res['Index_type'],
                    'Comment' => $res['Comment'],
                    'Index_comment' => $res['Index_comment'],
                    'Column_name' => $res['Column_name']
                ];
            }
            $tables[$k]['INDEX'] = $index??[];
        }
    }

    /**
     * @param array $tables
     * @return string
     */
    public function keys(array $tables):string
    {
        $keys = array_keys($tables);
        $string = '(';
        foreach ($keys as $v){
            $string .= '\''.$v .'\',';
        }
        $string = trim($string,',');
        $string .= ')';
        return $string;
    }

    public function generateSqlJson():string
    {
        $path = '/mnt/d/phpEnv/www/farm_backend';
        $content = $this->packSql();
        file_put_contents($path.'/sql.json',json_encode($content,JSON_THROW_ON_ERROR));
        return $path.'/sql.json';
    }
}

try {
    echo (new PackSql())->generateSqlJson();
} catch (\JsonException $e) {
    throw  new \Exception('json encode failed');
}
