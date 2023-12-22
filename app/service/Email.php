<?php

namespace app\service;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $mailer;

    public function __construct(int $debug = 0, bool $showException = null)
    {
        $this->mailer            = new PHPMailer($showException);
        $this->mailer->SMTPDebug = $debug;
        switch (getenv('MAIL_DRIVER')) {
            case 'smtp':
                $this->mailer->isSMTP();
                break;
            case 'sendmail':
                $this->mailer->isSendmail();
                break;
            case 'qmail':
                $this->mailer->isQmail();
                break;
            case 'mail':
                $this->mailer->isMail();
                break;
            default:
                throw new \Exception('不支持的邮件驱动');
        }
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->Host       = getenv('MAIL_HOST');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = getenv('MAIL_USERNAME');
        $this->mailer->Password   = getenv('MAIL_PASSWORD');
        $this->mailer->SMTPSecure = getenv('MAIL_ENCRYPTION');
        $this->mailer->Port       = getenv('MAIL_PORT');
        $this->mailer->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
    }

    public function address(string $address, string $name = '')
    {
        #验证邮箱
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('邮箱格式不正确');
        }
        $this->mailer->addAddress($address, $name);
        return $this;
    }

    public function batchAddress(array $addresses)
    {
        foreach ($addresses as $address) {
            $this->address($address);
        }
        return $this;
    }

    public function attachment($file, $name = '')
    {
        $this->mailer->addAttachment($file, $name);
        return $this;
    }

    public function batchAttachment(array $files = [])
    {
        foreach ($files as $file) {
            $this->attachment($file);
        }
        return $this;
    }

    public function subject(string $subject)
    {
        $this->mailer->Subject = $subject;
    }

    public function body(string $body)
    {
        $this->mailer->Body = $body;
    }

    public function altBody(string $altBody)
    {
        $this->mailer->AltBody = $altBody;
    }


    public function send(bool $isHtml = true)
    {
        $this->mailer->isHTML($isHtml);
        try {
            return $this->mailer->send();
        } catch (\Exception $e) {
            throw new \Exception($this->mailer->ErrorInfo);
        }
    }


}
