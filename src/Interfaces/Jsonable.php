<?php

namespace NspTeam\Component\Tools\Interfaces;

interface Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string;
}