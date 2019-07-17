<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-6-20
 * Time: 上午10:08
 */
class NotFound extends Exception
{

}

class AutoReload
{
    /**
     * @var resource
     */
    protected $inotify;
    protected $pid;
    protected $reloadFileTypes = array('.php' => true);
    protected $watchFiles = array();
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

    function putLog($log)
    {
        $_log = "[" . date('Y-m-d H:i:s') . "]\t" . $log . "\n";
        echo $_log;
    }

    /**
     * @param $serverPid
     * @throws NotFound
     */
    function __construct($serverPid)
    {
        $this->pid = $serverPid;
        if (posix_kill($serverPid, 0) === false) {
            throw new NotFound("Process#$serverPid not found.");
        }
        $this->inotify = inotify_init();
        $this->events = IN_MODIFY | IN_DELETE | IN_CREATE | IN_MOVE;
        Swoole\Event::add($this->inotify, function ($ifd) {
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

    function reload()
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
    function addFileType($type)
    {
        $type = trim($type, '.');
        $this->reloadFileTypes['.' . $type] = true;
    }

    /**
     * 添加事件
     * @param $inotifyEvent
     */
    function addEvent($inotifyEvent)
    {
        $this->events |= $inotifyEvent;
    }

    /**
     * 清理所有inotify监听
     */
    function clearWatch()
    {
        foreach ($this->watchFiles as $wd) {
            inotify_rm_watch($this->inotify, $wd);
        }
        $this->watchFiles = array();
    }

    /**
     * 设置要监听的源码目录
     * @param $dir
     * @param bool $root
     * @return bool
     * @throws NotFound
     */
    function watch($dir, $root = true)
    {
        //目录不存在
        if (!is_dir($dir)) {
            throw new NotFound("[$dir] is not a directory.");
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

    function run()
    {
        //swoole_event_wait();
    }
}