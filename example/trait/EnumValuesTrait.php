<?php
use Jgauthi\Component\Traits\EnumValuesTrait;

enum SaleItemTypeEnum: string
{
    case SUBSCRIPTION = 'ABO';
    case SUPPLEMENT = 'SUP';
    case TICKET = 'ENT';

    use EnumValuesTrait;
}

var_dump(SaleItemTypeEnum::list());