<?php

namespace RedStor\SDK\Types;

trait SerializableType
{
    public function jsonSerialize()
    {
        return [
            'isPrimaryKey' => $this->isPrimaryKey(),
            'isAutoIncrement' => $this->isAutoIncrement(),
            'sqlType' => $this->getSqlType(),
            'sqlLength' => $this->getSqlLength(),
            'solrType' => $this->getSolrType(),
        ];
    }
}
