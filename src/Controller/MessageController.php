<?php
namespace App\Websocket;
    
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class MessageHandler implements MessageComponentInterface
{
    
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        //$this->connections->attach($conn);

        // first, you are sending to all existing users message of 'new'
        foreach ($this->connections as $client) {
            $client->send('<status>' . $conn->remoteAddress . ' Online</status>'); //here we are sending a status-message
        }
        // than,
        // Store the new connection to send messages to later
        $this->connections->attach($conn);

        echo "New connection! ({$conn->remoteAddress})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach($this->connections as $connection)
        {
            if($connection === $from)
            {
                continue;
            }
            var_dump($msg);
            $connection->send($msg);
        }
    }
    
    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }
    
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}