<?php

/*

سورس ربات و وبسرویس اسکیرین شات از سایت ها و صفحالت وب
کانال ما : @DimoTM
نویسنده : @DevUltra
منبع بزن.

*/

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set("Asia/Tehran");

#-----------------------------#

$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], 
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range){
	if(!$ok){
		$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
		$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
		if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec){
			$ok=true;
		}
	}
}
if(!$ok){
	exit(header("location: https://google.com"));
}

#-----------------------------#

define('API_KEY', '6193435103:AAHVkSSjj7Hu7PzUv9rdyOc7D48-dCVf19I'); # Bot Token
$api = 'http://' . $_SERVER['SERVER_NAME'] . '/' . basename(__DIR__) . '/webApi.php'; # Dont Touch it

#-----------------------------#

$update = json_decode(file_get_contents("php://input"));
if(isset($update->message)){
    $from_id    = $update->message->from->id;
    $chat_id    = $update->message->chat->id;
    $tc         = $update->message->chat->type;
    $text       = $update->message->text;
    $first_name = $update->message->from->first_name;
    $message_id = $update->message->message_id;
    @$photo     = $update->message->photo;
    @$video     = $update->message->video;
    @$music     = $update->message->audio;
    @$document  = $update->message->document;
    @$voice     = $update->message->voice;
    @$caption   = $update->message->caption;
    $username   = isset($update->message->from->username) ? $update->message->from->username : "ندارد !";
}elseif(isset($update->callback_query)){
    $chat_id    = $update->callback_query->message->chat->id;
    $data       = $update->callback_query->data;
    $query_id   = $update->callback_query->id;
    $message_id = $update->callback_query->message->message_id;
    $in_text    = $update->callback_query->message->text;
    $from_id    = $update->callback_query->from->id;
    $first_name = $update->callback_query->from->first_name;
    $username   = isset($update->callback_query->from->username) ? $update->callback_query->from->username : "ندارد !";
}

#-----------------------------#

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function sendmessage($chat_id,$text,$keyboard = null, $mrk = 'Markdown') {
    bot('sendMessage',[
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $mrk,
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ]);
}

function editmessage($chat_id,$message_id,$text,$keyboard = null, $mrk = 'Markdown') {
    bot('editmessagetext',[
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => $mrk,
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ]);
}

function deletemessage($chat_id,$message_id) {
    bot('deleteMessage',[
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ]);
}

function queryanswer($query_id,$text,$alert) {
    bot('answercallbackquery',[
        'callback_query_id' => $query_id,
        'text' => $text,
        'show_alert' => $alert,
    ]);
}

function step($step) {
    global $from_id;
    $ud = json_decode(file_get_contents("data/$from_id.json"));
    $ud->step = $step;
    file_put_contents("data/$from_id.json", json_encode($ud));
}

function type($type) {
    global $from_id;
    $ud = json_decode(file_get_contents("data/$from_id.json"));
    $ud->type = $type;
    file_put_contents("data/$from_id.json", json_encode($ud));
}

function size($size) {
    global $from_id;
    $ud = json_decode(file_get_contents("data/$from_id.json"));
    $ud->size = $size;
    file_put_contents("data/$from_id.json", json_encode($ud));
}

function last() {
    global $from_id;
    global $message_id;
    $ud = json_decode(file_get_contents("data/$from_id.json"));
    $ud->last = $message_id + 1;
    file_put_contents("data/$from_id.json", json_encode($ud));
}

#-----------------------------#

$start_key = json_encode(['keyboard' => [
    [['text' => "📸 اسکیرین شات 📸"]],
    [['text' => "تنظیمات ⚙️"], ['text' => "☁️ پروفایل"]],
    [['text' => "راهنما 📨"], ['text' => "🗣 درباره ما"]]
], 'resize_keyboard' => true]);

$back_key = json_encode(['keyboard' => [
    [['text' => " ➡️ بازگشت ⬅️"]]
], 'resize_keyboard' => true]);

