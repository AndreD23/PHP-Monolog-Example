<?php

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SendGridHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Formatter\LineFormatter;

require __DIR__ ."/vendor/autoload.php";

// Logger de nível DEBUG ao NOTICE, exibir no browser
$logger = new Logger("web");
$logger->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));
$logger->pushHandler(new StreamHandler(__DIR__."/log.txt", Logger::WARNING));
$logger->pushHandler(new SendGridHandler(SENDGRID['user'], SENDGRID['passwd'], "noreply@imperiosoft.com.br", "contato@imperiosoft.com.br", "Erro na aplicação: ". date("d/m/Y H:i:s"), Logger::CRITICAL));

$logger->pushProcessor(function($record){
   $record['extra']['HTTP_HOST'] = $_SERVER['HTTP_HOST'];
   $record['extra']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
   $record['extra']['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
   $record['extra']['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
   return $record;
});

$telegram_hanlder = new TelegramBotHandler(TELEGRAM_BOT_ALERT['api'], TELEGRAM_BOT_ALERT['channel'], Logger::EMERGENCY);
$telegram_hanlder->setFormatter(new LineFormatter("%level_name%: %message%"));

$logger->pushHandler($telegram_hanlder);

$logger->debug("Olá mundo", ["logger" => true]);
$logger->info("Olá mundo", ["logger" => true]);
$logger->notice("Olá mundo", ["logger" => true]);


// Logger de nível WARNING e ERROR, exibir no arquivo
$logger->warning("Olá mundo", ["logger" => true]);
$logger->error("Olá mundo", ["logger" => true]);

// Logger de nível CRITICAL e ALERT, enviar por e-mail
$logger->critical("Olá mundo", ["logger" => true]);
$logger->alert("Olá mundo", ["logger" => true]);

// Logger de nível EMERGENCY, enviar pelo Telegram
$logger->emergency("Olá mundo", ["logger" => true]);


