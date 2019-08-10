<?php

namespace KBox;

class GetMaximumUploadSize
{
    public function __invoke()
    {
        $config = intval(config('dms.max_upload_size')) ?? 0;

        return $config > 0 ? $config : 0;
    }
}