$setting_key = json_encode(['inline_keyboard' => [
    [['text' => '⬇️ نوع خروجی اسکیرین شات ها ⬇️', 'callback_data' => 'no_data']],
    [['text' => 'عکس معمولی 📷', 'callback_data' => 'photo'],['text' => '📂 داکیومنت', 'callback_data' => 'document']],
    [['text' => '⬇️ سایز اسکیرین شات ها ⬇️', 'callback_data' => 'no_data']],
    [['text' => 'تمام صفحه 🖇', 'callback_data' => 'full'],['text' => '1024 * 1024 px ✂️', 'callback_data' => 'limit']]
]]);

#-----------------------------#

if(!is_dir("data")) mkdir("data");

if(!file_exists("data/$from_id.json")){
    file_put_contents("data/$from_id.json", json_encode(['step' => 'none', 'size' => 'limit', 'type' => 'photo']));
}

$ud = json_decode(file_get_contents("data/$from_id.json"));
$step = $ud->step;
$type = $ud->type;
$size = $ud->size;
$last = $ud->last;

if($text) last();

if($text == '/start' or $text == '➡️ بازگشت ⬅️'){
    
    step('none');
    $bot_name = bot('getMe')->result->first_name;
    sendmessage($from_id, "❤️‍🔥 سلام <a href='tg://user?id=$from_id'>$first_name</a> عزیز!\n\n✅ به ربات <b>$bot_name</b> خوش اومدی!\n🔗 من میتونم از سایتی که میدی یه اسکیرین شات برات بگیرم و بفرستم.\n\n↩️ تو منو امتحان کن :)\n.", $start_key, 'HTML');
    
}

elseif($text == 'راهنما 📨'){

    sendmessage($from_id, "✅ برای گرفتن اسکیرین شات، به بخش '📸 اسکیرین شات 📸' رفته؛ سپس لینک سایت یا صفحه مورد نظر را به صورت زیر وارد کنید:\n\n`http://php.net`\n\n♻️ بعد از ارسال لینک، چند لحظه منتظر بمانید تا اسکیرین شات برای شما ارسال شود.\n.", $back_key);

}

elseif($text == '📸 اسکیرین شات 📸'){

    sendmessage($from_id, "✅ آدرس سایت یا صفحه مورد نظر را ارسال کنید:\n\n♻️ مثال: `http://php.net`\n.", $back_key);
    step('take');

}

elseif($text == '🗣 درباره ما'){
    
    sendmessage($from_id, "✅ دیمو تیم :\n\n@DimoTM\n\n✅ نویسنده سورس و وبسرویس :\n\n@DevUltra\n\nجهت سفارش پروژه ربات های تلگرامی به پیوی مراجعه کنید!\n.", $back_key);
    
}

elseif($text == '☁️ پروفایل'){
    
    $type = str_replace(['photo', 'document'], ['عکس (کیفیت پایین تر و حجم کمتر)', 'داکیومنت (کیفیت بالاتر و حجم بیشتر)'], $type);
    $size = str_replace(['full', 'limit'], ['تمام صفحه', '1024 * 1024'], $size);
    sendmessage($from_id, "⬅️ نام شما: <code>$first_name</code>\n\n🔗 آیدی عددی : <code>$from_id</code>\n\n📸 سایز عکس ها: <b>$size</b>\n\n📂 نوع خروجی: <b>$type</b>\n.", $back_key, 'HTML');
    
}

elseif($text == 'تنظیمات ⚙️'){
    
    $type = str_replace(['photo', 'document'], ['عکس (کیفیت پایین تر و حجم کمتر)', 'داکیومنت (کیفیت بالاتر و حجم بیشتر)'], $type);
    $size = str_replace(['full', 'limit'], ['تمام صفحه', '1024 * 1024'], $size);
    sendmessage($from_id, "⚙️ تنظیمات زیر را به دلخواه نتخاب کنید تا بلافاصله اعمال شوند:\n\n✅ تنظیمات کنونی: اسکیرین شات ها به صورت <b>$type</b> و در سایز <b>$size</b> ارسال میشوند.\n\n↩️ <b>توجه داشته باشید، برای ارسال عکس های تمام صفحه باید نوع خروجی را بر روی داکیومنت تنظیم کنید تا بتوانید خروجی را دریافت کنید!</b>\n.", $setting_key, 'HTML');
    
}

