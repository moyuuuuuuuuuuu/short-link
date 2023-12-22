<?php

namespace app\common\model;

use app\exception\SystemException;
use Illuminate\Database\Eloquent\SoftDeletes;
use support\Model;

class Short extends Model
{
    use SoftDeletes;

    /**
     * 默认过期时间
     */
    const DEFAULT_EXPIRE_TIME = 7800;

    /**
     * 默认热度
     */
    const DEFAULT_HOTNESS = 3;
    /**
     * 每掉热度衰减时间
     */
    const HOTNESS_DECAY_TIME = 260;

    /**
     * 每次衰减热度数
     */
    const HOTNESS_DECAY_NUM = 0.1;

    /**
     * 每访问一次增加热度数
     */
    const HOTNESS_INC_NUM = 0.2;

    /**
     * 状态：正常
     */
    const NORMAL = 1;
    /**
     * 生成短链接最大尝试次数
     */
    const REGENERATE_CODE_MAX_NUM = 10;

    protected $table = 'short';

    protected $fillable = ['code', 'link', 'created_at', 'updated_at', 'user_id', 'status', 'name', 'desc'];

    public static function generateCode($link)
    {
        $code = base_convert(abs(crc32($link)), 10, 36); // 获取编码后的前6位作为短链接
        $num  = 0;
        while (self::where(['code' => $code, 'status' => self::NORMAL])->count() > 0 && $num < self::REGENERATE_CODE_MAX_NUM) {
            $code = base_convert(abs(crc32($link)), 10, 36); // 获取编码后的前6位作为短链接
            $num++;
        }
        if ($num >= self::REGENERATE_CODE_MAX_NUM)
            throw new SystemException('生成短链接失败');
        return $code;
    }

    public static function validateLink(string $link)
    {

        if (empty($link)) {
            throw new SystemException('链接不能为空');
        }


        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            throw new SystemException('链接不合法');
        }
        #TODO: 违规站点检测
    }
}
