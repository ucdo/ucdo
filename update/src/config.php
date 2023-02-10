<?php
namespace Ucdo\Update\Config;
class config{
    /**
     * GIT  or DB
     * @param string $key
     */
    public function __construct(string $key)
    {
        return $this->config()['$key']??[];
    }

    public function config():array
    {
        return [
            'GIT' =>[
                '',
                'cmd' => [
                    'git fetch --all && git reset --hard origin/master'
                ]
            ],
            'DB' => [
                'HOSTNAME' => '127.0.0.1',
                'DATABASE' => 'local',
                'USERNAME' => 'root',
                'PASSWORD' => 'root'
            ]
        ];
    }
}
