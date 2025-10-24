<?php

namespace sakoora0x\Telegram\Interfaces;

interface HasCaption
{
    public function caption(): ?string;
    public function captionEntities(): ?array;
    public function setCaption(?string $caption): static;
    public function captionSignature(): ?string;
}
