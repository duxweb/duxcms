<?php

namespace App\System\Service;

class System
{
    protected bool $isWin = false;
    protected bool $isMac = false;

    protected mixed $linCpuUsage = null;
    protected mixed $linMemUsage = null;

    public function __construct()
    {
        $this->isWin = $this->isWin();
        $this->isMac = $this->isMac();
    }

    /**
     * linux 统计
     * @return void
     */
    private function getLinuxStatus(): void
    {
        $fp = popen('top -b -n 2 | grep -E "(Cpu\(s\))|(KiB Mem)"', "r");

        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);

        $sys_info = explode("\n", $rs);
        $cpu_info = explode(',', $sys_info[2]);
        $this->linCpuUsage = trim(trim($cpu_info[0], '%Cpu(s): '), 'us');

        $mem_info = explode(",", $sys_info[3]);
        $mem_total = trim(trim($mem_info[0], 'KiB Mem: '), ' total');
        $mem_used = trim(trim($mem_info[2], 'used'));
        $this->linMemUsage = $mem_total ? round(100 * intval($mem_used) / intval($mem_total), 2) : 0;
    }

    /**
     * 获取 mac 统计
     * @return void
     */
    private function getMacStatus(): void
    {
        $fp = popen("top -l 1 | awk '/^CPU usage/ || /^PhysMem/' | awk '{print $3,$7}'", "r");
        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        pclose($fp);

        $sys_info = explode("\n", $rs);
        list($cpuUsage, $memoryUsage) = explode(" ", trim($sys_info[0]));

        $this->linCpuUsage = trim($cpuUsage, '%');
        $this->linMemUsage = trim($memoryUsage, '%');
    }

    /**
     * 获取文件
     * @param  $file_name
     * @param  $content
     * @return  string
     */
    private function getFilePath($file_name, $content): string
    {
        $path = data_path("/{$file_name}");
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
        return $path;
    }

    /**
     * cpu 利用率文件
     * @return string
     */
    private function getCpuUsageVbsPath(): string
    {
        return $this->getFilePath(
            'cpu_usage.vbs',
            "On Error Resume Next
    Set objProc = GetObject(\"winmgmts:\\\\.\\root\cimv2:win32_processor='cpu0'\")
    WScript.Echo(objProc.LoadPercentage)"
        );
    }

    /**
     * 可用内存
     * @return string
     */
    private function getMemUsagePath(): string
    {
        return $this->getFilePath(
            'memory_usage.vbs',
            "On Error Resume Next
    Set objWMI = GetObject(\"winmgmts:\\\\.\\root\cimv2\")
    Set colOS = objWMI.InstancesOf(\"Win32_OperatingSystem\")
    For Each objOS in colOS
     Wscript.Echo(\"{\"\"TotalVisibleMemorySize\"\":\" & objOS.TotalVisibleMemorySize & \",\"\"FreePhysicalMemory\"\":\" & objOS.FreePhysicalMemory & \"}\")
    Next"
        );
    }

    /**
     * cpu 占用率
     * @return mixed
     */
    public function getCpuUsage(): mixed
    {
        if ($this->isWin) {
            $path = $this->getCpuUsageVbsPath();
             $this->exec("cscript -nologo \"{$path}\"", $usage);
            return intval($usage[0]);
        } elseif ($this->isMac) {
            is_null($this->linCpuUsage) and $this->getMacStatus();
            return $this->linCpuUsage;
        } else {
            is_null($this->linCpuUsage) and $this->getLinuxStatus();
            return $this->linCpuUsage;
        }
    }

    /**
     * 获取 内存占用率
     * @return mixed
     */
    public function getMemUsage(): mixed
    {
        if ($this->isWin) {
            $path = $this->getMemUsagePath();
            $this->exec("cscript -nologo \"$path\"", $usage);
            $memory = json_decode($usage[0], true);
            return Round((($memory['TotalVisibleMemorySize'] - $memory['FreePhysicalMemory']) / $memory['TotalVisibleMemorySize']) * 100);
        } elseif ($this->isMac) {
            is_null($this->linMemUsage) and $this->getMacStatus();
            return $this->linMemUsage;
        }  else {
            is_null($this->linMemUsage) and $this->getLinuxStatus();
            return $this->linMemUsage;
        }
    }

    /**
     * 磁盘使用率
     */
    public function getHdUsage(): float
    {
        $storage = [];
        $sys_hd = $this->isWin ? 'C:' : '/';

        $hdc_free = disk_free_space($sys_hd);
        $hdc_total = disk_total_space($sys_hd);
        return  floor(100 * $hdc_free / $hdc_total);
    }

    /**
     * 获取负载率
     * @return mixed
     */
    public function getLoad(): mixed
    {
        $load = null;

        if ($this->isWin) {
            $cmd = "WMIC CPU GET LOADPERCENTAGE /ALL";
            @exec($cmd,  $output);

            if ($output)
            {
                foreach ($output as $line)
                {
                    if ($line && preg_match("/^[0-9]+\$/", $line))
                    {
                        $load = $line;
                        break;
                    }
                }
            }
        } else {
            $sys_load = sys_getloadavg();
            $load = $sys_load[0];
        }

        return $load;
    }

    /**
     * 检查进程存在
     * @param $process_name
     * @return bool
     */
    public function checkProcessExists($process_name): bool
    {
        $output = null;

        $cmd = $this->isWin ? "TASKLIST | FINDSTR {$process_name}" : "ps -ax | grep {$process_name}";
        if ($this->isWin) {
            $this->exec($cmd, $output);
            if (!empty($output[0])) {
                return true;
            }
        } else {
            $this->exec($cmd, $output);
            if ($output && count($output) >= 2) {
                return false;
            }
        }

        return false;
    }

    /**
     * 命令执行
     * @param $cmd
     * @param $out
     * @return int
     */
    protected function exec($cmd, &$out = null): int
    {
        $desc = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );
        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $ret_val = proc_close($proc);

        if (func_num_args() == 2) {
            $out = array($ret, $err);
        }
        return $ret_val;
    }

    /**
     * 是否 win
     * @return bool
     */
    private function isWin(): bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            || str_starts_with(php_uname(), 'Windows')
            || DIRECTORY_SEPARATOR == '\\');
    }

    /**
     * 是否 win
     * @return bool
     */
    private function isMac(): bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'Darwin'
            || str_starts_with(php_uname(), 'Darwin')
            || DIRECTORY_SEPARATOR == '\\');
    }
}