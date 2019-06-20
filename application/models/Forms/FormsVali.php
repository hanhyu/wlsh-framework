<?php

namespace App\Models\Forms;

use WebGeeker\Validation\Validation;

/**
 * 国际化
 * Class FormsVali
 * @package App\Models\Forms
 */
class FormsVali extends Validation
{
    // “错误提示信息模版”翻译对照表
    protected static $langCode2ErrorTemplates = [
        "zh-tw" => [
            'Int'   => '“{{param}}”必須是整數', // 🌝
            'IntGt' => '“{{param}}”必須大於 {{min}}',
            'Str'   => '“{{param}}”必須是字符串',
        ],
        "en-us" => [
            'Int'            => '“{{param}}” must be an integer',
            'IntEq'          => '“{{param}}” must be equal to {{value}}',
            'IntGt'          => '“{{param}}” must be greater than {{min}}',
            'IntGe'          => '“{{param}}” must be greater than or equal to {{min}}',
            'IntLt'          => '“{{param}}” must be smaller than {{max}}',
            'IntLe'          => '“{{param}}” must be less than or equal to {{max}}',
            'IntGtLt'        => '“{{param}}” must be greater than {{min}} and smaller than {{max}}',
            'IntGeLe'        => '“{{param}}” must be greater than or equal to {{min}} and less than or equal to {{max}}',
            'IntGtLe'        => '“{{param}}” must be greater than {{min}} and less than or equal to {{max}}',
            'IntGeLt'        => '“{{param}}” must be greater than or equal to {{min}} and smaller than {{max}}',
            'IntIn'          => '“{{param}}” can only be values: {{valueList}}',
            'IntNotIn'       => '“{{param}}” can`t take values: {{valueList}}',

            // 浮点型（内部一律使用double来处理）
            'Float'          => '“{{param}}” must be floating point',
            'FloatGt'        => '“{{param}}” must be greater than {{min}}',
            'FloatGe'        => '“{{param}}” must be greater than or equal to {{min}}',
            'FloatLt'        => '“{{param}}” must be smaller than {{max}}',
            'FloatLe'        => '“{{param}}” must be less than or equal to {{max}}',
            'FloatGtLt'      => '“{{param}}” must be greater than {{min}} and smaller than {{max}}',
            'FloatGeLe'      => '“{{param}}” must be greater than or equal to {{min}} and less than or equal to {{max}}',
            'FloatGtLe'      => '“{{param}}” must be greater than {{min}} and less than or equal to {{max}}',
            'FloatGeLt'      => '“{{param}}” must be greater than or equal to {{min}} and smaller than {{max}}',

            // bool型
            'Bool'           => '“{{param}}” must be bool(true or false)', // 忽略大小写
            'BoolSmart'      => '“{{param}}” can only be values: true, false, 1, 0, yes, no, y, n（忽略大小写）',

            // 字符串
            'Str'            => '“{{param}}” must be a string',
            'StrEq'          => '“{{param}}” must be equal to "{{value}}"',
            'StrEqI'         => '“{{param}}” must be equal to "{{value}}"（忽略大小写）',
            'StrNe'          => '“{{param}}” not equal to "{{value}}"',
            'StrNeI'         => '“{{param}}” not equal to "{{value}}"（忽略大小写）',
            'StrIn'          => '“{{param}}” can only be values: {{valueList}}',
            'StrInI'         => '“{{param}}” can only be values: {{valueList}}（忽略大小写）',
            'StrNotIn'       => '“{{param}}” can`t take values: {{valueList}}',
            'StrNotInI'      => '“{{param}}” can`t take values: {{valueList}}（忽略大小写）',
            // todo StrSame:var 检测某个参数是否等于另一个参数, 比如password2要等于password
            'StrLen'         => '“{{param}}” length has to be equal to {{length}}', // 字符串长度
            'StrLenGe'       => '“{{param}}” length has to be greater than or equal to {{min}}',
            'StrLenLe'       => '“{{param}}” length has to be less than or equal to {{max}}',
            'StrLenGeLe'     => '“{{param}}” Length must be between {{min}} - {{max}}', // 字符串长度
            'ByteLen'        => '“{{param}}” length (bytes) must be equal to {{length}}', // 字符串长度
            'ByteLenGe'      => '“{{param}}” length (bytes) must be greater than or equal to {{min}}',
            'ByteLenLe'      => '“{{param}}” length (bytes) must be less than or equal {{max}}',
            'ByteLenGeLe'    => '“{{param}}” length (bytes) must be in {{min}} - {{max}} between', // 字符串长度
            'Letters'        => '“{{param}}” can only contain letters',
            'Alphabet'       => '“{{param}}” can only contain letters', // 同Letters
            'Numbers'        => '“{{param}}” can only be pure Numbers',
            'Digits'         => '“{{param}}” can only be pure Numbers', // 同Numbers
            'LettersNumbers' => '“{{param}}” only letters and Numbers can be included',
            'Numeric'        => '“{{param}}” has to be number', // 一般用于大数处理（超过double表示范围的数,一般会用字符串来表示）, 如果是正常范围内的数, 可以使用'Int'或'Float'来检测
            'VarName'        => '“{{param}}” Only letters, Numbers, and underscores can be included and begin with a letter or underscore',
            'Email'          => '“{{param}}” not a legitimate email',
            'Url'            => '“{{param}}” not a valid Url',
            'Ip'             => '“{{param}}” not a legitimate IP address',
            'Mac'            => '“{{param}}” not a valid MAC address',
            'Regexp'         => '“{{param}}” does not match the regular expression “{{regexp}}”', // Perl正则表达式匹配. 目前不支持modifiers. http://www.rexegg.com/regex-modifiers.html

            // 数组. 如何检测数组长度为0, Arr认证的是数值型索引数组
            'Arr'            => '“{{param}}” has to be an array',
            'ArrLen'         => '“{{param}}” length has to be equal to {{length}}',
            'ArrLenGe'       => '“{{param}}” length has to be greater than or equal to {{min}}',
            'ArrLenLe'       => '“{{param}}” length has to be less than or equal to {{max}}',
            'ArrLenGeLe'     => '“{{param}}” length has to be in {{min}} ~ {{max}} between',

            // 对象，Obj认证的是键（字符串）值对型数组
            'Obj'            => '“{{param}}” has to be an object',

            // 文件
            'File'           => '“{{param}}”has to be a file',
            'FileMaxSize'    => '“{{param}}” must be a file, and the file size must not exceed{{size}}',
            'FileMinSize'    => '“{{param}}” must be a file, and the file size is not less than{{size}}',
            'FileImage'      => '“{{param}}” has to be a picture',
            'FileVideo'      => '“{{param}}” must be a video file',
            'FileAudio'      => '“{{param}}” must be an audio file',
            'FileMimes'      => '“{{param}}” must be files of these MIME types:{{mimes}}',

            // Date & Time
            'Date'           => '“{{param}}” must conform to date formatYYYY-MM-DD',
            'DateFrom'       => '“{{param}}” shall not be earlier than {{from}}',
            'DateTo'         => '“{{param}}” shall not be later than {{to}}',
            'DateFromTo'     => '“{{param}}” must be in {{from}} ~ {{to}} between',
            'DateTime'       => '“{{param}}” must conform to date and time format YYYY-MM-DD HH:mm:ss',
            'DateTimeFrom'   => '“{{param}}” shall not be earlier than {{from}}',
            'DateTimeTo'     => '“{{param}}” must be earlier than {{to}}',
            'DateTimeFromTo' => '“{{param}}” must be in {{from}} ~ {{to}} between',

            // 其它
            'Required'       => 'must provide “{{param}}”',
        ],
    ];

    // 文本翻译对照表
    protected static $langCodeToTranslations = [
        "zh-tw" => [
            "变量"      => "變量", // 🌙
            "变量必须是整数" => "變量必須是整數", // ⭐
        ],
        "en-us" => [
            "变量"      => "variable",
            "变量必须是整数" => "variable must be an integer",
            "手机号"     => "mobile phone no",
        ],
    ];

}