<?php
/**
 * @author Alexander.Lisachenko
 * @date 21.07.2014
 */

namespace Protocol\FCGI;

class Server
{
    protected static $id = 0;

    protected $connections = array();

    protected $buffers = array();

    protected $socketAddress;
    protected $serverSocket;
    protected $isBlocking;

    public function __construct($socketAddress, $isBlocking = false)
    {
        $this->socketAddress = $socketAddress;
        $this->isBlocking = $isBlocking;
    }

    public function run()
    {
        $this->serverSocket = stream_socket_server(
            $this->socketAddress,
            $errorNumber,
            $errorString,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
        );
        if ($errorString) {
            throw new \RuntimeException($errorString, $errorNumber);
        }

        socket_set_blocking($this->serverSocket, $this->isBlocking);

        $eventBase   = event_base_new();
        $socketEvent = event_new();

        event_set($socketEvent, $this->serverSocket, EV_READ | EV_PERSIST, array($this, 'onAccept'), $eventBase);
        event_base_set($socketEvent, $eventBase);
        event_add($socketEvent);
        event_base_loop($eventBase);

        stream_socket_shutdown($this->serverSocket, STREAM_SHUT_RDWR);
    }

    public function onAccept($socket, $flag, $base)
    {
        $id = static::$id++;

        $connection = stream_socket_accept($socket);
        stream_set_blocking($connection, false);

        $conn = new Connection($connection, $base, $id);
        $conn->handle();
    }
}
