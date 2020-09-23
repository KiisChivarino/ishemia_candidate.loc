<?php

namespace App\Services\InfoService;

use App\Entity\BlogRecord;

class BlogRecordInfoService
{
    /**
     * Get blog record title
     *
     * @param BlogRecord $blogRecord
     *
     * @return string
     */
    public function getBlogRecordTitle(BlogRecord $blogRecord): string
    {
        return $blogRecord->getDateBegin()->format('d.m.Y').' - '.$blogRecord->getDateEnd()->format('d.m.Y');
    }
}