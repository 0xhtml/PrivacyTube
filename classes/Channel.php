<?php

class Channel
{
    private $id;
    private $name;
    private $image;
    private $subscribers;

    private $mySQL;

    public function __construct(MySQL $mySQL, string $id, string $name, string $image, int $subscribers)
    {
        $this->mySQL = $mySQL;
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->subscribers = $subscribers;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getSubscribers(): int
    {
        return $this->subscribers;
    }

    public function getPrivateSubscribers(): int
    {
        return $this->mySQL->execute("SELECT * FROM subscriptions WHERE channel = ?", "s", $this->id)->num_rows;
    }

    public function subscribe(User $user, string $channel)
    {
        if ($this->is_subscribed($user)) {
            return;
        }

        $user = $user->getUser();
        $this->mySQL->execute("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $user, $channel);
    }

    public function is_subscribed(User $user)
    {
        $user = $user->getUser();
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $this->id);
        if ($result->num_rows === 0) {
            return false;
        } else {
            return true;
        }
    }

    public function unsubscribe(User $user, string $channel)
    {
        $user = $user->getUser();
        $this->mySQL->execute("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $channel);
    }
}
