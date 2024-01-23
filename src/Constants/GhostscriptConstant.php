<?php

namespace Ordinary9843\Constants;

class GhostscriptConstant
{
    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string */
    const CONVERT_COMMAND = '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dCompatibilityLevel=%s -sOutputFile=%s %s';

    /** @var string */
    const MERGE_COMMAND = '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -sOUTPUTFILE=%s %s';

    /** @var string */
    const SPLIT_COMMAND = '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dFirstPage=%d -dLastPage=%d -sOUTPUTFILE=%s %s';

    /** @var string */
    const TOTAL_PAGE_COMMAND = '%s -dQUIET -dNODISPLAY -dNOSAFER -c "(%s) (r) file runpdfbegin pdfpagecount = quit"';

    /** @var string */
    const TO_IMAGE_COMMAND = '%s -dQUIET -dNOPAUSE -dBATCH -sDEVICE=%s -r300 -sOutputFile=%s %s';

    /** @var string */
    const SPLIT_FILENAME = '/part_%d.pdf';

    /** @var string */
    const TO_IMAGE_FILENAME = '/image_%d.';

    /** @var float */
    const STABLE_VERSION = 1.4;

    /** @var string */
    const TO_IMAGE_TYPE_JPEG = 'jpeg';

    /** @var string */
    const TO_IMAGE_TYPE_PNG = 'png';
}
