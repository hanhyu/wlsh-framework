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
            'IntEq'          => '“{{param}}”必须等于 {{value}}',
            'IntGt'          => '“{{param}}” must be greater than {{min}}',
            'IntGe'          => '“{{param}}”必须大于等于 {{min}}',
            'IntLt'          => '“{{param}}”必须小于 {{max}}',
            'IntLe'          => '“{{param}}”必须小于等于 {{max}}',
            'IntGtLt'        => '“{{param}}”必须大于 {{min}} 小于 {{max}}',
            'IntGeLe'        => '“{{param}}”必须大于等于 {{min}} 小于等于 {{max}}',
            'IntGtLe'        => '“{{param}}”必须大于 {{min}} 小于等于 {{max}}',
            'IntGeLt'        => '“{{param}}”必须大于等于 {{min}} 小于 {{max}}',
            'IntIn'          => '“{{param}}”只能取这些值: {{valueList}}',
            'IntNotIn'       => '“{{param}}”不能取这些值: {{valueList}}',

            // 浮点型（内部一律使用double来处理）
            'Float'          => '“{{param}}”必须是浮点数',
            'FloatGt'        => '“{{param}}”必须大于 {{min}}',
            'FloatGe'        => '“{{param}}”必须大于等于 {{min}}',
            'FloatLt'        => '“{{param}}”必须小于 {{max}}',
            'FloatLe'        => '“{{param}}”必须小于等于 {{max}}',
            'FloatGtLt'      => '“{{param}}”必须大于 {{min}} 小于 {{max}}',
            'FloatGeLe'      => '“{{param}}”必须大于等于 {{min}} 小于等于 {{max}}',
            'FloatGtLe'      => '“{{param}}”必须大于 {{min}} 小于等于 {{max}}',
            'FloatGeLt'      => '“{{param}}”必须大于等于 {{min}} 小于 {{max}}',

            // bool型
            'Bool'           => '“{{param}}”必须是bool型(true or false)', // 忽略大小写
            'BoolSmart'      => '“{{param}}”只能取这些值: true, false, 1, 0, yes, no, y, n（忽略大小写）',

            // 字符串
            'Str'            => '“{{param}}” must be a string',
            'StrEq'          => '“{{param}}”必须等于"{{value}}"',
            'StrEqI'         => '“{{param}}”必须等于"{{value}}"（忽略大小写）',
            'StrNe'          => '“{{param}}”不能等于"{{value}}"',
            'StrNeI'         => '“{{param}}”不能等于"{{value}}"（忽略大小写）',
            'StrIn'          => '“{{param}}”只能取这些值: {{valueList}}',
            'StrInI'         => '“{{param}}”只能取这些值: {{valueList}}（忽略大小写）',
            'StrNotIn'       => '“{{param}}”不能取这些值: {{valueList}}',
            'StrNotInI'      => '“{{param}}”不能取这些值: {{valueList}}（忽略大小写）',
            // todo StrSame:var 检测某个参数是否等于另一个参数, 比如password2要等于password
            'StrLen'         => '“{{param}}”长度必须等于 {{length}}', // 字符串长度
            'StrLenGe'       => '“{{param}}”长度必须大于等于 {{min}}',
            'StrLenLe'       => '“{{param}}”长度必须小于等于 {{max}}',
            'StrLenGeLe'     => '“{{param}}” Length must be between {{min}} - {{max}}', // 字符串长度
            'ByteLen'        => '“{{param}}”长度（字节）必须等于 {{length}}', // 字符串长度
            'ByteLenGe'      => '“{{param}}”长度（字节）必须大于等于 {{min}}',
            'ByteLenLe'      => '“{{param}}”长度（字节）必须小于等于 {{max}}',
            'ByteLenGeLe'    => '“{{param}}”长度（字节）必须在 {{min}} - {{max}} 之间', // 字符串长度
            'Letters'        => '“{{param}}”只能包含字母',
            'Alphabet'       => '“{{param}}”只能包含字母', // 同Letters
            'Numbers'        => '“{{param}}”只能是纯数字',
            'Digits'         => '“{{param}}”只能是纯数字', // 同Numbers
            'LettersNumbers' => '“{{param}}”只能包含字母和数字',
            'Numeric'        => '“{{param}}”必须是数值', // 一般用于大数处理（超过double表示范围的数,一般会用字符串来表示）, 如果是正常范围内的数, 可以使用'Int'或'Float'来检测
            'VarName'        => '“{{param}}”只能包含字母、数字和下划线，并且以字母或下划线开头',
            'Email'          => '“{{param}}”不是合法的email',
            'Url'            => '“{{param}}”不是合法的Url地址',
            'Ip'             => '“{{param}}”不是合法的IP地址',
            'Mac'            => '“{{param}}”不是合法的MAC地址',
            'Regexp'         => '“{{param}}”不匹配正则表达式“{{regexp}}”', // Perl正则表达式匹配. 目前不支持modifiers. http://www.rexegg.com/regex-modifiers.html

            // 数组. 如何检测数组长度为0
            'Arr'            => '“{{param}}”必须是数组',
            'ArrLen'         => '“{{param}}”长度必须等于 {{length}}',
            'ArrLenGe'       => '“{{param}}”长度必须大于等于 {{min}}',
            'ArrLenLe'       => '“{{param}}”长度必须小于等于 {{max}}',
            'ArrLenGeLe'     => '“{{param}}”长度必须在 {{min}} ~ {{max}} 之间',

            // 对象
            'Obj'            => '“{{param}}”必须是对象',

            // 文件
            'File'           => '“{{param}}”必须是文件',
            'FileMaxSize'    => '“{{param}}”必须是文件, 且文件大小不超过{{size}}',
            'FileMinSize'    => '“{{param}}”必须是文件, 且文件大小不小于{{size}}',
            'FileImage'      => '“{{param}}”必须是图片',
            'FileVideo'      => '“{{param}}”必须是视频文件',
            'FileAudio'      => '“{{param}}”必须是音频文件',
            'FileMimes'      => '“{{param}}”必须是这些MIME类型的文件:{{mimes}}',

            // Date & Time
            'Date'           => '“{{param}}”必须符合日期格式YYYY-MM-DD',
            'DateFrom'       => '“{{param}}”不得早于 {{from}}',
            'DateTo'         => '“{{param}}”不得晚于 {{to}}',
            'DateFromTo'     => '“{{param}}”必须在 {{from}} ~ {{to}} 之间',
            'DateTime'       => '“{{param}}”必须符合日期时间格式YYYY-MM-DD HH:mm:ss',
            'DateTimeFrom'   => '“{{param}}”不得早于 {{from}}',
            'DateTimeTo'     => '“{{param}}”必须早于 {{to}}',
            'DateTimeFromTo' => '“{{param}}”必须在 {{from}} ~ {{to}} 之间',

            // 其它
            'Required'       => '必须提供“{{param}}”',
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