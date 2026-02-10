<?php

namespace Ae3\LaravelLogsLayer\app\Loggers;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Level;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;

class EmailLogger extends AbstractLogger
{
    /**
     * @param array $config
     * @return void
     * @throws MissingConfigurationException
     */
    public function validateConfig(array $config): void
    {
        $requiredKeys = ['host', 'port', 'email', 'password', 'encryption', 'subject', 'from', 'to'];

        foreach (array_merge($requiredKeys, $this->requiredKeys) as $key) {
            if (!array_key_exists($key, $config)) {
                throw new MissingConfigurationException("Missing configuration key: $key in email channel");
            }
        }
    }

    /**
     * @param array $config
     * @return Logger
     */
    public function createLogger(array $config): Logger
    {
        $handler = new SwiftMailerHandler(
            $this->getMailer($config),
            $this->getMailerMessage($config),
            $config['level'] ?? Level::Debug,
            $config['bubble'] ?? true
        );

        $handler->setFormatter(new HtmlFormatter());

        $logger = new Logger('email');
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param array $config
     * @return Swift_Mailer
     */
    private function getMailer(array $config): Swift_Mailer
    {
        $transport = new \Swift_SmtpTransport($config['host'], $config['port']);
        $transport->setUsername($config['email']);
        $transport->setPassword($config['password']);
        $transport->setEncryption($config['encryption']);

        return new Swift_Mailer($transport);
    }

    /**
     * @param array $config
     * @return Swift_Message
     */
    private function getMailerMessage(array $config): Swift_Message
    {
        $message = new Swift_Message();
        $message->setSubject($config['subject']);
        $message->setFrom($config['from']);
        $message->setTo($config['to']);
        $message->setContentType('text/html');

        return $message;
    }
}