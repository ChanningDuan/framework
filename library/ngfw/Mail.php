<?php

/**
 * ngfw
 * ---
 * copyright (c) 2015, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace ngfw;

/**
 * Mail
 * @todo CLASS in not stable 
 * @package ngfw
 * @subpackage library
 * @version 1.2.0
 * @copyright (c) 2015, Nick Gejadze
 */
class Mail {
    
    /**
     * newline     
     */
    const newline = "\r\n";

    /**
     * $isSMTP
     * @var bool
     */
    protected $isSMTP = false;

    /**
     * $isHTML
     * @var bool
     */
    protected $isHTML = false;

    /**
     * $server
     * @var string
     */
    protected $server = "127.0.0.1";

    /**
     * $port
     * @var int
     */
    protected $port = 25;

    /**
     * $smtp
     * @var resource
     */
    protected $smtp;

    /**
     * $username
     * @var string
     */
    protected $username;

    /**
     * $password
     * @var string
     */
    protected $password;

    /**
     * $connectTimeout
     * @var int
     */
    protected $connectTimeout = 30;

    /**
     * $responseTimeout
     * @var type 
     */
    protected $responseTimeout = 8;

    /**
     * $headers
     * @var array
     */
    protected $headers;

    /**
     * $from 
     * @var array
     */
    protected $from = array();

    /**
     * $to
     * @var array
     */
    protected $to = array();

    /**
     * $cc
     * @var array
     */
    protected $cc = array();

    /**
     * $replyTo
     * @var array
     */
    protected $replyTo;

    /**
     * $subject
     * @var string
     */
    protected $subject;

    /**
     * $body
     * @var type 
     */
    protected $body;

    /**
     * $html
     * @var string
     */
    protected $html;

    /**
     * $text
     * @var string
     */
    protected $text;

    /**
     * $boundary
     * @var string
     */
    protected $boundary;

    /**
     * $debug
     * @var bool
     */
    protected $debug = false;

    /**
     * $charset
     * @var string
     */
    protected $charset = "UTF-8";

    /**
     * __construct()
     * Sets Default Headers
     * @return void
     */
    public function __construct() {
        $this->headers['MIME-Version'] = "1.0";
        $this->headers['X-Engine'] = "ngfw";
        $this->setContentType();
    }

    /**
     * setCharset()
     * Sets Charset, Default Value = UTF-8
     * @param string $charset
     * @return \ngfw\Mail
     */
    public function setCharset($charset) {
        if (isset($charset)):
            $this->charset = $charset;
        endif;
        $this->setContentType();
        return $this;
    }

    /**
     * isSMTP()
     * Indicates if Email should go out through SMTP or regular MAIL function
     * @param bool $boolean
     * @return \ngfw\Mail
     */
    public function isSMTP($boolean) {
        if (is_bool($boolean)):
            $this->isSMTP = $boolean;
        endif;
        return $this;
    }

    /**
     * isHtml()
     * Indicates if Email is HTML format, Default Email is set to text
     * @param bool $boolean
     * @return \ngfw\Mail
     */
    public function isHtml($boolean) {
        if (is_bool($boolean)):
            $this->isHTML = $boolean;
            $this->setContentType();
        endif;
        return $this;
    }

    /**
     * setUsername()
     * Sets username for SMTP connection
     * @param string $username
     * @return \ngfw\Mail
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * setPassword()
     * Sets password for SMTP connection
     * @param string $password
     * @return \ngfw\Mail
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * setServer()
     * Sets SMTP server hostname
     * @param string $server
     * @return \ngfw\Mail
     */
    public function setServer($server) {
        $this->server = $server;
        return $this;
    }

    /**
     * setPort()
     * Sets SMTP port
     * @param int $port
     * @return \ngfw\Mail
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    /**
     * setHeader()
     * Sets HTML header, if Header with same key was already set, it will be overwritten.
     * @param string $key
     * @param string $value
     * @return \ngfw\Mail
     */
    public function setHeader($key, $value) {
        $this->header[$key] = $value;
        return $this;
    }

    /**
     * setTo()
     * Sets TO address, name not required
     * @param string $address
     * @param string $name
     * @return \ngfw\Mail
     */
    public function setTo($address, $name = "") {
        $this->to[] = array($address, $name);
        return $this;
    }

    /**
     * setCc()
     * Sets CC address, name not required
     * @param string $address
     * @param string $name
     * @return \ngfw\Mail
     */
    public function setCc($address, $name = "") {
        $this->cc[] = array($address, $name);
        return $this;
    }

    /**
     * setFrom()
     * Sets FROM address, name not required
     * @param string $address
     * @param string $name
     * @return \ngfw\Mail
     */
    public function setFrom($address, $name = "") {
        $this->from = array($address, $name);
        return $this;
    }

    /**
     * setReplyTo()
     * Sets ReplyTo, Name not required
     * @param type $address
     * @param type $name
     * @return \ngfw\Mail
     */
    public function setReplyTo($address, $name = "") {
        $this->replyTo = array($address, $name);
        return $this;
    }

    /**
     * Sets Email subject
     * @param string $subject
     * @return \ngfw\Mail
     */
    public function setSubject($subject) {
        if (isset($subject)):
            $this->subject = $subject;
        endif;
        return $this;
    }

    /**
     * setHtml()
     * Sets Email HTML Body
     * @param string $html
     * @return \ngfw\Mail
     */
    public function setHtml($html) {
        if (isset($html)):
            $this->html = $html;
        endif;
        return $this;
    }

