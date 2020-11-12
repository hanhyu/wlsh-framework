<?php declare(strict_types=1);


namespace App\Models\Mysql;


use App\Library\DI;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Model;

class UserMysql extends Model
{
    protected string $name = 'system_user';

    /**
     * 测试think db性能
     *
     * User: hanhyu
     * Date: 2020/9/20
     * Time: 下午10:13
     *
     * @param $name
     *
     * @return array
     */
    public function getInfo($name)
    {
        $mysql_pool_obj = DI::get('thk_mysql_pool_obj');
        /** @var $db Db */
        $db = $mysql_pool_obj->get();
        try {
            return $db->find(1)->toArray();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        $mysql_pool_obj->put($db);
    }

}
