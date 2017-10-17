<?php
/**
 * 混淆PHP代码
 *
 * Function
 * - 混淆变量
 * - 删除注释
 * - 删除空行
 *
 * Option
 * Options                 Default                    Desc
 * --no-obscure-var        混淆变量                   不混淆代码
 * --keep-comment          删除注释                   保留注释
 * --keep-blank-line       删除空行                   保留空行
 * --path                  项目test                   待混淆路径
 * --save-path             混淆项目路径+obscure后缀   保存路径
 *
 * Usage
 * 命令行
 * > php obscure.php
 * 使用默认配置，默认混淆变量/删除注释/删除空行
 * > php obscure.php --path=D:\ObscurePHP\src\test
 * 指定需混淆路径
 * > php obscure.php --no-obscure-var
 * 不混淆变量
 * > php obscure.php --keep-comment
 * 保留注释
 * > php obscure.php --keep-blank-line
 * 保留空行
 *
 * MIT license
 * Github: https://github.com/sunshinexcode/ObscurePHP
 * Email: 24xinhui@163.com
 *
 * @author sunshine
 */
$options = getopt('', ['no-obscure-var', 'keep-comment', 'keep-blank-line', 'path::', 'save-path::']);

// 排除替换变量
$EXCEPT_REPLACE_VAR = ['$this', '$_SERVER', '$_SESSION', '$_REQUEST', '$_POST', '$_GET',
    '$_COOKIE', '$_FILES', '$GLOBALS', '$_COOKIE'
];
// 混淆变量
$obscure_var = isset($options['no-obscure-var']) ? false : true;
// 删除注释
$remove_comment = isset($options['keep-comment']) ? false : true;
// 删除空行
$remove_blank_line = isset($options['keep-blank-line']) ? false : true;
// 混淆路径
$path = isset($options['path']) ? $options['path'] : __DIR__ . DIRECTORY_SEPARATOR . 'test';
// 保存路径
$save_path = isset($options['save-path']) ? $options['save-path'] : $path . '_obscure';

// 获取目录文件
$file = [];
get_dir($path, $file);
foreach ($file as $val) {
    // 获取文件内容
    $content = file_get_contents($val);
    // 混淆
    if ($obscure_var) $content = obscure_php($content, $EXCEPT_REPLACE_VAR);
    // 删除注释
    if ($remove_comment) $content = remove_comment($content);
    // 删除空行
    if ($remove_blank_line) $content = remove_blank_line($content);
    // 替换路径
    $write = str_replace($path, $save_path, $val);
    // 创建目录
    $dir = dirname($write);
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    // 写入文件
    file_put_contents($write, $content);
    // 输出
    echo $val . '=>' . $write . "\r\n";
}

/**
 * 混淆php
 *
 * @param string $content
 * @param array  $except
 * @return string
 * @author sunshine
 */
function obscure_php($content, $except)
{
    // 匹配php变量
    preg_match_all('/\$[a-z0-9_][a-z0-9_]*/i', $content, $out);
    // 去重
    $var = array_unique($out[0]);
    // 生成随机字符串
    $str = [];
    $num = 0;
    $total = count($var);
    while ($num < $total) {
        // 随机
        $rand = rand_str(rand(1, 6));
        $str[$rand] = $rand;
        $num = count($str);
    }
    // 混淆变量
    foreach ($var as $val) {
        // 排除替换变量
        if (in_array($val, $except)) continue;
        // 替换变量, 排除类变量
        $content = preg_replace('@(?<!protected\s)(?<!public\s)(?<!private\s)\\' . $val . '@', '$' . array_pop($str), $content);
    }

    return $content;
}

/**
 * 删除注释
 *
 * @param string $php
 * @return string
 * @author sunshine
 */
function remove_comment($php)
{
    $search = [
        '@/\*.*?\*/@s', // 去除多行注释
        '@\s+//.*$@m',  // 去除单行注释
    ];
    $replace = [
        '',
        '',
    ];
    return preg_replace($search, $replace, $php);
}

/**
 * 删除空行
 *
 * @param string $php
 * @return string
 * @author sunshine
 */
function remove_blank_line($php)
{
    $search = [
        '~(\n\s*\n){1,}~'
    ];
    $replace = [
        "\n"
    ];
    return preg_replace($search, $replace, $php);
}

/**
 * 生成随机字符串
 *
 * @param  int $len
 * @return string
 * @author sunshine
 */
function rand_str($len = 6)
{
    $char = 'abcdefghijklmnopqrstuvwxyz_';
    $str = '';
    for ($i = 0; $i < $len; $i++) $str .= $char[rand(0, 26)];
    return $str;
}

/**
 * 递归获取指定目录下的所有文件
 *
 * @param $path
 * @param &$file
 * @param $remove
 * @param $type
 * @return void
 * @author sunshine
 */
function get_dir($path, & $file, $remove = [], $type = ['php'])
{
    if (!file_exists($path)) return;
    if (is_file($path)) {
        if (!in_array($path, $remove) && ($type != null && in_array(get_file_ext($path), $type))) array_push($file, $path);
    } else {
        $handle = opendir($path);
        while (($f = readdir($handle)) != '') {
            if ($f != '.' && $f != '..' && $f != '' && $f != '.svn') get_dir($path . '/' . $f, $file, $remove, $type);
        }
        closedir($handle);
    }
}

/**
 * 获取文件后缀名
 *
 * @param string $file
 * @return string
 * @author sunshine
 */
function get_file_ext($file)
{
    return substr(strrchr($file, '.'), 1);
}

/**
 * 打印
 *
 * @param  mixed $data
 * @param  bool  $exit
 * @return void
 * @author sunshine
 */
function d($data, $exit = true)
{
    print_r($data);
    if ($exit) exit();
}