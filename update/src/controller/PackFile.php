<?php
namespace Ucdo\Update\Controller;
/**
 * pack file as a zip
 */
class PackFile{

    const ignore = [
        '.env','.gitignore','.idea','.vscode'
    ];

    /**
     * @return bool
     * @throws \Exception
     */
    public function pullFile():bool
    {
        $projectPath = '/mnt/d/phpenv/www';
        if(! file_exists($projectPath . '/develop')){
            $gitCmd = "cd  {$projectPath} && git clone -b develop git@gitee.com:FATZNG/xt6p_code.git develop";
            $this->execCmd($gitCmd);
        }

        $gitCmd = 'git fetch --all && git reset --hard origin/develop';
        $this->execCmd($gitCmd);

        return true;
    }

    public function tidyFiles(string $path = '/mnt/d/phpEnv/www/farm_backend'):array
    {
        $files = @scandir($path);
        $list = [];
        if(empty($files)){
            return $list;
        }
        foreach($files as $v){
            if(in_array($v,['.','..'])){
                continue;
            }

            if(in_array($v,self::ignore)){
                continue;
            }

            if(is_dir($path . '/' . $v)){
                $list = array_merge($list,$this->tidyFiles($path.'/'.$v));
            }else{
                $list[] = [
                   'file_name' => $v,
                   'file_md5' => md5_file($path.'/'.$v)
                ];
            }
        }
        return $list;
    }

    /**
     * @return string
     * @throws \JsonException
     */
    public function generateFileJson():string
    {
        $path = '/mnt/d/phpEnv/www/farm_backend';
        $content = $this->tidyFiles($path);
        file_put_contents($path.'/file.json',json_encode($content,JSON_THROW_ON_ERROR));
        return $path.'/file.json';
    }

    /**
     * @throws \Exception
     */
    public function execCmd(string $cmd): bool
    {
        if(! exec($cmd)){
            throw new \Exception("Execute Command Line `{$cmd}` failed");
        }

        return true;
    }

}

$res =  (new PackFile)->tidyFiles();
echo count($res??[]);
echo json_encode($res);