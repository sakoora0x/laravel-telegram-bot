<?php

namespace sakoora0x\Telegram\Entity;

use DefStudio\Telegraph\DTO\Message;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use sakoora0x\Telegram\Enums\Direction;
use sakoora0x\Telegram\Foundation\BaseEntity;

class ChatMessage extends BaseEntity
{
    protected string $direction;
    protected ?string $text = null;
    protected ?array $media;
    protected ?array $menu = null, $keyboard = null, $message = null;

    public function direction(): Direction
    {
        return Direction::from($this->direction);
    }

    public function message(): ?Message
    {
        return $this->message ? Message::fromArray($this->message) : null;
    }

    public function menu(): ?ReplyKeyboard
    {
        TelegraphChat::first()->video('', '')
        return $this->menu ? ReplyKeyboard::fromArray($this->menu) : null;
    }

    public function keyboard(): ?Keyboard
    {
        return $this->keyboard ? Keyboard::fromArray($this->keyboard) : null;
    }
}