elseif($data){
    
    if($message_id != $last){
        
        queryanswer($query_id, "🚫 این پیام باطل شده است.\n\n♻️ مجددا به تنظیمات بروید!", true);        
        
    }else{
        
        
        if(in_array($data, ['full', 'limit'])){
            
            if($data == 'full'){
                type('document');
                $type = 'document';
            }
            
            size($data);
            queryanswer($query_id, "✅", false);        
            $type = str_replace(['photo', 'document'], ['عکس (کیفیت پایین تر و حجم کمتر)', 'داکیومنت (کیفیت بالاتر و حجم بیشتر)'], $type);
            $size = str_replace(['full', 'limit'], ['تمام صفحه', '1024 * 1024'], $data);
            editmessage($from_id, $message_id, "⚙️ تنظیمات زیر را به دلخواه نتخاب کنید تا بلافاصله اعمال شوند:\n\n✅ تنظیمات کنونی: اسکیرین شات ها به صورت <b>$type</b> و در سایز <b>$size</b> ارسال میشوند.\n\n↩️ <b>توجه داشته باشید، برای ارسال عکس های تمام صفحه باید نوع خروجی را بر روی داکیومنت تنظیم کنید تا بتوانید خروجی را دریافت کنید!</b>\n.", $setting_key, 'HTML');
            
        }elseif(in_array($data, ['photo', 'document'])){
            
            if($data == 'photo' and $size == 'full'){
                size('limit');
                $size = 'limit';
            }
            
            type($data);
            queryanswer($query_id, "✅", false);        
            $type = str_replace(['photo', 'document'], ['عکس (کیفیت پایین تر و حجم کمتر)', 'داکیومنت (کیفیت بالاتر و حجم بیشتر)'], $data);
            $size = str_replace(['full', 'limit'], ['تمام صفحه', '1024 * 1024'], $size);
            editmessage($from_id, $message_id, "⚙️ تنظیمات زیر را به دلخواه نتخاب کنید تا بلافاصله اعمال شوند:\n\n✅ تنظیمات کنونی: اسکیرین شات ها به صورت <b>$type</b> و در سایز <b>$size</b> ارسال میشوند.\n\n↩️ <b>توجه داشته باشید، برای ارسال عکس های تمام صفحه باید نوع خروجی را بر روی داکیومنت تنظیم کنید تا بتوانید خروجی را دریافت کنید!</b>\n.", $setting_key, 'HTML');
            
        }
        
    }
    
}

elseif($step == 'take'){
    
    if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $text)){
    
        sendmessage($from_id, '♻️♻️♻️');
    
        if($size == 'limit'){
            $end = json_decode(file_get_contents($api . "?url=$text"), true);
        }else{
            $end = json_decode(file_get_contents($api . "?url=$text&full=true"), true);
        }
        
        if($end['ok'] and $end['screenshot']){
            
            editmessage($from_id, $message_id + 1, 'درحال ارسال تصویر...');
            bot('send' . $type, ['chat_id' => $from_id, $type => $end['screenshot'], 'caption' => "✅ اسکیرین شات شما آماده شد!\n\n🔗 لینک: $text\n.", 'reply_markup' => $start_key]);
            editmessage($from_id, $message_id + 1, '✅');
            step('none');
            
        }else{
            
            editmessage($from_id, $message_id + 1, 'خطا در انجام عملیات! لطفا لینک خود را بررسی کنید و مجددا ارسال نمایید:');
            
        }
    
    }else{
        
        editmessage($from_id, $message_id + 1, 'خطا در انجام عملیات! لطفا لینک خود را بررسی کنید و مجددا ارسال نمایید:');
        
    }

}

#-----------------------------#

/*

سورس ربات و وبسرویس اسکیرین شات از سایت ها و صفحالت وب
کانال ما : @DimoTM
نویسنده : @DevUltra
منبع بزن.

*/

?>
