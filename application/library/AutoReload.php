<?php
namespace App\Library;

use RuntimeException;
use Swoole\Event;

class AutoReload
{
    /**
     * @var resource
     */
    protected $inotify;
    protected $pid;
    protected $reloadFileTypes = ['.php' => true];
    protected $watchFiles = [];
    /**
     * 正在reload
     */
    protected $reloading = false;
    protected $events;
    /**
     * 根目录
     * @var array
     */
    protected $rootDirs = array();

    public function putLog($log)
    {
        $_log = '[' . date('Y-m-d H:i:s') . "]\t" . $log . "\n";
        echo $_log;
    }

    /**
     * @param $serverPid
     * @throws RuntimeException
     */
    public function __construct($serverPid)
    {
        $this->pid = $serverPid;
        if (posix_kill($serverPid, 0) === false) {
            throw new RuntimeException("ProcessDomain#$serverPid not found.");
        }
        $this->inotify = inotify_init();
        $this->events  = IN_MODIFY | IN_DELETE | IN_CREATE | IN_MOVE;
        Event::add($this->inotify, function ($ifd) {
            $events = inotify_read($this->inotify);
            if (!$events) {
                return;
            }
            foreach ($events as $ev) {
                if ($ev['mask'] == IN_IGNORED) {
                    continue;
                } else if ($ev['mask'] == IN_CREATE or $ev['mask'] == IN_DELETE or $ev['mask'] == IN_MODIFY or $ev['mask'] == IN_MOVED_TO or $ev['mask'] == IN_MOVED_FROM) {
                    $fileType = strrchr($ev['name'], '.');
                    //非重启类型
                    if (!isset($this->reloadFileTypes[$fileType])) {
                        continue;
                    }
                }

                if (!$this->reloading) {
                    //有事件发生了，进行重启
                    //swoole_timer_after(1000, array($this, 'reload'));
                    $this->putLog("reloading");
                    posix_kill($this->pid, SIGUSR1);
                    $this->reloading = true;
                }
            }
        });
    }

    public function reload()
    {
        $this->putLog("reloading");
        //向主进程发送信号
        posix_kill($this->pid, SIGUSR1);
        //清理所有监听
        //$this->clearWatch();
        //重新监听
        //foreach ($this->rootDirs as $root) {
        //    $this->watch($root);
        //}
        //继续进行reload
        $this->reloading = false;
    }

    /**
     * 添加需要监听文件类型
     * @param $type
     */
    public function addFileType($type)
    {
        $type                               = trim($type, '.');
        $this->reloadFileTypes['.' . $type] = true;
    }

    /**
     * 添加事件
     * @param $inotifyEvent
     */
    public function addEvent($inotifyEvent)
    {
        $this->events |= $inotifyEvent;
    }

    /**
     * 清理所有inotify监听
     */
    public function clearWatch()
    {
        foreach ($this->watchFiles as $wd) {
            inotify_rm_watch($this->inotify, $wd);
        }
        $this->watchFiles = [];
    }

    /**
     * 设置要监听的源码目录
     * @param      $dir
     * @param bool $root
     * @return bool
     * @throws RuntimeException
     */
    public function watch($dir, $root = true)
    {
        //目录不存在
        if (!is_dir($dir)) {
            throw new RuntimeException("[$dir] is not a directory.");
        }
        //避免重复监听
        if (isset($this->watchFiles[$dir])) {
            return false;
        }
        //根目录
        if ($root) {
            $this->rootDirs[] = $dir;
        }
        $wd = inotify_add_watch($this->inotify, $dir, $this->events);
        $this->watchFiles[$dir] = $wd;
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..') {
                continue;
            }
            $path = $dir . '/' . $f;
            //递归目录
            if (is_dir($path)) {
                $this->watch($path, false);
            }
            //检测文件类型
            $fileType = strrchr($f, '.');
            if (isset($this->reloadFileTypes[$fileType])) {
                $wd = inotify_add_watch($this->inotify, $path, $this->events);
                $this->watchFiles[$path] = $wd;
            }
        }
        return true;
    }

    public function run()
    {
        //swoole_event_wait();
    }
}
