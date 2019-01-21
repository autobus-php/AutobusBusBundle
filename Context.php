<?php

namespace Autobus\Bundle\BusBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Context
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param mixed $message
     *
     * @return Context
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getJsonMessageAsArray()
    {
        $messageAsArray = json_decode($this->getMessage(), true);
        if (!is_array($messageAsArray)) {
            return [];
        }

        return $messageAsArray;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return Context
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Context
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
