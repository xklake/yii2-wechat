<?php
namespace callmez\wechat\controllers\process;

use callmez\wechat\models\MpUser;
use callmez\wechat\models\Wechat;
use Yii;
use callmez\wechat\models\Fans;
use callmez\wechat\components\ProcessController;
/**
 * 微信粉丝请求默认处理
 * @package callmez\wechat\controllers
 */
class FansController extends ProcessController
{

    /**
     * 数据记录
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRecord()
    {
        $wechat = $this->getWechat();
        $fans = $this->getFans();
        if (!$fans) { // 存储粉丝信息
            $fans = Yii::createObject(Fans::className());
            $fans->setAttributes([
                'wid' => $wechat->id,
                'open_id' => $this->message['FromUserName'],
                'status' => Fans::STATUS_SUBSCRIBED
            ]);
            if ($fans->save() && $wechat->status > Wechat::TYPE_SUBSCRIBE) { // 更新用户详细数据, 普通订阅号无权限获取
                $fans->updateUser();
            }
        } elseif ($fans->status != Fans::STATUS_SUBSCRIBED) { // 更新关注状态
            $fans->subscribe();
        }

//        $history = new MessageHistory();
//        $attributes = [
//            'wid' => $wechat->id,
//            'module' => $this->getModuleName($this->api->lastProcessController),
//        ];
    }

    /**
     * 关注处理
     */
    public function actionSubscribe()
    {}

    /**
     * 取消关注处理
     */
    public function actionUnsubscribe()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }
}