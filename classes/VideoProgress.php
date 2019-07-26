<?php
class VideoProgress
{
    private $mySQL;
    private $progress;
    private $user;
    private $video;

    public function __construct(MySQL $mySQL, Video $video, User $user)
    {
        $this->mySQL = $mySQL;
        $this->user = $user;
        $this->video = $video;
        $result = $this->mySQL->execute("SELECT * FROM video_progress WHERE video=? AND user=?", "ss", $this->video->getId(), $this->user->getUser());
        if ($result->num_rows === 0) {
            $this->mySQL->execute("INSERT INTO video_progress(video, user, progress) VALUES (?, ?, 0)", "ss", $this->video->getId(), $this->user->getUser());
            $this->progress = 0;
        } else {
            $this->progress = $result->fetch_object()->progress;
        }
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function setProgress(float $progress)
    {
        $this->progress = $progress;
        $this->mySQL->execute("UPDATE video_progress SET progress=? WHERE video=? AND user=?", "dss", $this->progress, $this->video->getId(), $this->user->getUser());
    }
}
