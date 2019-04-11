<?php

class Video
{
    private $id;
    private $title;
    private $description;
    private $channel;
    private $date;
    private $views;
    private $likes;
    private $dislikes;
    private $thumbnail;

    public function __construct(string $id, string $title, string $description, Channel $channel, int $date, int $views, int $likes, int $dislikes, string $thumbnail)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->channel = $channel;
        $this->date = $date;
        $this->views = $views;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->thumbnail = $thumbnail;
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

    public function getChannel(): Channel
    {
        return $this->channel;
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

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
