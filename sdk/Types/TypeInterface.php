<?php
namespace RedStor\SDK\Types;

interface TypeInterface
    extends \JsonSerializable
{
    public function isPrimaryKey() : bool;
    public function isAutoIncrement() : bool;

    public function getSqlType() : string;
    public function getSqlLength() : int;

    public function getSolrType() : string;
}