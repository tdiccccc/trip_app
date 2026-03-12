<?php

declare(strict_types=1);

namespace Packages\Domain\Exceptions;

class CategoryInUseException extends DomainException
{
    public function __construct(string $message = 'このカテゴリは使用中のため削除できません。')
    {
        parent::__construct($message);
    }
}
