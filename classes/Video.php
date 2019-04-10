<?php

class Video
{
    private $id;
    private $title;
    private $description;
    private $channelId;
    private $date;
    private $views;
    private $likes;
    private $dislikes;

    public function __construct(string $id, string $title, string $description, string $channelId, int $date, int $views, int $likes, int $dislikes)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->channelId = $channelId;
        $this->date = $date;
        $this->views = $views;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }
}
