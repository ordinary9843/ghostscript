<?php

namespace Ordinary9843\Constants;

class ToImageConstant
{
    /** @var string */
    const COMMAND = '%s -dQUIET -dNOPAUSE -dBATCH -sDEVICE=%s -r300 -sOutputFile=%s %s';

    /** @var string */
    const TYPE_JPEG = 'jpeg';

    /** @var string */
    const TYPE_PNG = 'png';
}
