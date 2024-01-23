<?php

namespace Ordinary9843\Constants;

class SplitConstant
{
    /** @var string */
    const COMMAND = '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dFirstPage=%d -dLastPage=%d -sOUTPUTFILE=%s %s';
}
