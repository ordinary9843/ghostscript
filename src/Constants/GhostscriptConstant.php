<?php

namespace Ordinary9843\Constants;

class GhostscriptConstant
{
    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string */
    const CONVERT_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -dCompatibilityLevel=%s -sOutputFile=%s %s';

    /** @var string */
    const MERGE_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOUTPUTFILE=%s %s';

    /** @var string */
    const SPLIT_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -dFirstPage=%d -dLastPage=%d -sOUTPUTFILE=%s %s';

    /** @var string */
    const TOTAL_PAGE_COMMAND = '%s -dQUIET -dNODISPLAY -dNOSAFER -c "(%s) (r) file runpdfbegin pdfpagecount = quit"';

    /** @var string */
    const SPLIT_FILENAME = '/part_%d.pdf';

    /** @var float */
    const STABLE_VERSION = 1.4;
}
