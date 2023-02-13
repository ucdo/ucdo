<?php
namespace Ucdo\Update\Controller;
use ZipArchive;

/**
 * pack file as a zip
 */
class PackFile{

    const ignore = [
        '.env','.gitignore','.idea','.vscode'
    ];

    const file = '/mnt/d/phpenv/www/swoole-webhook';

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

    public function tidyFiles(string $path = '/'):array
    {
        $src = self::file . $path;
        $files = @scandir($src);
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

            if(is_dir($src . $v)){
                $list = array_merge($list,$this->tidyFiles($path . $v . '/'));
            }else{
                $list[] = [
                   'file_name' => $path . $v,
                   'file_md5' => md5_file($src . $v)
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

    public function generateFileZip(string $path = ''):string
    {
        $path = '/mnt/d/phpenv/www';
        $develop = $path.'/develop.zip';

        if(file_exists($develop)){
            unlink($path.'/develop.zip');
        }

        if(! file_exists($develop)){
            echo touch($develop);
        }

        $zip = (new \ZipArchive());
        $res = $zip->open($path.'/develop.zip',\ZipArchive::CREATE);
        if(! $res){
            throw new \Exception('版本压缩文件不能创建');
        }
        $file = $this->tidyFiles();

        foreach ($file as $v){
            $zip->addFile(self::file . $v['file_name'],ltrim($v['file_name'],$path));
        }

        $zip->close();
        return $path.'/develop.zip';
    }

}

try {
    echo (new PackFile())->generateFileZip();
}catch (\Throwable $e){
    throw new \Exception($e);
}