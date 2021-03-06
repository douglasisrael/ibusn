<?php

namespace SocialNetwork\API\Response;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResponse
 * @package SocialNetwork\API\Response
 */
class ApiResponse{

    protected $response;
    protected $code;
    protected $data;
    protected $error;
    protected $headers = array();

    /**
     * @param $data Array of data
     * @param $code Int code of http
     * @param $error Boolean hasError
     */
    function __construct(array $data = array(), $code = 200, $error = false )
    {
        $this->setData( $data, $code, $error );
    }


    /**
     * Set de parameters of response
     *
     * @param array $data
     * @param int $code
     * @param bool $error
     * @return $this
     * @throws \Exception
     */
    public function setData(array $data = array(), $code = 200, $error = false )
    {
        $this->data = $data;
        $this->code = $code;
        $this->error = $error;

        if( ($this->code < 300 && $error) || ($this->code > 300 && !$error) )
        {
            throw new \Exception('Error format response.');
        }

        return $this;
    }

    /**
     * Set Header parameters
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader( $key , $value )
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Render the Response and parse the json
     *
     * @return Response
     */
    public function render()
    {
        $this->response = new Response();
        $this->response->setContent( @json_encode( $this->data ) );
        $this->response->setStatusCode( $this->code );

        foreach( $this->headers as $key => $val )
            $this->response->headers->set( $key, $val );

        $this->response->headers->set('Content-Type', 'application/json');

        return $this->response;
    }
}