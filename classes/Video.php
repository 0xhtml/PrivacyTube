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

    /**
     * Video constructor
     * @param string $id Video id
     * @param string $title Video title
     * @param string $description Video description
     * @param Channel $channel Channel who uploaded the video
     * @param int $date Date the video was uploaded
     * @param int $views Views of the video
     * @param int $likes Likes of the video
     * @param int $dislikes Dislikes of the video
     * @param string $thumbnail Video thumbnail url
     */
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

    /**
     * Get the video id
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the video title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the video decription
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the channel who uploaded the video
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * Get the date the video was uploaded
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Get the views of the video
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * Get the likes of the video
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * Get the dislikes of the video
     * @return int
     */
    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    /**
     * Get the video thumbnail url
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
