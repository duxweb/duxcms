<?php

namespace App\Sms\Service\Enum;

enum Type: string
{
    case ALIYUN = 'aliyun';
    case ALIYUNREST = 'aliyunrest';
    case ALIYUNINTL = 'aliyunintl';
    case YUNPIAN = 'yunpian';
    case SUBMAIL = 'submail';
    case LUOSIMAO = 'luosimao';
    case YUNTONGXUN = 'yuntongxun';
    case HUYI = 'huyi';
    case JUHE = 'juhe';
    case SENDCLOUD = 'sendcloud';
    case BAIDU = 'baidu';
    case HUAXIN = 'huaxin';
    case CHUANGLAN = 'chuanglan';
    case RONGCLOUD = 'rongcloud';
    case TIANYIWUXIAN = 'tianyiwuxian';
    case TWILIO = 'twilio';
    case TINIYO = 'tiniyo';
    case QCLOUD = 'qcloud';
    case AVATARDATA = 'avatardata';
    case HUAWEI = 'huawei';
    case YUNXIN = 'yunxin';
    case YUNZHIXUN = 'yunzhixun';
    case KINGTTO = 'kingtto';
    case QINIU = 'qiniu';
    case UCLOUD = 'ucloud';
    case SMSBAO = 'smsbao';
    case MODUYUN = 'moduyun';
    case RONGHEYUN = 'rongheyun';
    case ZZYUN = 'zzyun';
    case MAAP = 'maap';
    case TINREE = 'tinree';
    case VOLCENGINE = 'volcengine';
    case VAPTCHA = 'vaptcha';
    case UNISMS = 'unisms';

    public function name(): string
    {
        return match ($this) {
            self::ALIYUN => '阿里云',
            self::ALIYUNREST => '阿里云Rest',
            self::ALIYUNINTL => '阿里云国际',
            self::YUNPIAN => '云片',
            self::SUBMAIL => 'Submail',
            self::LUOSIMAO => '螺丝帽',
            self::YUNTONGXUN => '容联云通讯',
            self::HUYI => '互亿无线',
            self::JUHE => '聚合数据',
            self::SENDCLOUD => 'SendCloud',
            self::BAIDU => '百度云',
            self::HUAXIN => '华信短信平台',
            self::CHUANGLAN => '253云通讯（创蓝）',
            self::RONGCLOUD => '融云',
            self::TIANYIWUXIAN => '天翼无线',
            self::TWILIO => 'twilio',
            self::TINIYO => 'tiniyo',
            self::QCLOUD => '腾讯云 SMS',
            self::AVATARDATA => '阿凡达数据',
            self::HUAWEI => '华为云 SMS',
            self::YUNXIN => '网易云信',
            self::YUNZHIXUN => '云之讯',
            self::KINGTTO => '凯信通',
            self::QINIU => '七牛云',
            self::UCLOUD => 'Ucloud',
            self::SMSBAO => '短信宝',
            self::MODUYUN => '摩杜云',
            self::RONGHEYUN => '融合云（助通）',
            self::ZZYUN => '蜘蛛云',
            self::MAAP => '融合云信',
            self::TINREE => '天瑞云',
            self::VOLCENGINE => '火山引擎',
            self::VAPTCHA => 'vaptcha',
            self::UNISMS => 'uniSMS',
        };
    }

    public function type(): int
    {
        return match ($this) {
            self::ALIYUN, self::ALIYUNREST, self::ALIYUNINTL, self::SUBMAIL, self::YUNTONGXUN, self::JUHE, self::SENDCLOUD, self::BAIDU, self::RONGCLOUD, self::QCLOUD, self::AVATARDATA, self::HUAWEI, self::YUNXIN, self::YUNZHIXUN, self::QINIU, self::UCLOUD, self::MODUYUN, self::RONGHEYUN, self::ZZYUN, self::MAAP, self::TINREE, self::VOLCENGINE, self::VAPTCHA, self::UNISMS => 1,
            self::YUNPIAN, self::LUOSIMAO, self::HUYI, self::HUAXIN, self::CHUANGLAN, self::TIANYIWUXIAN, self::TWILIO, self::TINIYO, self::KINGTTO, self::SMSBAO => 0,
        };
    }

    public static function list(): array {
        return array_map(
            static fn(Type $status) => ['name' => $status->name(), 'type' => $status->type(), 'value' => $status->value],
            self::cases()
        );
    }
}