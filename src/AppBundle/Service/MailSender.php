<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 09/10/2018
 * Time: 11:03
 */

namespace AppBundle\Service;

use Swift_Mailer;
use Twig\Environment;

class MailSender
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var \Swift_Message
     */
    private $message;
    /**
     * @var mixed
     */
    private $mailBody = '';

    /**
     * MailSender constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->message = new \Swift_Message();
    }

    /**
     * @param $emailTo
     * @param $emailFrom
     * @param $subject
     * @return mixed|null|string|string[]
     */
    public function sendMail($emailTo, $emailFrom, $subject)
    {
        if ($this->isHtml($this->mailBody)) {
            $textContent = DataConverter::stripHTML($this->mailBody);
        }else{
            $textContent = $this->mailBody;
        }

        $message = $this->message->setContentType('text/html')
            ->setSubject($subject)
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody($this->mailBody)
            ->addPart($textContent, 'text/plain');

        $this->mailer->send($message);
        return true;
    }

    /**
     * @param $string
     * @return bool
     */
    private function isHtml($string)
    {
        if ( $string != strip_tags($string) )
        {
            return true; // Contains HTML
        }
        return false; // Does not contain HTML
    }

    /**
     * Method lets use raw data as well as generated data using Twig template rendering
     *
     * @param $templatePath
     * @param mixed $data
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function setMailBody($data, $templatePath = null)
    {
        if (!is_null($templatePath) && is_array($data)) {
            $this->mailBody = $this->twig->render($templatePath, $data);
        }else{
            $this->mailBody = $data;
        }
        return $this->mailBody;
    }

    /**
     * @param $path
     * @return string
     */
    public function addEmbedImage($path)
    {
        return $this->message->embed(\Swift_Image::fromPath($path));
    }

    /**
     * @return mixed
     */
    public function getMailBody()
    {
        return $this->mailBody;
    }

}

