<?php

namespace app\manager\controller;

use app\manager\controller\Base;
use support\Redis;
use Webman\Event\Event;
use Workerman\Timer;
use app\manager\model\Short as Model;

class Short extends Base
{
    public function create()
    {
        $link = $this->request->post('link');
        $name = $this->request->post('name');
        $desc = $this->request->post('desc');

        Model::validateLink($link);

        if (Model::where('link', $link)->count() > 0) {
            return $this->p('链接已存在', 500);
        }

        $code  = Model::generateCode($link);
        $short = Model::create([
            'code'    => $code,
            'link'    => $link,
            'user_id' => $this->request->user->id,
            'status'  => Model::NORMAL,
            'name'    => $name,
            'desc'    => $desc,
        ]);
        if ($short) {
            Event::emit('short.insert', ['code' => $code, 'link' => $link]);
            return $this->p('生成成功', 200, ['code' => $code]);
        } else {
            return $this->p('生成失败', 500);
        }
    }

    public function update()
    {
        $id   = $this->request->post('id');
        $link = $this->request->post('link');
        $name = $this->request->post('name');
        $desc = $this->request->post('desc');

        Model::validateLink($link);

        if (Model::where(['link' => $link, 'status' => Model::NORMAL, ['id', '<>', $id],])->count() > 0) {
            return $this->p('链接已存在', 500);
        }

        $short = Model::find($id);

        if (!$short) {
            return $this->p('短链接不存在', 500);
        }

        if ($link != $short->link) {
            Event::emit('short.delete', $short->code);
            $code        = Model::generateCode($link);
            $short->code = $code;
        }

        $short->link = $link;
        $short->name = $name;
        $short->desc = $desc;
        if ($short->save()) {
            Event::emit('short.insert', ['code' => $code, 'link' => $link]);
            return $this->p('更新成功', 200);
        }
        return $this->p('更新失败', 500);
    }
}
