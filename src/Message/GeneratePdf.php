<?php

namespace App\Message;

class GeneratePdf
{
    public function __construct(
        private string $content,
		private string $filename,
		private string $filepath,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

	public function getFilename(): string
    {
        return $this->filename;
    }

	public function getFilepath(): string
    {
        return $this->filepath;
    }
	public function getImageSource(): string
    {
        return $this->filepath.$this->filename;
    }
}