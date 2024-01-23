<?php

namespace Ordinary9843\Constants;

class GhostscriptConstant
{
    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var float */
    const STABLE_VERSION = 1.4;

    /** @var string */
    const TOTAL_PAGE_COMMAND = '%s -dQUIET -dNODISPLAY -dNOSAFER -c "(%s) (r) file runpdfbegin pdfpagecount = quit"';
}