    /**
     * setText()
     * Sets Email TEXT body
     * @param string $text
     * @return \ngfw\Mail
     */
    public function setText($text) {
        if (isset($text)):
            $this->text = $text;
        endif;
        return $this;
    }

    /**
     * setContentType()
     * Sets Email Content Type Header
     * @return void
     */
    private function setContentType() {
        if ($this->isHTML):
            $this->boundary = md5(date('U'));
            $this->headers['Content-type'] = "multipart/alternative; boundary=$this->boundary";
        else:
            $this->headers['Content-type'] = "text/plain; charset=`" . $this->charset . "`";
        endif;
    }

    /**
     * smtpCmd()
     * Sends SMTP Command and Returns Response
     * @see getResponse()
     * @param stirng $command
     * @return string
     */
    private function smtpCmd($command) {
        fputs($this->smtp, $command . self::newline);
        return $this->getResponse();
    }

    /**
     * getResponse()
     * Gets response from SMTP Socket and returns as String
     * @return string
     */
    private function getResponse() {
        $response = '';
        while (($line = fgets($this->smtp, 515)) != false):
            $response .= trim($line) . "\n";
            if (substr($line, 3, 1) == ' '):
                break;
            endif;
        endwhile;
        return trim($response);
    }

    /**
     * formatAddress()
     * Formats email Addess and returns as string
     * @param string $address
     * @return string
     */
    private function formatAddress($address) {
        if (isset($address[0]) and !isset($address[1])):
            return $address[0];
        elseif (isset($address[0]) and isset($address[1])):
            return '"' . $address[1] . '" <' . $address[0] . ">";
        endif;
    }

    /**
     * formatAddressArray()
     * Formats emails array as a string
     * @param array $address
     * @return string
     */
    private function formatAddressArray($address) {
        foreach ($address as $key => $addr) :
            $address[$key] = $this->formatAddress($addr);
        endforeach;
        return implode(', ' . self::newline . "\t", $address);
    }

    /**
     * comileBody()
     * Builds email body
     * @return string
     */
    private function compileBody() {
        if ($this->isHTML):
            return "--" . $this->boundary . self::newline .
                    "Content-Type: text/plain; charset=" . $this->charset . "" . self::newline .
                    "Content-Transfer-Encoding: base64" . self::newline . self::newline .
                    base64_encode($this->text) . self::newline .
                    "--" . $this->boundary . self::newline .
                    "Content-Type: text/html; charset=" . $this->charset . "" . self::newline .
                    "Content-Transfer-Encoding: base64" . self::newline . self::newline .
                    base64_encode($this->html) . self::newline .
                    "--" . $this->boundary . "--";
        else:
            return $this->text;
        endif;
    }

    /**
     * send()
     * Builds Email body and sends via SMTP or Mail
     * @return bool
     */
    public function send() {
        $this->body = $this->compileBody();
        if ($this->isSMTP):
            return $this->sendViaSmtp();
        else:
            return $this->sendViaMail();
        endif;
    }

    /**
     * sendViaMail()
     * Send Email via MAIL function
     * @return bool
     */
    private function sendViaMail() {
        $this->headers['From'] = $this->formatAddress($this->from);

        $this->headers['To'] = $this->formatAddressArray($this->to);
        $this->headers['Reply-To'] = $this->formatAddress($this->replyTo);
        if (!empty($this->cc)):
            $this->headers['Cc'] = $this->formatAddressArray($this->cc);
        endif;
        $this->headers['Subject'] = $this->subject;
        $this->headers['Date'] = date('r');
        $headers = '';
        foreach ($this->headers as $key => $val):
            if ($key != "To"):
                $headers .= $key . ': ' . $val . self::newline;
            endif;
        endforeach;
        return mail($this->headers['To'], $this->subject, $this->body, $headers);
    }

    /**
     * sendViaSmtp()
     * Send Email via SMTP server
     * @return boolean
     */
    private function sendViaSmtp() {
        $this->smtp = fsockopen($this->server, $this->port, $errno, $errstr, $this->connectTimeout);
        if (empty($this->smtp)):
            return false;
        endif;
        $this->setStreamTimeout();
        $this->getResponse();
        $this->smtpCmd("EHLO {$this->localhost}");
        if (isset($this->username) and isset($this->password)):
            $this->smtpCmd("AUTH LOGIN");
            $this->smtpCmd(base64_encode($this->username));
            $this->smtpCmd(base64_encode($this->password));
        endif;
        $this->smtpCmd("MAIL FROM:<" . $this->from[0] . ">");
        foreach (array_merge($this->to, $this->cc) as $address):
            $this->smtpCmd("RCPT TO:<" . $address[0] . ">");
        endforeach;
        $this->smtpCmd("DATA");
        $this->headers['From'] = $this->formatAddress($this->from);
        $this->headers['To'] = $this->formatAddressArray($this->to);
        $this->headers['Reply-To'] = $this->formatAddress($this->replyTo);
        if (!empty($this->cc)):
            $this->headers['Cc'] = $this->formatAddressArray($this->cc);
        endif;
        $this->headers['Subject'] = $this->subject;
        $this->headers['Date'] = date('r');
        $headers = '';
        foreach ($this->headers as $key => $val):
            $headers .= $key . ': ' . $val . self::newline;
        endforeach;
        $result = $this->smtpCmd($headers . self::newline . $this->body . self::newline);
        $this->smtpCmd("QUIT");
        fclose($this->smtp);
        return substr($result, 0, 3) == "250";
    }

    /**
     * setStreamTimeout()
     * Sets stream_set_time with $resposeTimeout
     * @return void
     */
    private function setStreamTimeout() {
        stream_set_timeout($this->smtp, $this->responseTimeout);
    }

}
