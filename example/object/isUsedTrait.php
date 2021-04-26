<?php
use Jgauthi\Component\Utils\Object;

// In this example, the vendor folder is located in "example/"
require_once __DIR__.'/../vendor/autoload.php';

trait StatusActiveTrait
{
    private bool $active = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}

trait StatusOnlineTrait {}

class User
{
    use StatusActiveTrait;

    private string $fullname;

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }
}

$john = (new User)
    ->setFullname('John Doe')
    ->setActive(true)
;

var_dump(
    Objects::isUsedTrait(StatusActiveTrait::class, $john), // true
    Objects::isUsedTrait(StatusActiveTrait::class, User::class), // true
    Objects::isUsedTrait(StatusOnlineTrait::class, $john) // false
);