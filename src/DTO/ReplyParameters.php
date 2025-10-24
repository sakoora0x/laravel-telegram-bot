<?php

namespace sakoora0x\Telegram\DTO;

use sakoora0x\Telegram\Abstract\DTO;

class ReplyParameters extends DTO
{
    protected function required(): array
    {
        return ['message_id'];
    }

    public function messageId(): int
    {
        return (int)$this->getOrFail('message_id');
    }

    public function chatId(): ?string
    {
        return $this->get('chat_id');
    }
}
